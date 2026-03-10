<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Speciality;
use Carbon\Carbon;
use Livewire\Component;

class AvailabilitySearch extends Component
{
    public string $date        = '';
    public string $speciality_id = '';

    public array $results  = [];
    public bool $searched  = false;

    // Slot seleccionado
    public int    $selectedDoctorId   = 0;
    public string $selectedDoctorName = '';
    public string $selectedStart      = '';
    public string $selectedEnd        = '';
    public bool   $slotSelected       = false;

    public function search(): void
    {
        $this->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $dayOfWeek = strtolower(Carbon::parse($this->date)->englishDayOfWeek);

        $query = DoctorSchedule::with(['doctor.user', 'doctor.speciality'])
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true);

        if ($this->speciality_id) {
            $query->whereHas('doctor', fn($q) => $q->where('speciality_id', $this->speciality_id));
        }

        $schedules = $query->get();

        $grouped = [];

        foreach ($schedules as $schedule) {
            $current = Carbon::parse($schedule->start_time);
            $end     = Carbon::parse($schedule->end_time);

            while ($current->copy()->addMinutes(15) <= $end) {
                $slotStart = $current->format('H:i:s');
                $slotEnd   = $current->copy()->addMinutes(15)->format('H:i:s');

                if (!Appointment::hasConflict($schedule->doctor_id, $this->date, $slotStart, $slotEnd)) {
                    $doctorId = $schedule->doctor_id;

                    if (!isset($grouped[$doctorId])) {
                        $grouped[$doctorId] = [
                            'doctor_id'        => $doctorId,
                            'doctor_name'      => $schedule->doctor->user->name,
                            'speciality_name'  => $schedule->doctor->speciality->name,
                            'slots'            => [],
                        ];
                    }

                    $grouped[$doctorId]['slots'][] = [
                        'start'         => $slotStart,
                        'end'           => $slotEnd,
                        'start_display' => $current->format('H:i'),
                        'end_display'   => $current->copy()->addMinutes(15)->format('H:i'),
                    ];
                }

                $current->addMinutes(15);
            }
        }

        $this->results  = array_values($grouped);
        $this->searched = true;
        $this->slotSelected = false;
    }

    public function selectSlot(int $doctorId, string $doctorName, string $start, string $end): void
    {
        $this->selectedDoctorId   = $doctorId;
        $this->selectedDoctorName = $doctorName;
        $this->selectedStart      = $start;
        $this->selectedEnd        = $end;
        $this->slotSelected       = true;

        $this->dispatch('slot-selected', [
            'doctor_id'   => $doctorId,
            'doctor_name' => $doctorName,
            'date'        => $this->date,
            'start'       => substr($start, 0, 5),
            'end'         => substr($end, 0, 5),
        ]);
    }

    public function render()
    {
        $specialities = Speciality::orderBy('name')->pluck('name', 'id');

        return view('livewire.admin.availability-search', compact('specialities'));
    }
}
