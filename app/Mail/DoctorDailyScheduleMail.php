<?php

namespace App\Mail;

use App\Models\Doctor;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DoctorDailyScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Doctor $doctor,
        public Collection $appointments,
        public string $date,
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tu Agenda del Día — {$this->date}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.doctor_daily_schedule',
            with: [
                'doctor'       => $this->doctor,
                'appointments' => $this->appointments,
                'date'         => $this->date,
            ],
        );
    }
}
