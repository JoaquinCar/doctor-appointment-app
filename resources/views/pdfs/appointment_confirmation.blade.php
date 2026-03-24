<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 13px; margin: 0; padding: 0; }
        .header { background: #1d4ed8; color: white; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 22px; letter-spacing: 1px; }
        .header p { margin: 4px 0 0; font-size: 12px; opacity: 0.85; }
        .body { padding: 32px; }
        .badge { display: inline-block; background: #dcfce7; color: #166534; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        table td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; }
        table td:first-child { width: 40%; color: #6b7280; font-weight: bold; }
        .note { background: #eff6ff; border-left: 4px solid #1d4ed8; padding: 12px 16px; font-size: 12px; color: #1e40af; border-radius: 4px; }
        .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MediMatch</h1>
        <p>Sistema de Gestión de Citas Médicas</p>
    </div>

    <div class="body">
        <div class="badge">✓ CITA CONFIRMADA</div>
        <div class="title">Comprobante de Cita Médica</div>

        <table>
            <tr>
                <td>Paciente</td>
                <td>{{ $appointment->patient->user->name }}</td>
            </tr>
            <tr>
                <td>Doctor</td>
                <td>{{ $appointment->doctor->user->name }}</td>
            </tr>
            <tr>
                <td>Especialidad</td>
                <td>{{ $appointment->doctor->speciality->name }}</td>
            </tr>
            <tr>
                <td>Fecha</td>
                <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->translatedFormat('j \d\e F \d\e Y') }}</td>
            </tr>
            <tr>
                <td>Hora</td>
                <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}</td>
            </tr>
            @if($appointment->reason)
            <tr>
                <td>Motivo</td>
                <td>{{ $appointment->reason }}</td>
            </tr>
            @endif
            <tr>
                <td>Folio</td>
                <td>#{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</td>
            </tr>
        </table>

        <div class="note">
            Por favor, preséntate 10 minutos antes de tu cita. Si necesitas cancelar, hazlo con al menos 24 horas de anticipación.
        </div>
    </div>

    <div class="footer">
        Comprobante generado el {{ now()->format('d/m/Y H:i') }} &bull; MediMatch &bull; Este documento es válido como comprobante de cita
    </div>
</body>
</html>
