<?php

namespace App\Mail;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Cita Médica — ' .
                \Carbon\Carbon::parse($this->appointment->appointment_date)
                    ->locale('es')->translatedFormat('j \d\e F \d\e Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment_confirmation',
            with: ['appointment' => $this->appointment],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdfs.appointment_confirmation', [
            'appointment' => $this->appointment,
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'comprobante-cita-' . str_pad($this->appointment->id, 6, '0', STR_PAD_LEFT) . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
