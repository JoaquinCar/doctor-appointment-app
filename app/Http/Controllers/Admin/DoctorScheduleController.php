<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    /**
     * Cuadrícula semanal de horarios de un doctor específico.
     */
    public function index(Doctor $doctor)
    {
        $doctor->load(['user', 'speciality', 'schedules']);

        $days = [
            'monday'    => 'Lunes',
            'tuesday'   => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday'  => 'Jueves',
            'friday'    => 'Viernes',
            'saturday'  => 'Sábado',
            'sunday'    => 'Domingo',
        ];

        // Slots de 15 minutos: 08:00 → 19:45
        $slots = [];
        $current = Carbon::createFromTimeString('08:00');
        $endOfDay = Carbon::createFromTimeString('20:00');
        while ($current < $endOfDay) {
            $slots[] = $current->format('H:i');
            $current->addMinutes(15);
        }

        // Pre-computar la cuadrícula en PHP (sin queries adicionales)
        $activeSchedules = $doctor->schedules->where('is_active', true);
        $grid = [];

        foreach (array_keys($days) as $day) {
            $daySchedules = $activeSchedules->where('day_of_week', $day);
            foreach ($slots as $slot) {
                $slotTime = Carbon::createFromTimeString($slot);
                $covered = $daySchedules->filter(function ($s) use ($slotTime) {
                    $sStart = Carbon::createFromTimeString($s->start_time);
                    $sEnd   = Carbon::createFromTimeString($s->end_time);
                    return $slotTime >= $sStart && $slotTime < $sEnd;
                })->isNotEmpty();
                $grid[$day][$slot] = $covered;
            }
        }

        return view('admin.doctors.schedules', compact('doctor', 'days', 'slots', 'grid'));
    }

    /**
     * Guarda el horario completo desde la cuadrícula de checkboxes.
     * Elimina los registros anteriores y crea los nuevos slots de 15 min.
     */
    public function save(Request $request, Doctor $doctor): RedirectResponse
    {
        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        // Eliminar todos los horarios actuales del doctor
        $doctor->schedules()->delete();

        $slots = $request->input('slots', []);

        foreach ($slots as $day => $times) {
            if (!in_array($day, $validDays)) {
                continue;
            }
            foreach ((array) $times as $time) {
                // Validar formato H:i
                if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
                    continue;
                }

                DoctorSchedule::create([
                    'doctor_id'   => $doctor->id,
                    'day_of_week' => $day,
                    'start_time'  => $time . ':00',
                    'end_time'    => Carbon::createFromTimeString($time)->addMinutes(15)->format('H:i:s'),
                    'is_active'   => true,
                ]);
            }
        }

        return back()->with('swal', [
            'title' => 'Horario guardado',
            'text'  => 'El horario del doctor ha sido actualizado exitosamente.',
            'icon'  => 'success',
        ]);
    }
}
