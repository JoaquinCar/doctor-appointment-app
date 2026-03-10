<?php

namespace App\Livewire\Admin\DataTables;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AppointmentTable extends DataTableComponent
{
    protected $model = Appointment::class;

    public function builder(): Builder
    {
        return Appointment::query()->with([
            'patient.user',
            'doctor.user',
        ]);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        $statusLabels = Appointment::statusLabels();
        $statusColors = Appointment::statusColors();

        return [
            Column::make('Id', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Paciente', 'patient.user.name')
                ->sortable()
                ->searchable(),

            Column::make('Doctor', 'doctor.user.name')
                ->sortable()
                ->searchable(),

            Column::make('Fecha', 'appointment_date')
                ->sortable()
                ->format(fn($value) => $value->format('d/m/Y')),

            Column::make('Hora inicio', 'start_time')
                ->sortable()
                ->format(fn($value) => substr($value, 0, 5)),

            Column::make('Hora fin', 'end_time')
                ->sortable()
                ->format(fn($value) => substr($value, 0, 5)),

            Column::make('Estado', 'status')
                ->sortable()
                ->format(function ($value) use ($statusLabels, $statusColors) {
                    $label = $statusLabels[$value] ?? $value;
                    $color = $statusColors[$value] ?? 'bg-gray-100 text-gray-800';
                    return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$color}\">{$label}</span>";
                })
                ->html(),

            Column::make('Acciones')
                ->label(fn($row) => view('admin.appointments.actions', ['appointment' => $row])),
        ];
    }
}
