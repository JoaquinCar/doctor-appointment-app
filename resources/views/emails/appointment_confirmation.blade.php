<x-mail::message>
# Cita Médica Confirmada

Hola **{{ $appointment->patient->user->name }}**,

Tu cita médica ha sido registrada exitosamente. Adjunto encontrarás el comprobante en PDF.

<x-mail::panel>
**Doctor:** {{ $appointment->doctor->user->name }}
**Especialidad:** {{ $appointment->doctor->speciality->name }}
**Fecha:** {{ \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->translatedFormat('j \d\e F \d\e Y') }}
**Hora:** {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}
</x-mail::panel>

Por favor, preséntate 10 minutos antes de tu cita.

Gracias,
{{ config('app.name') }}
</x-mail::message>
