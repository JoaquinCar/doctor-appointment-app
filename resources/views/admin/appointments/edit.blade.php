<x-admin-layout
    title="Editar cita | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas médicas', 'href' => route('admin.appointments.index')],
        ['name' => 'Editar cita'],
    ]"
>
    @php
        $statusLabels = App\Models\Appointment::statusLabels();
        $statusColors = App\Models\Appointment::statusColors();
        $currentColor = $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800';
        $currentLabel = $statusLabels[$appointment->status] ?? $appointment->status;
    @endphp

    {{-- Header Card --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-8 mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-5">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-calendar-days text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $appointment->patient->user->name }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Dr. {{ $appointment->doctor->user->name }} —
                        {{ $appointment->appointment_date->format('d/m/Y') }}
                        {{ substr($appointment->start_time, 0, 5) }} – {{ substr($appointment->end_time, 0, 5) }}
                    </p>
                    <span class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $currentColor }}">
                        {{ $currentLabel }}
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.appointments.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fa-solid fa-arrow-left"></i>
                    Volver
                </a>
                <button type="submit" form="edit-appointment-form"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fa-solid fa-check"></i>
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>

    <form id="edit-appointment-form" action="{{ route('admin.appointments.update', $appointment) }}" method="POST"
          x-data="{ status: '{{ old('status', $appointment->status) }}' }">
        @csrf
        @method('PUT')

        <x-wire-card>
            <div class="grid sm:grid-cols-2 gap-4">
                {{-- Estado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado de la cita</label>
                    <select
                        name="status"
                        x-model="status"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        @foreach($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $appointment->status) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Motivo de cancelación (solo si status = cancelled) --}}
                <div x-show="status === 'cancelled'">
                    <x-wire-input
                        label="Motivo de cancelación"
                        name="cancellation_reason"
                        placeholder="Indique el motivo..."
                        value="{{ old('cancellation_reason', $appointment->cancellation_reason) }}"
                    />
                </div>

                {{-- Notas del doctor --}}
                <div class="sm:col-span-2">
                    <x-wire-textarea
                        label="Notas"
                        name="notes"
                        placeholder="Observaciones o notas adicionales..."
                        :value="old('notes', $appointment->notes)"
                    />
                </div>
            </div>

            @if($errors->any())
                <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-wire-card>
    </form>

</x-admin-layout>
