<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReportAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $appointments,
        public string $date,
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reporte Diario de Citas — {$this->date}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.daily_report_admin',
            with: [
                'appointments' => $this->appointments,
                'date'         => $this->date,
            ],
        );
    }
}
