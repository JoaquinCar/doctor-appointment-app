<x-admin-layout
    title="Nuevo Paciente | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Pacientes',
            'href' => route('admin.patients.index'),
        ],
        [
            'name' => 'Nuevo',
        ],
    ]"
>
    <x-wire-card>
        <form action="{{ route('admin.patients.store') }}" method="POST">
            @csrf

            <div class="grid lg:grid-cols-2 gap-4">
                <x-wire-input label="Nombre" name="name" placeholder="Nombre completo"
                value="{{ old('name') }}" autocomplete="name">
                </x-wire-input>

                <x-wire-input label="Email" name="email" type="email" placeholder="correo@ejemplo.com"
                value="{{ old('email') }}" inputmode="email" autocomplete="email">
                </x-wire-input>

                <x-wire-input label="Número de identificación" name="id_number" placeholder="Número de identificación"
                value="{{ old('id_number') }}">
                </x-wire-input>

                <x-wire-input label="Teléfono" name="phone" placeholder="Número de teléfono"
                value="{{ old('phone') }}" inputmode="tel" autocomplete="phone">
                </x-wire-input>

                <x-wire-input label="Contraseña" name="password" type="password" placeholder="Ingrese la contraseña"
                autocomplete="new-password">
                </x-wire-input>

                <x-wire-input label="Confirmar Contraseña" name="password_confirmation" type="password" placeholder="Confirme la contraseña"
                autocomplete="new-password">
                </x-wire-input>

                <x-wire-input label="Fecha de nacimiento" name="date_of_birth" type="date"
                value="{{ old('date_of_birth') }}">
                </x-wire-input>

                <div class="space-y-1">
                    <x-wire-native-select label="Género" name="gender" :options="[
                        'male' => 'Masculino',
                        'female' => 'Femenino',
                        'other' => 'Otro',
                    ]" option-key-value placeholder="Seleccione género" :value="old('gender')">
                    </x-wire-native-select>
                </div>

                <div class="space-y-1">
                    <x-wire-native-select label="Tipo de sangre" name="blood_type_id" :options="$bloodTypes"
                    option-key-value placeholder="Seleccione tipo de sangre" :value="old('blood_type_id')">
                    </x-wire-native-select>
                </div>

                <x-wire-textarea label="Dirección" name="address" placeholder="Dirección completa"
                class="lg:col-span-2" :value="old('address')">
                </x-wire-textarea>

                <x-wire-textarea label="Alergias" name="allergies" placeholder="Alergias conocidas (opcional)"
                :value="old('allergies')">
                </x-wire-textarea>

                <x-wire-textarea label="Condiciones crónicas" name="chronic_conditions" placeholder="Condiciones crónicas (opcional)"
                :value="old('chronic_conditions')">
                </x-wire-textarea>

                <x-wire-textarea label="Historial quirúrgico" name="surgical_history" placeholder="Cirugías previas (opcional)"
                :value="old('surgical_history')">
                </x-wire-textarea>

                <x-wire-textarea label="Observaciones" name="observations" placeholder="Observaciones adicionales (opcional)"
                class="lg:col-span-2" :value="old('observations')">
                </x-wire-textarea>

                <x-wire-input label="Contacto de emergencia" name="emergency_contact_name" placeholder="Nombre del contacto"
                value="{{ old('emergency_contact_name') }}">
                </x-wire-input>

                <x-wire-input label="Teléfono de emergencia" name="emergency_contact_phone" placeholder="Teléfono del contacto"
                value="{{ old('emergency_contact_phone') }}" inputmode="tel">
                </x-wire-input>

                <x-wire-input label="Parentesco" name="emergency_relationship" placeholder="Parentesco con el paciente"
                value="{{ old('emergency_relationship') }}">
                </x-wire-input>
            </div>

            <div class="flex justify-end mt-4">
                <x-wire-button type="submit" blue>Guardar</x-wire-button>
            </div>

        </form>
    </x-wire-card>

    <div class="flex justify-end mt-4">
        <x-wire-button flat secondary href="{{ route('admin.patients.index') }}">
            Volver a la lista de pacientes
        </x-wire-button>
    </div>

</x-admin-layout>
