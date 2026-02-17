<x-admin-layout
    title="Editar Paciente | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Pacientes', 'href' => route('admin.patients.index')],
        ['name' => 'Editar'],
    ]"
>
    {{-- Header Card --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-8 mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-5">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-user-injured text-3xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $patient->user->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $patient->user->email }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.patients.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200">
                    <i class="fa-solid fa-arrow-left"></i>
                    Volver
                </a>
                <button type="submit" form="edit-patient-form"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                    <i class="fa-solid fa-check"></i>
                    Guardar cambios
                </button>
            </div>
        </div>
    </div>

    {{-- Formulario principal --}}
    <form id="edit-patient-form" action="{{ route('admin.patients.update', $patient) }}" method="POST">
        @csrf
        @method('PUT')

        <x-tabs id="patientTabs" :initialTab="$initialTab">

            {{-- Botones de navegación --}}
            <x-slot name="links">
                <x-tab-link tab="personal" icon="fa-id-card">
                    Datos Personales
                </x-tab-link>

                <x-tab-link
                    tab="history"
                    icon="fa-notes-medical"
                    :hasError="$errors->hasAny(['blood_type_id', 'allergies', 'chronic_conditions', 'surgical_history', 'family_history'])"
                >
                    Antecedentes
                </x-tab-link>

                <x-tab-link
                    tab="general"
                    icon="fa-circle-info"
                    :hasError="$errors->hasAny(['date_of_birth', 'gender', 'id_number', 'observations'])"
                >
                    Información General
                </x-tab-link>

                <x-tab-link
                    tab="emergency"
                    icon="fa-phone-volume"
                    :hasError="$errors->hasAny(['emergency_contact_name', 'emergency_contact_phone', 'emergency_relationship'])"
                    :last="true"
                >
                    Contacto de Emergencia
                </x-tab-link>
            </x-slot>

            {{-- Pestaña 1: Datos Personales (solo lectura) --}}
            <x-tab-content tab="personal">
                <x-wire-alert class="mb-6" color="info" title="Edición de cuenta de usuario">
                    <strong>La información de acceso</strong> (Nombre, Email y Contraseña) debe gestionarse desde la cuenta del usuario asociada.
                    <x-slot name="action">
                        <x-wire-button blue sm href="{{ route('admin.users.edit', $patient->user) }}">
                            <i class="fa-solid fa-user-pen"></i>
                            Editar Usuario
                        </x-wire-button>
                    </x-slot>
                </x-wire-alert>

                <div class="grid lg:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="mt-1 text-base text-gray-900">{{ $patient->user->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Teléfono</p>
                        <p class="mt-1 text-base text-gray-900">{{ $patient->user->phone ?? '-' }}</p>
                    </div>
                    <div class="lg:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Dirección</p>
                        <p class="mt-1 text-base text-gray-900">{{ $patient->user->address ?? '-' }}</p>
                    </div>
                </div>
            </x-tab-content>

            {{-- Pestaña 2: Antecedentes --}}
            <x-tab-content tab="history" title="Información Médica">
                <div class="grid lg:grid-cols-2 gap-4">
                    <div class="space-y-1 lg:col-span-2 lg:w-1/2">
                        <x-wire-native-select
                            label="Tipo de sangre"
                            name="blood_type_id"
                            :options="$bloodTypes"
                            option-key-value
                            placeholder="Seleccione tipo de sangre"
                            :value="old('blood_type_id', $patient->blood_type_id)"
                        />
                    </div>

                    <x-wire-textarea label="Alergias" name="allergies"
                        placeholder="Alergias conocidas (opcional)"
                        :value="old('allergies', $patient->allergies)" />

                    <x-wire-textarea label="Condiciones crónicas" name="chronic_conditions"
                        placeholder="Condiciones crónicas (opcional)"
                        :value="old('chronic_conditions', $patient->chronic_conditions)" />

                    <x-wire-textarea label="Historial quirúrgico" name="surgical_history"
                        placeholder="Cirugías previas (opcional)"
                        :value="old('surgical_history', $patient->surgical_history)" />

                    <x-wire-textarea label="Antecedentes familiares" name="family_history"
                        placeholder="Antecedentes familiares (opcional)"
                        :value="old('family_history', $patient->family_history)" />
                </div>
            </x-tab-content>

            {{-- Pestaña 3: Información General --}}
            <x-tab-content tab="general" title="Datos Generales">
                <div class="grid lg:grid-cols-2 gap-4">
                    <x-wire-input label="Fecha de nacimiento" name="date_of_birth" type="date"
                        value="{{ old('date_of_birth', $patient->date_of_birth->format('Y-m-d')) }}" />

                    <x-wire-native-select label="Género" name="gender" :options="[
                        'male'   => 'Masculino',
                        'female' => 'Femenino',
                        'other'  => 'Otro',
                    ]" option-key-value placeholder="Seleccione género"
                        :value="old('gender', $patient->gender)" />

                    <x-wire-input label="Número de identificación" name="id_number"
                        placeholder="Número de identificación"
                        value="{{ old('id_number', $patient->user->id_number) }}" />

                    <x-wire-textarea label="Observaciones" name="observations"
                        placeholder="Observaciones adicionales (opcional)"
                        class="lg:col-span-2"
                        :value="old('observations', $patient->observations)" />
                </div>
            </x-tab-content>

            {{-- Pestaña 4: Contacto de Emergencia --}}
            <x-tab-content tab="emergency" title="Datos del Contacto de Emergencia">
                <div class="grid lg:grid-cols-2 gap-4">
                    <x-wire-input label="Contacto de emergencia" name="emergency_contact_name"
                        placeholder="Nombre del contacto"
                        value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" />

                    <x-wire-phone label="Teléfono de emergencia" name="emergency_contact_phone"
                        placeholder="(999) 999-9999"
                        value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" />

                    <x-wire-input label="Parentesco" name="emergency_relationship"
                        placeholder="Parentesco con el paciente"
                        value="{{ old('emergency_relationship', $patient->emergency_relationship) }}" />
                </div>
            </x-tab-content>

        </x-tabs>
    </form>

</x-admin-layout>
