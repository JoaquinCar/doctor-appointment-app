<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'reason',
        'notes',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'cancelled_at'     => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Detecta conflictos de horario para un doctor en una fecha dada.
     * Algoritmo: dos intervalos [A_start, A_end] y [B_start, B_end] se solapan si:
     *   A_start < B_end  AND  A_end > B_start
     */
    public static function hasConflict(
        int $doctorId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): bool {
        return static::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    public static function statusLabels(): array
    {
        return [
            'scheduled'  => 'Programada',
            'confirmed'  => 'Confirmada',
            'completed'  => 'Completada',
            'cancelled'  => 'Cancelada',
            'no_show'    => 'No asistió',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'scheduled'  => 'bg-blue-100 text-blue-800',
            'confirmed'  => 'bg-green-100 text-green-800',
            'completed'  => 'bg-gray-100 text-gray-800',
            'cancelled'  => 'bg-red-100 text-red-800',
            'no_show'    => 'bg-orange-100 text-orange-800',
        ];
    }
}
