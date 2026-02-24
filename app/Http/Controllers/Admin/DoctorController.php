<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.doctors.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialities = Speciality::pluck('name', 'id')->toArray();
        return view('admin.doctors.create', compact('specialities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|email|unique:users,email|max:255',
            'id_number'              => 'required|string|unique:users,id_number|max:20',
            'phone'                  => 'required|string|max:20',
            'address'                => 'required|string|max:500',
            'password'               => 'required|string|min:8|confirmed',
            'speciality_id'          => 'required|exists:specialities,id',
            'medical_license_number' => 'nullable|digits_between:1,20',
            'biography'              => 'nullable|string|min:10|max:1000',
        ]);

        // Crear el usuario
        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'id_number' => $validated['id_number'],
            'phone'     => $validated['phone'],
            'address'   => $validated['address'],
            'password'  => bcrypt($validated['password']),
        ]);

        // Asignar rol de Doctor
        $user->assignRole('Doctor');

        // Crear el registro de doctor
        $doctor = Doctor::create([
            'user_id'                => $user->id,
            'speciality_id'          => $validated['speciality_id'],
            'medical_license_number' => $validated['medical_license_number'] ?? null,
            'biography'              => $validated['biography'] ?? null,
        ]);

        return redirect()->route('admin.doctors.edit', $doctor)
            ->with('swal', [
                'title' => 'Doctor creado',
                'text'  => 'El doctor ha sido creado exitosamente.',
                'icon'  => 'success',
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'speciality']);
        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        $doctor->load(['user', 'speciality']);
        $specialities = Speciality::pluck('name', 'id')->toArray();
        return view('admin.doctors.edit', compact('doctor', 'specialities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'speciality_id'          => ['required', 'exists:specialities,id'],
            'medical_license_number' => ['nullable', 'digits_between:1,20'],
            'biography'              => ['nullable', 'string', 'min:10', 'max:1000'],
        ]);

        $doctor->update($validated);

        return redirect()->route('admin.doctors.edit', $doctor)
            ->with('swal', [
                'title' => 'Doctor actualizado',
                'text'  => 'El doctor ha sido actualizado exitosamente.',
                'icon'  => 'success',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        // Eliminar el usuario (el doctor se eliminará en cascada)
        $doctor->user->delete();

        return redirect()->route('admin.doctors.index')
            ->with('swal', [
                'title' => 'Doctor eliminado',
                'text'  => 'El doctor ha sido eliminado exitosamente.',
                'icon'  => 'success',
            ]);
    }
}
