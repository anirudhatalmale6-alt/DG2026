<?php

use Illuminate\Support\Facades\Route;
use Modules\CIMSAppointments\Http\Controllers\DashboardController;
use Modules\CIMSAppointments\Http\Controllers\ServiceController;
use Modules\CIMSAppointments\Http\Controllers\StaffController;
use Modules\CIMSAppointments\Http\Controllers\AppointmentController;
use Modules\CIMSAppointments\Http\Controllers\SettingsController;
use Modules\CIMSAppointments\Http\Controllers\ReportController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('calendar/events', [DashboardController::class, 'calendarEvents'])->name('calendar.events');

// --- AJAX Routes (must come before parameterized routes) ---
Route::get('ajax/search-clients', [AppointmentController::class, 'ajaxSearchClients'])->name('ajax.search-clients');
Route::get('ajax/get-slots', [AppointmentController::class, 'ajaxGetSlots'])->name('ajax.get-slots');
Route::get('ajax/staff-for-service', [AppointmentController::class, 'ajaxGetStaffForService'])->name('ajax.staff-for-service');
Route::get('ajax/service-details', [AppointmentController::class, 'ajaxGetServiceDetails'])->name('ajax.service-details');
Route::get('ajax/check-client-sync', [AppointmentController::class, 'ajaxCheckClientSync'])->name('ajax.check-client-sync');

// --- Services ---
Route::get('services', [ServiceController::class, 'index'])->name('services.index');
Route::get('services/create', [ServiceController::class, 'create'])->name('services.create');
Route::post('services/store', [ServiceController::class, 'store'])->name('services.store');
Route::get('services/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');
Route::put('services/{id}/update', [ServiceController::class, 'update'])->name('services.update');
Route::delete('services/{id}/delete', [ServiceController::class, 'destroy'])->name('services.destroy');
Route::put('services/{id}/activate', [ServiceController::class, 'activate'])->name('services.activate');
Route::put('services/{id}/deactivate', [ServiceController::class, 'deactivate'])->name('services.deactivate');

// --- Staff ---
Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
Route::get('staff/create', [StaffController::class, 'create'])->name('staff.create');
Route::post('staff/store', [StaffController::class, 'store'])->name('staff.store');
Route::get('staff/{id}', [StaffController::class, 'show'])->name('staff.show');
Route::get('staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
Route::put('staff/{id}/update', [StaffController::class, 'update'])->name('staff.update');
Route::delete('staff/{id}/delete', [StaffController::class, 'destroy'])->name('staff.destroy');
Route::put('staff/{id}/activate', [StaffController::class, 'activate'])->name('staff.activate');
Route::put('staff/{id}/deactivate', [StaffController::class, 'deactivate'])->name('staff.deactivate');
Route::put('staff/{id}/availability', [StaffController::class, 'updateAvailability'])->name('staff.availability.update');
Route::post('staff/{id}/blocked-dates', [StaffController::class, 'storeBlockedDate'])->name('staff.blocked-dates.store');
Route::delete('staff/{staffId}/blocked-dates/{blockedDateId}', [StaffController::class, 'destroyBlockedDate'])->name('staff.blocked-dates.destroy');

// --- Appointments ---
Route::get('list', [AppointmentController::class, 'index'])->name('appointments.index');
Route::get('calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');
Route::get('book', [AppointmentController::class, 'create'])->name('appointments.create');
Route::post('book/store', [AppointmentController::class, 'store'])->name('appointments.store');
Route::get('view/{id}', [AppointmentController::class, 'show'])->name('appointments.show');
Route::get('edit/{id}', [AppointmentController::class, 'edit'])->name('appointments.edit');
Route::put('update/{id}', [AppointmentController::class, 'update'])->name('appointments.update');
Route::put('status/{id}', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
Route::delete('delete/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

// --- Reports ---
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

// --- Settings ---
Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('settings/update', [SettingsController::class, 'update'])->name('settings.update');
Route::post('settings/blocked-dates', [SettingsController::class, 'storeGlobalBlockedDate'])->name('settings.blocked-dates.store');
Route::delete('settings/blocked-dates/{id}', [SettingsController::class, 'destroyGlobalBlockedDate'])->name('settings.blocked-dates.destroy');
