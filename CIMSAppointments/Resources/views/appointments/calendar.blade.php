@extends('layouts.default')

@section('title', 'Appointment Calendar')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
<style>
#appointmentCalendar { min-height: 650px; }
.fc-event { cursor: pointer; border-radius: 4px; padding: 2px 4px; font-size: 12px; }
.fc .fc-toolbar-title { font-size: 1.3em; }
.calendar-filter { margin-bottom: 15px; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item active">Calendar</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'calendar', 'counts' => ['pending' => 0]])
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Calendar View</h4>
                    <div class="calendar-filter">
                        <select id="calStaffFilter" class="form-select form-select-sm" style="width:200px;display:inline-block;">
                            <option value="">All Staff</option>
                            @foreach($staffList as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="appointmentCalendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('appointmentCalendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(info, successCallback, failureCallback) {
            const staffId = document.getElementById('calStaffFilter').value;
            let url = '{{ route("cimsappointments.calendar.events") }}?start=' + info.startStr + '&end=' + info.endStr;
            if (staffId) url += '&staff_id=' + staffId;

            fetch(url)
                .then(r => r.json())
                .then(events => successCallback(events))
                .catch(err => failureCallback(err));
        },
        eventClick: function(info) {
            const apptId = info.event.extendedProps.appointment_id;
            window.location.href = '{{ url("cims/appointments/view") }}/' + apptId;
        },
        height: 'auto',
        slotMinTime: '07:00:00',
        slotMaxTime: '18:00:00',
        allDaySlot: false,
        weekends: true,
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5, 6],
            startTime: '08:00',
            endTime: '17:00',
        },
    });

    calendar.render();

    document.getElementById('calStaffFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
});
</script>
@endpush
