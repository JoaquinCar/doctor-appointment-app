<x-admin-layout
    title="Editar Doctor | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Doctores', 'href' => route('admin.doctors.index')],
        ['name' => 'Editar'],
    ]"
>
    {{-- Header Card --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-8 mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-5">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-user-doctor text-3xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $doctor->user->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $doctor->user->email }}</p>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2">
                        <span class="text-xs text-gray-500">
                            <span class="font-medium">N° Licencia:</span>
                            {{ $doctor->medical_license_number ?? 'N/A' }}
                        </span>
                        <span class="text-xs text-gray-500">
                            <span class="font-medium">Biografía:</span>
                            {{ $doctor->biography ? Str::limit($doctor->biography, 60) : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.doctors.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200">
                    <i class="fa-solid fa-arrow-left"></i>
                    Volver
                </a>
                <button type="submit" form="edit-doctor-form"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                    <i class="fa-solid fa-check"></i>
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>

    {{-- Formulario principal --}}
    <form id="edit-doctor-form" action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
        @csrf
        @method('PUT')

        <x-wire-card>
            <div class="grid lg:grid-cols-2 gap-4">
                <div class="space-y-1 lg:col-span-2 lg:w-1/2">
                    <x-wire-native-select
                        label="Especialidad"
                        name="speciality_id"
                        :options="$specialities"
                        option-key-value
                        placeholder="Seleccione una especialidad"
                        :value="old('speciality_id', $doctor->speciality_id)"
                    />
                </div>

                <x-wire-input
                    label="Número de licencia médica"
                    name="medical_license_number"
                    placeholder="Solo números"
                    inputmode="numeric"
                    value="{{ old('medical_license_number', $doctor->medical_license_number) }}"
                    class="lg:col-span-2 lg:w-1/2"
                />

                <x-wire-textarea
                    label="Biografía"
                    name="biography"
                    placeholder="Breve descripción profesional"
                    class="lg:col-span-2"
                    :value="old('biography', $doctor->biography)"
                />
            </div>
        </x-wire-card>
    </form>

</x-admin-layout>
