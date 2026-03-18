<?php

namespace Modules\CIMSAppointments\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CIMSAppointments\Models\AppointmentSetting;
use Modules\CIMSAppointments\Models\BlockedDate;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = AppointmentSetting::getAllSettings();

        // Get global blocked dates (not staff-specific)
        $globalBlockedDates = BlockedDate::whereNull('staff_id')
            ->where('blocked_date', '>=', now()->toDateString())
            ->orderBy('blocked_date', 'asc')
            ->get();

        return view('cims_appointments::settings.index', compact('settings', 'globalBlockedDates'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:500',
            'confirmation_email_enabled' => 'nullable|boolean',
            'reminder_email_enabled' => 'nullable|boolean',
            'cancellation_email_enabled' => 'nullable|boolean',
            'reminder_hours_before' => 'nullable|integer|min:1|max:72',
            'booking_buffer_hours' => 'nullable|integer|min:0|max:48',
            'cancellation_policy_hours' => 'nullable|integer|min:0|max:72',
            'default_slot_duration' => 'nullable|integer|min:30|max:240',
        ]);

        // Save each setting
        foreach ($validated as $key => $value) {
            if ($value === null && in_array($key, ['confirmation_email_enabled', 'reminder_email_enabled', 'cancellation_email_enabled'])) {
                $value = '0'; // Checkbox unchecked
            }
            $group = $this->getSettingGroup($key);
            AppointmentSetting::setValue($key, $value ?? '', $group);
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    /**
     * Add a global blocked date (applies to all staff).
     */
    public function storeGlobalBlockedDate(Request $request)
    {
        $validated = $request->validate([
            'blocked_date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255',
        ]);

        BlockedDate::create([
            'staff_id' => null, // Global
            'blocked_date' => $validated['blocked_date'],
            'reason' => $validated['reason'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Global blocked date added.');
    }

    public function destroyGlobalBlockedDate($id)
    {
        $blockedDate = BlockedDate::whereNull('staff_id')->where('id', $id)->firstOrFail();
        $blockedDate->delete();

        return back()->with('success', 'Global blocked date removed.');
    }

    private function getSettingGroup(string $key): string
    {
        $emailKeys = ['confirmation_email_enabled', 'reminder_email_enabled', 'cancellation_email_enabled', 'reminder_hours_before'];
        $bookingKeys = ['booking_buffer_hours', 'cancellation_policy_hours', 'default_slot_duration'];

        if (in_array($key, $emailKeys)) return 'email';
        if (in_array($key, $bookingKeys)) return 'booking';
        return 'general';
    }
}
