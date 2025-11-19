<?php

namespace Database\Seeders;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Crear un usuario de prueba cada que se ejecuten migraciones
        //php artisan migrate:fresh -seed
        User::factory()->create([
            'name' => 'Jhonatan Keb',
            'email' => 'jhony@example.com',
            'password' => bcrypt('12345678'),
            'id_number' => '123456789',
            'phone' => '555555555',
            'address' => 'calle 123, colonia 456',

        ])->assignRole('Doctor');

        User::factory()->create([
            'name' => 'Kino',
            'email' => 'kino@gmail.com',
            'password' => bcrypt('12345678'),
            'id_number' => '987654321',
            'phone' => '555123456',
            'address' => 'calle principal 789',

        ])->assignRole('Administrador');
    }
}
