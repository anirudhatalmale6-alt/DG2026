<?php

namespace Modules\CIMSAppointments\Services;

use Carbon\Carbon;
use Modules\CIMSAppointments\Models\Appointment;
use Modules\CIMSAppointments\Models\AppointmentStaff;
use Modules\CIMSAppointments\Models\AppointmentSetting;
use Modules\CIMSAppointments\Models\StaffAvailability;

class SlotService
{
    /**
     * Get available time slots for a staff member on a given date.
     *
     * @param int $staffId
     * @param string $date (Y-m-d)
     * @param int $durationHours Number of consecutive hours needed
     * @return array ['slots' => [...], 'staff' => AppointmentStaff]
     */
    public function getAvailableSlots(int $staffId, string $date, int $durationHours = 1): array
    {
        $staff = AppointmentStaff::find($staffId);
        if (!$staff || !$staff->is_active) {
            return ['slots' => [], 'staff' => null];
        }

        // Check if date is blocked
        if ($staff->isBlockedOn($date)) {
            return ['slots' => [], 'staff' => $staff];
        }

        // Get day of week (0=Monday, 5=Saturday)
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeekIso - 1; // Carbon: 1=Mon, 7=Sun → 0=Mon, 6=Sun

        // Sunday = 6, not available
        if ($dayOfWeek > 5) {
            return ['slots' => [], 'staff' => $staff];
        }

        // Get availability for this day
        $availability = $staff->getAvailabilityForDay($dayOfWeek);
        if (!$availability) {
            return ['slots' => [], 'staff' => $staff];
        }

        // Generate all possible hour slots from availability
        $allSlots = $availability->getTimeSlots();

        // Get existing appointments for this staff on this date
        $existingAppointments = Appointment::where('staff_id', $staffId)
            ->where('appointment_date', $date)
            ->whereIn('status', [
                Appointment::STATUS_PENDING,
                Appointment::STATUS_CONFIRMED,
            ])
            ->get();

        // Build set of booked hours
        $bookedSlots = [];
        foreach ($existingAppointments as $appt) {
            $start = strtotime($appt->start_time);
            $end = strtotime($appt->end_time);
            while ($start < $end) {
                $bookedSlots[] = date('H:i', $start);
                $start = strtotime('+1 hour', $start);
            }
        }

        // Filter out booked slots
        $freeSlots = array_values(array_diff($allSlots, $bookedSlots));

        // Check booking buffer (minimum notice)
        $bufferHours = (int) AppointmentSetting::getValue('booking_buffer_hours', 2);
        $now = Carbon::now();
        $isToday = $carbonDate->isToday();

        if ($isToday) {
            $cutoff = $now->copy()->addHours($bufferHours)->format('H:i');
            $freeSlots = array_values(array_filter($freeSlots, function ($slot) use ($cutoff) {
                return $slot >= $cutoff;
            }));
        }

        // If past date, no slots
        if ($carbonDate->isPast() && !$isToday) {
            return ['slots' => [], 'staff' => $staff];
        }

        // If duration > 1, only return start slots where all consecutive hours are free
        if ($durationHours > 1) {
            $validStartSlots = [];
            foreach ($freeSlots as $slot) {
                $startTime = strtotime($slot);
                $allConsecutiveFree = true;

                for ($h = 0; $h < $durationHours; $h++) {
                    $checkSlot = date('H:i', strtotime('+' . $h . ' hour', $startTime));
                    if (!in_array($checkSlot, $freeSlots)) {
                        $allConsecutiveFree = false;
                        break;
                    }
                    // Also check that the last slot's end time doesn't exceed availability
                    if ($h === $durationHours - 1) {
                        $endSlot = date('H:i', strtotime('+' . ($h + 1) . ' hour', $startTime));
                        $availEnd = $availability->end_time;
                        if ($endSlot > substr($availEnd, 0, 5)) {
                            $allConsecutiveFree = false;
                            break;
                        }
                    }
                }

                if ($allConsecutiveFree) {
                    $validStartSlots[] = $slot;
                }
            }
            $freeSlots = $validStartSlots;
        }

        return ['slots' => $freeSlots, 'staff' => $staff];
    }

    /**
     * Get all available slots for a date across all staff who provide a given service.
     *
     * @return array [['staff_id' => int, 'staff_name' => string, 'slots' => [...]], ...]
     */
    public function getAvailableSlotsForService(int $serviceId, string $date, int $durationHours = 1): array
    {
        $staffMembers = AppointmentStaff::getActiveForService($serviceId);
        $results = [];

        foreach ($staffMembers as $staff) {
            $slotData = $this->getAvailableSlots($staff->id, $date, $durationHours);
            if (!empty($slotData['slots'])) {
                $results[] = [
                    'staff_id' => $staff->id,
                    'staff_name' => $staff->name,
                    'staff_color' => $staff->color,
                    'slots' => $slotData['slots'],
                ];
            }
        }

        return $results;
    }

    /**
     * Validate that a booking can be made (slots are free and consecutive).
     */
    public function validateBooking(int $staffId, string $date, string $startTime, int $durationHours): array
    {
        $slotData = $this->getAvailableSlots($staffId, $date, $durationHours);

        if (empty($slotData['slots'])) {
            return ['valid' => false, 'message' => 'No available slots for this staff member on this date.'];
        }

        $startFormatted = substr($startTime, 0, 5);
        if (!in_array($startFormatted, $slotData['slots'])) {
            return ['valid' => false, 'message' => 'The selected time slot is not available.'];
        }

        return ['valid' => true, 'message' => 'Slot is available.'];
    }
}
