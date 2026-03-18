<?php

namespace Modules\CIMSAppointments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\CIMSAppointments\Models\Appointment;
use Modules\CIMSAppointments\Models\AppointmentService;
use Modules\CIMSAppointments\Models\AppointmentStaff;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $weekEnd = Carbon::now()->endOfWeek()->toDateString();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd = Carbon::now()->endOfMonth()->toDateString();

        // Today's appointments
        $todayAppointments = Appointment::with(['staff', 'service'])
            ->today()
            ->get();

        // This week's upcoming
        $weekAppointments = Appointment::with(['staff', 'service'])
            ->upcoming()
            ->forDateRange($weekStart, $weekEnd)
            ->get();

        // Stats
        $stats = [
            'today_count' => $todayAppointments->count(),
            'week_count' => $weekAppointments->count(),
            'month_total' => Appointment::forDateRange($monthStart, $monthEnd)->count(),
            'month_completed' => Appointment::forDateRange($monthStart, $monthEnd)
                ->where('status', Appointment::STATUS_COMPLETED)->count(),
            'month_cancelled' => Appointment::forDateRange($monthStart, $monthEnd)
                ->where('status', Appointment::STATUS_CANCELLED)->count(),
            'month_no_show' => Appointment::forDateRange($monthStart, $monthEnd)
                ->where('status', Appointment::STATUS_NO_SHOW)->count(),
            'pending_count' => Appointment::where('status', Appointment::STATUS_PENDING)->count(),
            'month_revenue' => Appointment::forDateRange($monthStart, $monthEnd)
                ->where('status', Appointment::STATUS_COMPLETED)
                ->where('is_chargeable', true)
                ->sum('amount'),
            'total_services' => AppointmentService::where('is_active', 1)->count(),
            'total_staff' => AppointmentStaff::where('is_active', 1)->count(),
        ];

        return view('cims_appointments::appointments.dashboard', compact(
            'todayAppointments',
            'weekAppointments',
            'stats'
        ));
    }

    /**
     * Calendar view - returns JSON events for FullCalendar.
     */
    public function calendarEvents(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfMonth()->toDateString());
        $end = $request->input('end', Carbon::now()->endOfMonth()->toDateString());
        $staffId = $request->input('staff_id');

        $query = Appointment::with(['staff', 'service'])
            ->forDateRange($start, $end);

        if ($staffId) {
            $query->forStaff($staffId);
        }

        $appointments = $query->get();

        $events = $appointments->map(function ($appt) {
            $color = '#17A2B8'; // default teal
            if ($appt->staff && $appt->staff->color) {
                $color = $appt->staff->color;
            } elseif ($appt->service && $appt->service->color) {
                $color = $appt->service->color;
            }

            // Override color by status
            if ($appt->status === Appointment::STATUS_CANCELLED) {
                $color = '#dc3545';
            } elseif ($appt->status === Appointment::STATUS_COMPLETED) {
                $color = '#28a745';
            } elseif ($appt->status === Appointment::STATUS_NO_SHOW) {
                $color = '#6c757d';
            }

            return [
                'id' => $appt->id,
                'title' => ($appt->client_name ?? 'No Client') . ' - ' . ($appt->service ? $appt->service->name : ''),
                'start' => $appt->appointment_date->format('Y-m-d') . 'T' . $appt->start_time,
                'end' => $appt->appointment_date->format('Y-m-d') . 'T' . $appt->end_time,
                'color' => $color,
                'extendedProps' => [
                    'appointment_id' => $appt->id,
                    'client_name' => $appt->client_name,
                    'staff_name' => $appt->staff ? $appt->staff->name : '',
                    'service_name' => $appt->service ? $appt->service->name : '',
                    'status' => $appt->status,
                    'status_label' => $appt->getStatusLabel(),
                    'amount' => $appt->amount,
                    'duration_hours' => $appt->duration_hours,
                ],
            ];
        });

        return response()->json($events);
    }
}
