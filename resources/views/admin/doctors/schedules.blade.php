<x-admin-layout
    title="Horario de {{ $doctor->user->name }} | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
        ['name' => $doctor->user->name, 'href' => route('admin.doctors.show', $doctor)],
        ['name' => 'Horario'],
    ]"
>
    {{-- Header del doctor --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5 mb-6 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                <i class="fa-solid fa-user-doctor text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $doctor->user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $doctor->speciality->name }}</p>
            </div>
        </div>
        <a href="{{ route('admin.doctors.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shrink-0">
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>
    </div>

    {{-- Formulario cuadrícula de checkboxes --}}
    <form action="{{ route('admin.doctors.schedules.save', $doctor) }}" method="POST">
        @csrf
        @method('PUT')

        <x-wire-card>
            {{-- Acciones --}}
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">
                        <i class="fa-solid fa-calendar-week mr-2 text-blue-500"></i>
                        Disponibilidad semanal
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Marca los bloques de 15 min en que el doctor está disponible. Slots de 08:00 a 20:00.
                    </p>
                </div>
                <x-wire-button type="submit" blue>
                    <i class="fa-solid fa-floppy-disk mr-1"></i>
                    Guardar horario
                </x-wire-button>
            </div>

            {{-- Tabla de checkboxes --}}
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse select-none" style="min-width: 560px;">
                    <thead>
                        <tr class="border-b-2 border-gray-300">
                            <th class="w-14 py-2 px-2 text-left font-semibold text-gray-500 sticky left-0 bg-white">
                                Hora
                            </th>
                            @foreach($days as $dayKey => $dayLabel)
                                <th class="py-2 px-1 text-center font-semibold text-gray-600 min-w-[64px]">
                                    <div>{{ $dayLabel }}</div>
                                    {{-- Botón seleccionar/deseleccionar toda la columna --}}
                                    <button
                                        type="button"
                                        onclick="toggleColumn('{{ $dayKey }}')"
                                        class="mt-1 text-[10px] font-normal text-blue-500 hover:text-blue-700 hover:underline"
                                    >todo/ninguno</button>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $prevHour = null; @endphp
                        @foreach($slots as $slot)
                            @php
                                [$h, $m] = explode(':', $slot);
                                $isHourStart = ($h !== $prevHour);
                                $prevHour = $h;
                            @endphp
                            <tr class="{{ $isHourStart ? 'border-t-2 border-gray-200' : 'border-t border-gray-100' }} hover:bg-blue-50 transition-colors">
                                <td class="py-1 px-2 font-mono text-gray-500 sticky left-0 bg-white whitespace-nowrap">
                                    {{ $slot }}
                                </td>
                                @foreach(array_keys($days) as $dayKey)
                                    @php $checked = $grid[$dayKey][$slot] ?? false; @endphp
                                    <td class="py-1 px-1 text-center">
                                        <input
                                            type="checkbox"
                                            name="slots[{{ $dayKey }}][]"
                                            value="{{ $slot }}"
                                            data-day="{{ $dayKey }}"
                                            {{ $checked ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                        >
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                <span class="flex items-center gap-1.5">
                    <input type="checkbox" checked disabled class="w-3.5 h-3.5 rounded border-gray-300 text-blue-600">
                    Disponible
                </span>
                <span class="flex items-center gap-1.5">
                    <input type="checkbox" disabled class="w-3.5 h-3.5 rounded border-gray-300">
                    No disponible
                </span>
            </div>
        </x-wire-card>
    </form>

    <script>
        function toggleColumn(day) {
            const checkboxes = document.querySelectorAll(`input[type=checkbox][data-day="${day}"]`);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }
    </script>

</x-admin-layout>
