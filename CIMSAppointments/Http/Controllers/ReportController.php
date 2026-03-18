<?php

namespace Modules\CIMSAppointments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\CIMSAppointments\Models\Appointment;
use Modules\CIMSAppointments\Models\AppointmentStaff;
use Modules\CIMSAppointments\Models\AppointmentService;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->toDateString());
        $staffId = $request->input('staff_id');

        $query = Appointment::forDateRange($dateFrom, $dateTo);
        if ($staffId) {
            $query->forStaff($staffId);
        }

        $appointments = $query->get();

        // Summary stats
        $summary = [
            'total' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'no_show' => $appointments->where('status', 'no_show')->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'confirmed' => $appointments->where('status', 'confirmed')->count(),
            'total_revenue' => $appointments->where('status', 'completed')->where('is_chargeable', true)->sum('amount'),
            'total_hours' => $appointments->where('status', 'completed')->sum('duration_hours'),
        ];

        // By staff
        $byStaff = $appointments->groupBy('staff_id')->map(function ($group) {
            $staffName = $group->first()->staff ? $group->first()->staff->name : 'Unassigned';
            return [
                'staff_name' => $staffName,
                'total' => $group->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'cancelled' => $group->where('status', 'cancelled')->count(),
                'no_show' => $group->where('status', 'no_show')->count(),
                'revenue' => $group->where('status', 'completed')->where('is_chargeable', true)->sum('amount'),
                'hours' => $group->where('status', 'completed')->sum('duration_hours'),
            ];
        })->values();

        // By service
        $byService = $appointments->groupBy('service_id')->map(function ($group) {
            $serviceName = $group->first()->service ? $group->first()->service->name : 'Unknown';
            return [
                'service_name' => $serviceName,
                'total' => $group->count(),
                'completed' => $group->where('status', 'completed')->count(),
                'revenue' => $group->where('status', 'completed')->where('is_chargeable', true)->sum('amount'),
            ];
        })->values();

        // By day of week
        $byDayOfWeek = $appointments->groupBy(function ($a) {
            return $a->appointment_date->dayOfWeekIso;
        })->map(function ($group, $day) {
            $dayNames = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
            return [
                'day_name' => $dayNames[$day] ?? 'Unknown',
                'count' => $group->count(),
            ];
        })->sortKeys()->values();

        $staffList = AppointmentStaff::getActive();

        return view('cims_appointments::appointments.reports', compact(
            'summary', 'byStaff', 'byService', 'byDayOfWeek',
            'staffList', 'dateFrom', 'dateTo', 'staffId'
        ));
    }
}
