<?php

namespace Modules\CIMSAppointments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CIMSAppointments\Models\AppointmentService;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $services = AppointmentService::withCount('appointments')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $stats = [
            'total' => $services->count(),
            'active' => $services->where('is_active', true)->count(),
            'inactive' => $services->where('is_active', false)->count(),
            'chargeable' => $services->where('is_chargeable', true)->count(),
        ];

        return view('cims_appointments::services.index', compact('services', 'stats'));
    }

    public function create()
    {
        return view('cims_appointments::services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'default_duration_minutes' => 'required|integer|min:60|max:480',
            'min_duration_minutes' => 'required|integer|min:60|max:480',
            'max_duration_minutes' => 'required|integer|min:60|max:480',
            'is_chargeable' => 'nullable|boolean',
            'price_per_hour' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_chargeable'] = $request->has('is_chargeable') ? 1 : 0;
        $validated['price_per_hour'] = $validated['is_chargeable'] ? ($validated['price_per_hour'] ?? 0) : 0;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = 1;
        $validated['created_by'] = Auth::id();

        AppointmentService::create($validated);

        return redirect()->route('cimsappointments.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit($id)
    {
        $service = AppointmentService::findOrFail($id);
        return view('cims_appointments::services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = AppointmentService::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'default_duration_minutes' => 'required|integer|min:60|max:480',
            'min_duration_minutes' => 'required|integer|min:60|max:480',
            'max_duration_minutes' => 'required|integer|min:60|max:480',
            'is_chargeable' => 'nullable|boolean',
            'price_per_hour' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_chargeable'] = $request->has('is_chargeable') ? 1 : 0;
        $validated['price_per_hour'] = $validated['is_chargeable'] ? ($validated['price_per_hour'] ?? 0) : 0;
        $validated['updated_by'] = Auth::id();

        $service->update($validated);

        return redirect()->route('cimsappointments.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy($id)
    {
        $service = AppointmentService::findOrFail($id);

        // Check if service has upcoming appointments
        $upcomingCount = $service->appointments()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', '>=', now()->toDateString())
            ->count();

        if ($upcomingCount > 0) {
            return back()->with('error', 'Cannot delete service with ' . $upcomingCount . ' upcoming appointment(s). Cancel them first.');
        }

        $service->delete();

        return redirect()->route('cimsappointments.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    public function activate($id)
    {
        $service = AppointmentService::findOrFail($id);
        $service->update(['is_active' => 1, 'updated_by' => Auth::id()]);

        return redirect()->route('cimsappointments.services.index')
            ->with('success', 'Service activated.');
    }

    public function deactivate($id)
    {
        $service = AppointmentService::findOrFail($id);
        $service->update(['is_active' => 0, 'updated_by' => Auth::id()]);

        return redirect()->route('cimsappointments.services.index')
            ->with('success', 'Service deactivated.');
    }
}
