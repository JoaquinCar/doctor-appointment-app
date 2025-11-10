<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles,name']);

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return redirect()->route('admin.roles.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Rol creado correctamente',
            'text' => 'El rol ha sido creado exitosamente'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // Validar que no sea un rol del sistema
        if ($role->id <= 4) {
            return redirect()->route('admin.roles.edit', $role)->with('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No puedes actualizar este rol del sistema'
            ]);
        }

        $request->validate(['name' => 'required|unique:roles,name,' . $role->id]);

        if ($role->name === $request->name) {
            return redirect()->route('admin.roles.edit', $role)->with('swal', [
                'icon' => 'info',
                'title' => 'Sin cambios',
                'text' => 'No se detectaron modificaciones'
            ]);
        }

        $role->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.roles.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Rol actualizado correctamente',
            'text' => 'El rol ha sido actualizado exitosamente'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Validar que no sea un rol del sistema
        if ($role->id <= 4) {
            return redirect()->route('admin.roles.index')->with('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No puedes eliminar este rol del sistema'
            ]);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('swal', [
            'icon' => 'success',
            'title' => 'Rol eliminado correctamente',
            'text' => 'El rol ha sido eliminado exitosamente'
        ]);
    }
}
