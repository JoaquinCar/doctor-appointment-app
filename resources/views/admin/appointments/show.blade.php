<x-admin-layout
    title="Detalle de cita | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas médicas', 'href' => route('admin.appointments.index')],
        ['name' => 'Detalle'],
    ]"
>
    @php
        $statusLabels = App\Models\Appointment::statusLabels();
        $statusColors = App\Models\Appointment::statusColors();
        $color = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800';
        $label = $statusLabels[$appointment->status] ?? $appointment->status;
    @endphp

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.appointments.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>
        <a href="{{ route('admin.appointments.edit', $appointment) }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
            <i class="fa-solid fa-pen-to-square"></i>
            Editar
        </a>
    </div>

    <x-wire-card>
        <div class="grid sm:grid-cols-2 gap-6">
            {{-- Paciente --}}
            <div>
                <h3 class="text-xs font-semibold uppercase text-gray-400 mb-3 tracking-wide">
                    <i class="fa-solid fa-user mr-1"></i> Paciente
                </h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nombre</dt>
                        <dd class="font-medium text-gray-800">{{ $appointment->patient->user->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Email</dt>
                        <dd class="font-medium text-gray-800">{{ $appointment->patient->user->email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Teléfono</dt>
                        <dd class="font-medium text-gray-800">{{ $appointment->patient->user->phone ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Doctor --}}
            <div>
                <h3 class="text-xs font-semibold uppercase text-gray-400 mb-3 tracking-wide">
                    <i class="fa-solid fa-user-doctor mr-1"></i> Doctor
                </h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nombre</dt>
                        <dd class="font-medium text-gray-800">{{ $appointment->doctor->user->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Especialidad</dt>
                        <dd class="font-medium text-gray-800">{{ $appointment->doctor->speciality->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">N° Licencia</dt>
                        <dd class="font-medium text-gray-800">{{ $appointment->doctor->medical_license_number ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="sm:col-span-2 border-t border-gray-100 pt-4">
                <h3 class="text-xs font-semibold uppercase text-gray-400 mb-3 tracking-wide">
                    <i class="fa-solid fa-calendar-check mr-1"></i> Datos de la cita
                </h3>
                <dl class="grid sm:grid-cols-2 gap-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Fecha</dt>
                        <dd class="font-medium text-gray-800">{{ $appointment->appointment_date->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Horario</dt>
                        <dd class="font-medium text-gray-800">
                            {{ substr($appointment->start_time, 0, 5) }} – {{ substr($appointment->end_time, 0, 5) }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Estado</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ $label }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Registrada el</dt>
                        <dd class="font-medium text-gray-800">{{ $appointment->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($appointment->reason)
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500 mb-1">Motivo</dt>
                            <dd class="text-gray-800 bg-gray-50 rounded p-2">{{ $appointment->reason }}</dd>
                        </div>
                    @endif
                    @if($appointment->notes)
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500 mb-1">Notas</dt>
                            <dd class="text-gray-800 bg-gray-50 rounded p-2">{{ $appointment->notes }}</dd>
                        </div>
                    @endif
                    @if($appointment->status === 'cancelled')
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500 mb-1">Motivo de cancelación</dt>
                            <dd class="text-red-700 bg-red-50 rounded p-2">{{ $appointment->cancellation_reason ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Cancelada el</dt>
                            <dd class="font-medium text-gray-800">{{ $appointment->cancelled_at?->format('d/m/Y H:i') ?? 'N/A' }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </x-wire-card>

</x-admin-layout>
