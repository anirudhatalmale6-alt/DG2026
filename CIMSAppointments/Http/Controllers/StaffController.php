<?php

namespace Modules\CIMSAppointments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CIMSAppointments\Models\AppointmentStaff;
use Modules\CIMSAppointments\Models\AppointmentService;
use Modules\CIMSAppointments\Models\StaffAvailability;
use Modules\CIMSAppointments\Models\BlockedDate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        $staff = AppointmentStaff::with(['services', 'availability'])
            ->withCount('appointments')
            ->orderBy('name', 'asc')
            ->get();

        $stats = [
            'total' => $staff->count(),
            'active' => $staff->where('is_active', true)->count(),
            'inactive' => $staff->where('is_active', false)->count(),
        ];

        return view('cims_appointments::staff.index', compact('staff', 'stats'));
    }

    public function create()
    {
        $services = AppointmentService::getActive();
        $users = DB::table('users')->orderBy('first_name')->get();
        return view('cims_appointments::staff.create', compact('services', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'position' => 'nullable|string|max:100',
            'user_id' => 'nullable|integer',
            'color' => 'nullable|string|max:20',
            'services' => 'nullable|array',
            'services.*' => 'integer|exists:cims_appointments_services,id',
        ]);

        $validated['is_active'] = 1;
        $validated['created_by'] = Auth::id();

        $serviceIds = $request->input('services', []);
        unset($validated['services']);

        $staff = AppointmentStaff::create($validated);

        // Attach services
        if (!empty($serviceIds)) {
            $staff->services()->attach($serviceIds);
        }

        // Create default availability (Mon-Fri 08:00-17:00, Sat 09:00-13:00)
        $defaultAvailability = [
            ['day_of_week' => 0, 'start_time' => '08:00', 'end_time' => '17:00'], // Monday
            ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '17:00'], // Tuesday
            ['day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '17:00'], // Wednesday
            ['day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '17:00'], // Thursday
            ['day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '17:00'], // Friday
            ['day_of_week' => 5, 'start_time' => '09:00', 'end_time' => '13:00'], // Saturday
        ];

        foreach ($defaultAvailability as $avail) {
            $staff->availability()->create(array_merge($avail, ['is_active' => 1]));
        }

        return redirect()->route('cimsappointments.staff.index')
            ->with('success', 'Staff member added with default availability (Mon-Fri 8am-5pm, Sat 9am-1pm).');
    }

    public function show($id)
    {
        $staff = AppointmentStaff::with(['services', 'availability', 'blockedDates', 'appointments' => function ($q) {
            $q->with(['service'])->orderBy('appointment_date', 'desc')->limit(20);
        }])->findOrFail($id);

        return view('cims_appointments::staff.show', compact('staff'));
    }

    public function edit($id)
    {
        $staff = AppointmentStaff::with(['services', 'availability'])->findOrFail($id);
        $services = AppointmentService::getActive();
        $users = DB::table('users')->orderBy('first_name')->get();
        return view('cims_appointments::staff.edit', compact('staff', 'services', 'users'));
    }

    public function update(Request $request, $id)
    {
        $staff = AppointmentStaff::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'position' => 'nullable|string|max:100',
            'user_id' => 'nullable|integer',
            'color' => 'nullable|string|max:20',
            'services' => 'nullable|array',
            'services.*' => 'integer|exists:cims_appointments_services,id',
        ]);

        $validated['updated_by'] = Auth::id();
        $serviceIds = $request->input('services', []);
        unset($validated['services']);

        $staff->update($validated);
        $staff->services()->sync($serviceIds);

        return redirect()->route('cimsappointments.staff.index')
            ->with('success', 'Staff member updated.');
    }

    public function destroy($id)
    {
        $staff = AppointmentStaff::findOrFail($id);

        $upcomingCount = $staff->appointments()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', '>=', now()->toDateString())
            ->count();

        if ($upcomingCount > 0) {
            return back()->with('error', 'Cannot delete staff with ' . $upcomingCount . ' upcoming appointment(s). Reassign or cancel them first.');
        }

        $staff->delete();

        return redirect()->route('cimsappointments.staff.index')
            ->with('success', 'Staff member removed.');
    }

    public function activate($id)
    {
        $staff = AppointmentStaff::findOrFail($id);
        $staff->update(['is_active' => 1, 'updated_by' => Auth::id()]);
        return back()->with('success', 'Staff member activated.');
    }

    public function deactivate($id)
    {
        $staff = AppointmentStaff::findOrFail($id);
        $staff->update(['is_active' => 0, 'updated_by' => Auth::id()]);
        return back()->with('success', 'Staff member deactivated.');
    }

    // --- Availability Management ---

    public function updateAvailability(Request $request, $id)
    {
        $staff = AppointmentStaff::findOrFail($id);

        $validated = $request->validate([
            'availability' => 'required|array',
            'availability.*.day_of_week' => 'required|integer|min:0|max:5',
            'availability.*.start_time' => 'required|date_format:H:i',
            'availability.*.end_time' => 'required|date_format:H:i|after:availability.*.start_time',
            'availability.*.is_active' => 'nullable|boolean',
        ]);

        // Delete existing availability and recreate
        $staff->availability()->delete();

        foreach ($validated['availability'] as $avail) {
            $staff->availability()->create([
                'day_of_week' => $avail['day_of_week'],
                'start_time' => $avail['start_time'],
                'end_time' => $avail['end_time'],
                'is_active' => isset($avail['is_active']) ? 1 : 0,
            ]);
        }

        return back()->with('success', 'Availability updated successfully.');
    }

    // --- Blocked Dates ---

    public function storeBlockedDate(Request $request, $id)
    {
        $staff = AppointmentStaff::findOrFail($id);

        $validated = $request->validate([
            'blocked_date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255',
        ]);

        $staff->blockedDates()->create([
            'blocked_date' => $validated['blocked_date'],
            'reason' => $validated['reason'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Date blocked successfully.');
    }

    public function destroyBlockedDate($staffId, $blockedDateId)
    {
        $blockedDate = BlockedDate::where('staff_id', $staffId)
            ->where('id', $blockedDateId)
            ->firstOrFail();

        $blockedDate->delete();

        return back()->with('success', 'Blocked date removed.');
    }
}
