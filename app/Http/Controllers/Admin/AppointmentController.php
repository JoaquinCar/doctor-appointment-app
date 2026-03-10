<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        return view('admin.appointments.index');
    }

    public function create()
    {
        $patients = Patient::with('user')
            ->get()
            ->pluck('user.name', 'id')
            ->toArray();

        return view('admin.appointments.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'patient_id'       => ['required', 'exists:patients,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'reason'           => ['nullable', 'string', 'max:500'],
        ]);

        $startFull = $validated['start_time'] . ':00';
        $endFull   = $validated['end_time'] . ':00';
        $dayOfWeek = strtolower(Carbon::parse($validated['appointment_date'])->englishDayOfWeek);

        // 1. Verificar que el doctor tenga disponibilidad ese día/hora
        if (!DoctorSchedule::coversSlot($validated['doctor_id'], $dayOfWeek, $startFull, $endFull)) {
            return back()
                ->withErrors(['start_time' => 'El doctor no tiene disponibilidad en ese horario.'])
                ->withInput();
        }

        // 2. Verificar que no haya conflicto con citas existentes
        if (Appointment::hasConflict($validated['doctor_id'], $validated['appointment_date'], $startFull, $endFull)) {
            return back()
                ->withErrors(['start_time' => 'El horario seleccionado ya está ocupado por otra cita.'])
                ->withInput();
        }

        Appointment::create([
            'doctor_id'        => $validated['doctor_id'],
            'patient_id'       => $validated['patient_id'],
            'appointment_date' => $validated['appointment_date'],
            'start_time'       => $startFull,
            'end_time'         => $endFull,
            'reason'           => $validated['reason'],
            'status'           => 'scheduled',
        ]);

        return redirect()->route('admin.appointments.index')->with('swal', [
            'title' => 'Cita registrada',
            'text'  => 'La cita ha sido registrada exitosamente.',
            'icon'  => 'success',
        ]);
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user', 'doctor.speciality']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'doctor.user', 'doctor.speciality']);
        return view('admin.appointments.edit', compact('appointment'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status'              => ['required', 'in:scheduled,confirmed,completed,cancelled,no_show'],
            'notes'               => ['nullable', 'string', 'max:1000'],
            'cancellation_reason' => ['nullable', 'required_if:status,cancelled', 'string', 'max:500'],
        ]);

        $data = [
            'status' => $validated['status'],
            'notes'  => $validated['notes'] ?? $appointment->notes,
        ];

        if ($validated['status'] === 'cancelled' && !$appointment->cancelled_at) {
            $data['cancellation_reason'] = $validated['cancellation_reason'];
            $data['cancelled_at']        = now();
            $data['cancelled_by']        = auth()->id();
        }

        $appointment->update($data);

        return redirect()->route('admin.appointments.edit', $appointment)->with('swal', [
            'title' => 'Cita actualizada',
            'text'  => 'La cita ha sido actualizada exitosamente.',
            'icon'  => 'success',
        ]);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('admin.appointments.index')->with('swal', [
            'title' => 'Cita eliminada',
            'text'  => 'La cita ha sido eliminada.',
            'icon'  => 'success',
        ]);
    }
}
