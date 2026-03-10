<x-admin-layout
    title="Nueva cita | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas médicas', 'href' => route('admin.appointments.index')],
        ['name' => 'Nueva cita'],
    ]"
>
    <div
        x-data="{
            slot: null,
            setSlot(data) {
                this.slot = data;
            }
        }"
        x-on:slot-selected.window="setSlot($event.detail)"
    >
        {{-- Buscador de disponibilidad --}}
        @livewire('admin.availability-search')

        {{-- Formulario de cita (aparece al seleccionar un slot) --}}
        <div x-show="slot !== null" x-cloak class="mt-6">
            <x-wire-card>
                <div class="mb-4 rounded-lg bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                    <i class="fa-solid fa-calendar-check mr-2"></i>
                    <strong>Slot seleccionado:</strong>
                    <span x-text="slot ? slot.doctor_name + ' — ' + slot.date + ' de ' + slot.start + ' a ' + slot.end : ''"></span>
                </div>

                <form action="{{ route('admin.appointments.store') }}" method="POST">
                    @csrf

                    {{-- Campos ocultos que llegan del slot --}}
                    <input type="hidden" name="doctor_id"        x-bind:value="slot ? slot.doctor_id : ''">
                    <input type="hidden" name="appointment_date" x-bind:value="slot ? slot.date : ''">
                    <input type="hidden" name="start_time"       x-bind:value="slot ? slot.start : ''">
                    <input type="hidden" name="end_time"         x-bind:value="slot ? slot.end : ''">

                    <div class="grid sm:grid-cols-2 gap-4">
                        {{-- Paciente --}}
                        <div class="sm:col-span-2">
                            <x-wire-native-select
                                label="Paciente"
                                name="patient_id"
                                :options="$patients"
                                option-key-value
                                placeholder="Seleccione un paciente"
                                :value="old('patient_id')"
                            />
                        </div>

                        {{-- Motivo --}}
                        <div class="sm:col-span-2">
                            <x-wire-textarea
                                label="Motivo de la consulta"
                                name="reason"
                                placeholder="Describa el motivo de la consulta..."
                                :value="old('reason')"
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

                    <div class="flex items-center gap-3 mt-6">
                        <a href="{{ route('admin.appointments.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fa-solid fa-arrow-left"></i>
                            Cancelar
                        </a>
                        <x-wire-button type="submit" blue>
                            <i class="fa-solid fa-calendar-plus mr-1"></i>
                            Registrar cita
                        </x-wire-button>
                    </div>
                </form>
            </x-wire-card>
        </div>
    </div>

</x-admin-layout>
