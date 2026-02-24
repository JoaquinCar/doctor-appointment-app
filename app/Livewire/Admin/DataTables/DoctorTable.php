<?php

namespace App\Livewire\Admin\DataTables;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DoctorTable extends DataTableComponent
{
    protected $model = Doctor::class;

    public function builder(): Builder
    {
        return Doctor::query()->with(['user', 'speciality']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable()
                ->searchable(),
            Column::make('Nombre', 'user.name')
                ->sortable()
                ->searchable(),
            Column::make('Especialidad', 'speciality.name')
                ->sortable()
                ->searchable(),
            Column::make('N° Licencia Médica', 'medical_license_number')
                ->sortable()
                ->format(fn($value) => $value ?? 'N/A'),
            Column::make('Biografía', 'biography')
                ->format(fn($value) => $value
                    ? \Illuminate\Support\Str::limit($value, 50)
                    : 'N/A'
                ),
            Column::make('Acciones')
                ->label(function ($row) {
                    return view('admin.doctors.actions', ['doctor' => $row]);
                }),
        ];
    }
}
