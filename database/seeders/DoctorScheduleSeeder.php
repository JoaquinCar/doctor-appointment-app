<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        // Dr. Carlos Mendoza (Cardiología): Lunes-Viernes 08:00-12:00 y 14:00-18:00
        $mendoza = Doctor::whereHas('user', fn($q) => $q->where('email', 'carlos.mendoza@medimatch.com'))->first();

        if ($mendoza) {
            foreach ($weekdays as $day) {
                DoctorSchedule::firstOrCreate(
                    ['doctor_id' => $mendoza->id, 'day_of_week' => $day, 'start_time' => '08:00:00', 'end_time' => '12:00:00'],
                    ['is_active' => true]
                );
                DoctorSchedule::firstOrCreate(
                    ['doctor_id' => $mendoza->id, 'day_of_week' => $day, 'start_time' => '14:00:00', 'end_time' => '18:00:00'],
                    ['is_active' => true]
                );
            }
        }

        // Dra. María García (Pediatría): Lunes-Sábado 09:00-13:00
        $garcia = Doctor::whereHas('user', fn($q) => $q->where('email', 'maria.garcia@medimatch.com'))->first();

        if ($garcia) {
            $days = array_merge($weekdays, ['saturday']);
            foreach ($days as $day) {
                DoctorSchedule::firstOrCreate(
                    ['doctor_id' => $garcia->id, 'day_of_week' => $day, 'start_time' => '09:00:00', 'end_time' => '13:00:00'],
                    ['is_active' => true]
                );
            }
        }
    }
}
