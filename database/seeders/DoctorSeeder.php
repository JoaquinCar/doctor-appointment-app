<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'name'                   => 'Dr. Carlos Mendoza',
                'email'                  => 'carlos.mendoza@medimatch.com',
                'speciality'             => 'Cardiología',
                'medical_license_number' => 'IMSS-2024-001',
                'biography'              => 'Médico especialista en Cardiología con más de 10 años de experiencia. Egresado de la UNAM con subespecialidad en intervenciones cardiovasculares.',
            ],
            [
                'name'                   => 'Dra. María García',
                'email'                  => 'maria.garcia@medimatch.com',
                'speciality'             => 'Pediatría',
                'medical_license_number' => 'IMSS-2024-002',
                'biography'              => 'Pediatra con amplia experiencia en atención a neonatos y niños con enfermedades crónicas. Certificada por el Consejo Mexicano de Pediatría.',
            ],
        ];

        foreach ($doctors as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'],
                    'password'  => bcrypt('12345678'),
                    'phone'     => '9991234567',
                    'id_number' => 'DOC' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'address'   => 'Mérida, Yucatán',
                ]
            );

            $user->assignRole('Doctor');

            $speciality = Speciality::where('name', $data['speciality'])->first();

            if ($speciality) {
                Doctor::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'speciality_id'          => $speciality->id,
                        'medical_license_number' => $data['medical_license_number'],
                        'biography'              => $data['biography'],
                    ]
                );
            }
        }
    }
}
