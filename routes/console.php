<?php

use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $tomorrow = Carbon::tomorrow()->toDateString();
    Appointment::with(['patient.user'])
        ->whereDate('appointment_date', $tomorrow)
        ->whereNotIn('status', ['cancelled', 'completed', 'no_show'])
        ->whereHas('patient.user', fn ($q) => $q->whereNotNull('phone')->where('phone', '!=', ''))
        ->each(fn (Appointment $appointment) => SendAppointmentReminder::dispatch($appointment->id));
})->dailyAt('08:00')
  ->name('whatsapp:send-reminders')
  ->withoutOverlapping();
