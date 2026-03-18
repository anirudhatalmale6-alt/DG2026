<?php

namespace Modules\CIMSAppointments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\CIMSAppointments\Models\Appointment;
use Modules\CIMSAppointments\Models\AppointmentService;
use Modules\CIMSAppointments\Models\AppointmentStaff;
use Modules\CIMSAppointments\Services\SlotService;
use Modules\CIMSAppointments\Services\ClientSyncService;
use Modules\CIMSAppointments\Services\AppointmentEmailService;

class AppointmentController extends Controller
{
    protected SlotService $slotService;
    protected ClientSyncService $clientSyncService;
    protected AppointmentEmailService $emailService;

    public function __construct(SlotService $slotService, ClientSyncService $clientSyncService, AppointmentEmailService $emailService)
    {
        $this->slotService = $slotService;
        $this->clientSyncService = $clientSyncService;
        $this->emailService = $emailService;
    }

    /**
     * List all appointments with filters.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['staff', 'service']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('staff_id')) {
            $query->forStaff($request->staff_id);
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        if ($request->filled('date_from')) {
            $query->where('appointment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('appointment_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('client_code', 'LIKE', '%' . $search . '%')
                  ->orWhere('client_email', 'LIKE', '%' . $search . '%');
            });
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        $staffList = AppointmentStaff::getActive();
        $serviceList = AppointmentService::getActive();

        return view('cims_appointments::appointments.index', compact('appointments', 'staffList', 'serviceList'));
    }

    /**
     * Calendar view.
     */
    public function calendar()
    {
        $staffList = AppointmentStaff::getActive();
        return view('cims_appointments::appointments.calendar', compact('staffList'));
    }

    /**
     * Show create appointment form.
     */
    public function create(Request $request)
    {
        $services = AppointmentService::getActive();
        $staffList = AppointmentStaff::getActive();

        // Pre-selected client from query string
        $selectedClientId = $request->input('client_id');
        $selectedClient = null;
        if ($selectedClientId) {
            $selectedClient = DB::table('client_master')
                ->where('client_id', $selectedClientId)
                ->whereNull('deleted_at')
                ->first();
        }

        return view('cims_appointments::appointments.create', compact('services', 'staffList', 'selectedClient'));
    }

    /**
     * Store a new appointment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_source' => 'required|in:existing,new',
            // Existing client
            'client_id' => 'required_if:client_source,existing|nullable|integer',
            // New client
            'new_client_name' => 'required_if:client_source,new|nullable|string|max:255',
            'new_client_email' => 'nullable|email|max:255',
            'new_client_phone' => 'nullable|string|max:30',
            // Appointment details
            'service_id' => 'required|integer|exists:cims_appointments_services,id',
            'staff_id' => 'required|integer|exists:cims_appointments_staff,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|integer|min:1|max:8',
            'notes' => 'nullable|string|max:2000',
            'internal_notes' => 'nullable|string|max:2000',
        ]);

        // Validate slot availability
        $slotCheck = $this->slotService->validateBooking(
            $validated['staff_id'],
            $validated['appointment_date'],
            $validated['start_time'],
            $validated['duration_hours']
        );

        if (!$slotCheck['valid']) {
            return back()->withInput()->with('error', $slotCheck['message']);
        }

        // Handle client
        $clientId = null;
        $clientCode = null;
        $clientName = null;
        $clientEmail = null;
        $clientPhone = null;

        if ($validated['client_source'] === 'existing') {
            $client = DB::table('client_master')
                ->where('client_id', $validated['client_id'])
                ->whereNull('deleted_at')
                ->first();

            if (!$client) {
                return back()->withInput()->with('error', 'Selected client not found.');
            }

            $clientId = $client->client_id;
            $clientCode = $client->client_code;
            $clientName = $client->company_name;
            $clientEmail = $client->email;
            $clientPhone = $client->phone_mobile ?? $client->phone_business;

            // Bidirectional sync check: ensure client exists in Grow CRM too
            $syncStatus = $this->clientSyncService->checkClientMasterSync($client->client_code);
            if (!$syncStatus['synced']) {
                // Silently create in Grow CRM
                $this->clientSyncService->createGrowCrmClient($client);
            }
        } else {
            // Create new lead in both systems
            $leadData = [
                'client_name' => $validated['new_client_name'],
                'company_name' => $validated['new_client_name'],
                'email' => $validated['new_client_email'] ?? null,
                'phone' => $validated['new_client_phone'] ?? null,
                'created_by' => Auth::id(),
            ];

            $result = $this->clientSyncService->createNewLead($leadData);

            if (!$result['client_id']) {
                return back()->withInput()->with('error', 'Failed to create new client record.');
            }

            $clientId = $result['client_id'];
            $clientCode = $result['client_code'];
            $clientName = $validated['new_client_name'];
            $clientEmail = $validated['new_client_email'] ?? null;
            $clientPhone = $validated['new_client_phone'] ?? null;
        }

        // Calculate end time and amount
        $startTime = $validated['start_time'];
        $endTime = date('H:i', strtotime('+' . $validated['duration_hours'] . ' hours', strtotime($startTime)));

        $service = AppointmentService::find($validated['service_id']);
        $isChargeable = $service ? $service->is_chargeable : false;
        $amount = $isChargeable ? $service->calculatePrice($validated['duration_hours']) : 0;

        // Create appointment
        $appointment = Appointment::create([
            'client_id' => $clientId,
            'client_code' => $clientCode,
            'client_name' => $clientName,
            'client_email' => $clientEmail,
            'client_phone' => $clientPhone,
            'staff_id' => $validated['staff_id'],
            'service_id' => $validated['service_id'],
            'appointment_date' => $validated['appointment_date'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_hours' => $validated['duration_hours'],
            'status' => Appointment::STATUS_PENDING,
            'notes' => $validated['notes'] ?? null,
            'internal_notes' => $validated['internal_notes'] ?? null,
            'is_chargeable' => $isChargeable,
            'amount' => $amount,
            'payment_status' => Appointment::PAYMENT_UNPAID,
            'created_by' => Auth::id(),
        ]);

        // Load relationships for email
        $appointment->load(['staff', 'service']);

        // Send confirmation email
        $this->emailService->sendConfirmation($appointment);

        return redirect()->route('cimsappointments.appointments.index')
            ->with('success', 'Appointment booked successfully.' . ($clientEmail ? ' Confirmation email sent to ' . $clientEmail . '.' : ''));
    }

    /**
     * Show appointment details.
     */
    public function show($id)
    {
        $appointment = Appointment::with(['staff', 'service', 'creator', 'updater'])->findOrFail($id);
        return view('cims_appointments::appointments.show', compact('appointment'));
    }

    /**
     * Edit appointment.
     */
    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);
        $services = AppointmentService::getActive();
        $staffList = AppointmentStaff::getActive();
        return view('cims_appointments::appointments.edit', compact('appointment', 'services', 'staffList'));
    }

    /**
     * Update appointment.
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'service_id' => 'required|integer|exists:cims_appointments_services,id',
            'staff_id' => 'required|integer|exists:cims_appointments_staff,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|integer|min:1|max:8',
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
            'notes' => 'nullable|string|max:2000',
            'internal_notes' => 'nullable|string|max:2000',
            'payment_status' => 'nullable|in:unpaid,paid,waived,invoiced',
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $endTime = date('H:i', strtotime('+' . $validated['duration_hours'] . ' hours', strtotime($validated['start_time'])));

        $service = AppointmentService::find($validated['service_id']);
        $isChargeable = $service ? $service->is_chargeable : false;
        $amount = $isChargeable ? $service->calculatePrice($validated['duration_hours']) : 0;

        $updateData = [
            'service_id' => $validated['service_id'],
            'staff_id' => $validated['staff_id'],
            'appointment_date' => $validated['appointment_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $endTime,
            'duration_hours' => $validated['duration_hours'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $appointment->notes,
            'internal_notes' => $validated['internal_notes'] ?? $appointment->internal_notes,
            'is_chargeable' => $isChargeable,
            'amount' => $amount,
            'payment_status' => $validated['payment_status'] ?? $appointment->payment_status,
            'updated_by' => Auth::id(),
        ];

        // Handle status changes
        if ($validated['status'] === Appointment::STATUS_CANCELLED && $appointment->status !== Appointment::STATUS_CANCELLED) {
            $updateData['cancelled_at'] = now();
            $updateData['cancellation_reason'] = $validated['cancellation_reason'] ?? null;
        }

        if ($validated['status'] === Appointment::STATUS_COMPLETED && $appointment->status !== Appointment::STATUS_COMPLETED) {
            $updateData['completed_at'] = now();
        }

        $appointment->update($updateData);

        // Send cancellation email if status changed to cancelled
        if ($validated['status'] === Appointment::STATUS_CANCELLED && $appointment->wasChanged('status')) {
            $appointment->load(['staff', 'service']);
            $this->emailService->sendCancellation($appointment);
        }

        return redirect()->route('cimsappointments.appointments.show', $id)
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Quick status update (AJAX).
     */
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'updated_by' => Auth::id(),
        ];

        if ($validated['status'] === Appointment::STATUS_CANCELLED) {
            $updateData['cancelled_at'] = now();
            $updateData['cancellation_reason'] = $validated['cancellation_reason'] ?? null;
        }

        if ($validated['status'] === Appointment::STATUS_COMPLETED) {
            $updateData['completed_at'] = now();
        }

        $appointment->update($updateData);

        // Send cancellation email
        if ($validated['status'] === Appointment::STATUS_CANCELLED) {
            $appointment->load(['staff', 'service']);
            $this->emailService->sendCancellation($appointment);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Status updated.']);
        }

        return back()->with('success', 'Appointment status updated to ' . ucfirst($validated['status']) . '.');
    }

    /**
     * Delete appointment (soft delete).
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->route('cimsappointments.appointments.index')
            ->with('success', 'Appointment deleted.');
    }

    // --- AJAX Endpoints ---

    /**
     * Search clients for autocomplete.
     */
    public function ajaxSearchClients(Request $request)
    {
        $search = $request->input('q', '');
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $clients = $this->clientSyncService->searchClients($search);
        return response()->json($clients);
    }

    /**
     * Get available time slots for a staff member on a date.
     */
    public function ajaxGetSlots(Request $request)
    {
        $staffId = $request->input('staff_id');
        $date = $request->input('date');
        $durationHours = (int) $request->input('duration_hours', 1);

        if (!$staffId || !$date) {
            return response()->json(['slots' => []]);
        }

        $result = $this->slotService->getAvailableSlots($staffId, $date, $durationHours);

        return response()->json([
            'slots' => $result['slots'],
            'staff_name' => $result['staff'] ? $result['staff']->name : '',
        ]);
    }

    /**
     * Get staff members who provide a given service.
     */
    public function ajaxGetStaffForService(Request $request)
    {
        $serviceId = $request->input('service_id');
        if (!$serviceId) {
            return response()->json([]);
        }

        $staff = AppointmentStaff::getActiveForService($serviceId);
        return response()->json($staff);
    }

    /**
     * Get service details (for price calculation).
     */
    public function ajaxGetServiceDetails(Request $request)
    {
        $serviceId = $request->input('service_id');
        $service = AppointmentService::find($serviceId);

        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        return response()->json([
            'id' => $service->id,
            'name' => $service->name,
            'is_chargeable' => $service->is_chargeable,
            'price_per_hour' => $service->price_per_hour,
            'min_duration_minutes' => $service->min_duration_minutes,
            'max_duration_minutes' => $service->max_duration_minutes,
            'default_duration_minutes' => $service->default_duration_minutes,
            'min_hours' => $service->getMinHours(),
            'max_hours' => $service->getMaxHours(),
        ]);
    }

    /**
     * Check client sync status.
     */
    public function ajaxCheckClientSync(Request $request)
    {
        $clientCode = $request->input('client_code');
        if (!$clientCode) {
            return response()->json(['synced' => false]);
        }

        $result = $this->clientSyncService->checkClientMasterSync($clientCode);
        return response()->json($result);
    }
}
