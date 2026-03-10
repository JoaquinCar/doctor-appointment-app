<x-admin-layout
    title="Horarios | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Horarios'],
    ]"
>
    <x-wire-card>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs uppercase text-gray-500 border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">Doctor</th>
                        <th class="py-3 px-4">Especialidad</th>
                        <th class="py-3 px-4">Bloques activos</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($doctors as $doctor)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium text-gray-900">
                                {{ $doctor->user->name }}
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                {{ $doctor->speciality->name }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $doctor->schedules->where('is_active', true)->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $doctor->schedules->where('is_active', true)->count() }} bloques
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <x-wire-button
                                    href="{{ route('admin.doctors.schedules', $doctor) }}"
                                    blue xs
                                >
                                    <i class="fa-solid fa-calendar-week mr-1"></i>
                                    Ver horario
                                </x-wire-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400">
                                <i class="fa-solid fa-calendar-xmark text-2xl mb-2 block"></i>
                                No hay doctores registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-wire-card>

</x-admin-layout>
