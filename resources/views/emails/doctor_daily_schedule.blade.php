<x-mail::message>
# Tu Agenda del Día — {{ $date }}

Hola **{{ $doctor->user->name }}**,

Tienes **{{ $appointments->count() }} {{ $appointments->count() === 1 ? 'paciente' : 'pacientes' }}** agendados para hoy.

@if($appointments->isEmpty())
No tienes citas programadas para hoy.
@else
<x-mail::table>
| # | Paciente | Hora | Motivo |
|---|----------|------|--------|
@foreach($appointments as $a)
| {{ str_pad($a->id, 4, '0', STR_PAD_LEFT) }} | {{ $a->patient->user->name }} | {{ \Carbon\Carbon::parse($a->start_time)->format('h:i A') }} | {{ $a->reason ?? '—' }} |
@endforeach
</x-mail::table>
@endif

Que tengas un excelente día,
{{ config('app.name') }}
</x-mail::message>
