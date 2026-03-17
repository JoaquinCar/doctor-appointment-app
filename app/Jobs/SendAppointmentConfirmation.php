<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAppointmentConfirmation implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly int $appointmentId)
    {
        //
    }

    public function handle(WhatsAppService $whatsApp): void
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user', 'doctor.speciality'])
            ->find($this->appointmentId);

        if (!$appointment) {
            Log::info('SendAppointmentConfirmation: appointment not found', [
                'appointment_id' => $this->appointmentId,
            ]);
            return;
        }

        $phone = $appointment->patient?->user?->phone ?? '';

        if (empty($phone)) {
            Log::info('SendAppointmentConfirmation: patient has no phone number', [
                'appointment_id' => $this->appointmentId,
                'patient_id'     => $appointment->patient_id,
            ]);
            return;
        }

        $patientName  = $appointment->patient->user->name;
        $doctorName   = $appointment->doctor->user->name;
        $speciality   = $appointment->doctor->speciality?->name ?? '';
        $date         = Carbon::parse($appointment->appointment_date)
            ->locale('es')
            ->translatedFormat('j \d\e F \d\e Y');
        $time         = Carbon::parse($appointment->start_time)->format('h:i A');

        $whatsApp->sendTemplate(
            $phone,
            config('whatsapp.templates.confirmation'),
            [$patientName, $doctorName, $speciality, $date, $time]
        );
    }
}
