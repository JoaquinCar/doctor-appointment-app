<?php

namespace App\Jobs;

use App\Mail\AppointmentConfirmationMail;
use App\Models\Appointment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentConfirmationEmail implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly int $appointmentId)
    {
        //
    }

    public function handle(): void
    {
        $appointment = Appointment::with([
            'patient.user',
            'doctor.user',
            'doctor.speciality',
        ])->find($this->appointmentId);

        if (!$appointment) {
            Log::info('SendAppointmentConfirmationEmail: appointment not found', [
                'appointment_id' => $this->appointmentId,
            ]);
            return;
        }

        $patientEmail = $appointment->patient?->user?->email;
        $doctorEmail  = $appointment->doctor?->user?->email;

        $mailable = new AppointmentConfirmationMail($appointment);

        if ($patientEmail) {
            Mail::to($patientEmail)->send($mailable);
        }

        if ($doctorEmail) {
            Mail::to($doctorEmail)->send($mailable);
        }
    }
}
