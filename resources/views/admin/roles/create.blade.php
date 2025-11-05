<x-admin-layout
    title="Roles | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Roles',
            'href' => route('admin.roles.index'),
        ],
        [
            'name' => 'Nuevo',
        ],
    ]"
>
    <x-wire-card>
        <form action="{{route('admin.roles.store')}}" method="POST">
            @csrf

            <x-wire-input label="Nombre" name="name" placeholder="Nombre del rol"
            value="{{old('name')}}">
            </x-wire-input>
            <div class="flex justify-end mt-4">
                <x-wire-button type="submit" blue>Guardar</x-wire-button>

            </div>

        </form>
    </x-wire-card>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <x-wire-input
                        label="Nombre del Rol"
                        placeholder="Ingrese el nombre del rol"
                        name="name"
                        :value="old('name')"
                        required
                    />
                </div>

                <div class="flex justify-end space-x-2">
                    <x-wire-button
                        flat
                        secondary
                        href="{{ route('admin.roles.index') }}"
                    >
                        Cancelar
                    </x-wire-button>

                    <x-wire-button
                        type="submit"
                        primary
                    >
                        <i class="fa-solid fa-save"></i>
                        Guardar
                    </x-wire-button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
