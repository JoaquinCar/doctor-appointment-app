<x-mail::message>
# Reporte Diario de Citas — {{ $date }}

Hola Administrador,

A continuación el listado de **{{ $appointments->count() }} {{ $appointments->count() === 1 ? 'cita' : 'citas' }}** programadas para hoy.

@if($appointments->isEmpty())
No hay citas programadas para hoy.
@else
<x-mail::table>
| # | Paciente | Doctor | Especialidad | Hora | Estado |
|---|----------|--------|--------------|------|--------|
@foreach($appointments as $a)
| {{ str_pad($a->id, 4, '0', STR_PAD_LEFT) }} | {{ $a->patient->user->name }} | {{ $a->doctor->user->name }} | {{ $a->doctor->speciality->name }} | {{ \Carbon\Carbon::parse($a->start_time)->format('h:i A') }} | {{ ucfirst($a->status) }} |
@endforeach
</x-mail::table>
@endif

Gracias,
{{ config('app.name') }}
</x-mail::message>
