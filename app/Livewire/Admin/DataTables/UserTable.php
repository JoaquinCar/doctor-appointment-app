<?php

namespace App\Livewire\Admin\DataTables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserTable extends DataTableComponent
{

    public function builder(): Builder
    {
        return User::query()->with('roles');
    }

    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "name")
                ->sortable(),
            Column::make("Email", "email")
                ->sortable(),
            Column::make("Numero de id", "id_number")
                ->sortable(),
            Column::make("Telefono", "phone")
                ->sortable(),
            Column::make("Direccion", "address")
                ->sortable(),
            Column::make("Rol", "roles")
                ->label(function($row){
                    return $row->roles->first()?->name ?? 'Sin rol';
                }),
            Column::make("Acciones")
                ->label(function($row){
                    return view('admin.users.actions',
                        ['user' => $row]);
                }),
        ];
    }
}
