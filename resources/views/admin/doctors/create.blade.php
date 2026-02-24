<x-admin-layout
    title="Nuevo Doctor | MediMatch"
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
            'name' => 'Nuevo',
        ],
    ]"
>
    <x-wire-card>
        <form action="{{ route('admin.doctors.store') }}" method="POST">
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

                <x-wire-textarea label="Dirección" name="address" placeholder="Dirección completa"
                    class="lg:col-span-2" :value="old('address')">
                </x-wire-textarea>

                <div class="space-y-1 lg:col-span-2 lg:w-1/2">
                    <x-wire-native-select label="Especialidad" name="speciality_id"
                        :options="$specialities"
                        option-key-value
                        placeholder="Seleccione una especialidad"
                        :value="old('speciality_id')">
                    </x-wire-native-select>
                </div>

                <x-wire-input label="Número de licencia médica" name="medical_license_number"
                    placeholder="Solo números"
                    inputmode="numeric"
                    value="{{ old('medical_license_number') }}">
                </x-wire-input>

                <x-wire-textarea label="Biografía" name="biography"
                    placeholder="Breve descripción profesional"
                    class="lg:col-span-2"
                    :value="old('biography')">
                </x-wire-textarea>
            </div>

            <div class="flex justify-end mt-4">
                <x-wire-button type="submit" blue>Guardar</x-wire-button>
            </div>

        </form>
    </x-wire-card>

    <div class="flex justify-end mt-4">
        <x-wire-button flat secondary href="{{ route('admin.doctors.index') }}">
            Volver a la lista de doctores
        </x-wire-button>
    </div>

</x-admin-layout>
