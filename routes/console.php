<?php

use App\Jobs\SendAppointmentReminder;
use App\Mail\DailyReportAdminMail;
use App\Mail\DoctorDailyScheduleMail;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// WhatsApp: recordatorios del día siguiente
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

// Email: reporte diario al administrador
Schedule::call(function () {
    $today = Carbon::today()->toDateString();
    $dateLabel = Carbon::today()->locale('es')->translatedFormat('j \d\e F \d\e Y');

    $appointments = Appointment::with(['patient.user', 'doctor.user', 'doctor.speciality'])
        ->whereDate('appointment_date', $today)
        ->whereNotIn('status', ['cancelled', 'no_show'])
        ->orderBy('start_time')
        ->get();

    $adminEmail = config('mail.admin_address', env('MAIL_ADMIN_ADDRESS'));
    if ($adminEmail) {
        Mail::to($adminEmail)->send(new DailyReportAdminMail($appointments, $dateLabel));
    }

    // Email: agenda del día a cada doctor con citas hoy
    Doctor::with(['user', 'appointments' => fn ($q) => $q
        ->with(['patient.user'])
        ->whereDate('appointment_date', $today)
        ->whereNotIn('status', ['cancelled', 'no_show'])
        ->orderBy('start_time'),
    ])->get()
    ->each(function (Doctor $doctor) use ($dateLabel) {
        if ($doctor->appointments->isEmpty()) return;
        if (!$doctor->user?->email) return;
        Mail::to($doctor->user->email)->send(new DoctorDailyScheduleMail($doctor, $doctor->appointments, $dateLabel));
    });
})->dailyAt('08:00')
  ->name('email:daily-reports')
  ->withoutOverlapping();
