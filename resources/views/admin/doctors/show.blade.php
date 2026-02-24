<x-admin-layout
    title="Detalle Doctor | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Doctores',
            'href' => route('admin.doctors.index'),
        ],
        [
            'name' => $doctor->user->name,
        ],
    ]"
>
    <x-wire-card>
        <div class="grid lg:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Personal</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                        <dd class="text-sm text-gray-900">{{ $doctor->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="text-sm text-gray-900">{{ $doctor->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                        <dd class="text-sm text-gray-900">{{ $doctor->user->phone ?? 'No registrado' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Identificación</dt>
                        <dd class="text-sm text-gray-900">{{ $doctor->user->id_number ?? 'No registrado' }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Profesional</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Especialidad</dt>
                        <dd class="text-sm text-gray-900">{{ $doctor->speciality->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Cédula Profesional</dt>
                        <dd class="text-sm text-gray-900">{{ $doctor->medical_license_number ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Biografía</h3>
                <p class="text-sm text-gray-900">{{ $doctor->biography ?? 'N/A' }}</p>
            </div>
        </div>
    </x-wire-card>

    <div class="flex justify-end mt-4 space-x-2">
        <x-wire-button flat secondary href="{{ route('admin.doctors.index') }}">
            Volver
        </x-wire-button>
        <x-wire-button blue href="{{ route('admin.doctors.edit', $doctor) }}">
            <i class="fa-solid fa-pen-to-square"></i>
            Editar
        </x-wire-button>
    </div>

</x-admin-layout>
