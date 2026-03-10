<div>
    {{-- Formulario de búsqueda --}}
    <x-wire-card class="mb-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">
            <i class="fa-solid fa-magnifying-glass mr-2 text-blue-500"></i>
            Buscar disponibilidad
        </h3>

        <div class="grid sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de la cita</label>
                <input
                    type="date"
                    wire:model="date"
                    min="{{ date('Y-m-d') }}"
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                @error('date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad (opcional)</label>
                <select
                    wire:model="speciality_id"
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">Todas las especialidades</option>
                    @foreach($specialities as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-wire-button wire:click="search" blue>
                    <i class="fa-solid fa-search mr-1"></i>
                    Buscar disponibilidad
                </x-wire-button>
            </div>
        </div>
    </x-wire-card>

    {{-- Resultados --}}
    @if($searched)
        @if(count($results) === 0)
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                No hay disponibilidad para la fecha y filtros seleccionados.
            </div>
        @else
            <div class="space-y-4">
                @foreach($results as $result)
                    <x-wire-card>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fa-solid fa-user-doctor"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $result['doctor_name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $result['speciality_name'] }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach($result['slots'] as $slot)
                                @php
                                    $isSelected = $slotSelected
                                        && $selectedDoctorId === $result['doctor_id']
                                        && $selectedStart === $slot['start'];
                                @endphp
                                <button
                                    wire:click="selectSlot({{ $result['doctor_id'] }}, '{{ $result['doctor_name'] }}', '{{ $slot['start'] }}', '{{ $slot['end'] }}')"
                                    type="button"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-colors
                                        {{ $isSelected
                                            ? 'bg-blue-600 text-white border-blue-600'
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-blue-50 hover:border-blue-400' }}"
                                >
                                    {{ $slot['start_display'] }} – {{ $slot['end_display'] }}
                                </button>
                            @endforeach
                        </div>
                    </x-wire-card>
                @endforeach
            </div>
        @endif
    @endif
</div>
