<x-admin-layout
    title="Usuarios | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Usuarios',
            'href' => route('admin.users.index'),
        ],
        [
            'name' => 'Nuevo',
        ],
    ]"
>
    <x-wire-card>
        <form action="{{route('admin.users.store')}}" method="POST">
            @csrf

            <x-wire-input label="Nombre" name="name" placeholder="Nombre del usuario"
            value="{{old('name')}}">
            </x-wire-input>

            <x-wire-input label="Email" name="email" type="email" placeholder="correo@ejemplo.com"
            value="{{old('email')}}">
            </x-wire-input>

            <div class="flex justify-end mt-4">
                <x-wire-button type="submit" blue>Guardar</x-wire-button>
            </div>

        </form>
    </x-wire-card>

</x-admin-layout>
