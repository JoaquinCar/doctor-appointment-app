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
            'name' => 'Editar',
        ],
    ]">

    @if(session('swal'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire(@json(session('swal')));
            });
        </script>
    @endif

    <x-wire-card>
        <form action="{{route('admin.users.update',$user)}}" method="POST">
            @csrf

            @method('PUT')

            <x-wire-input label="Nombre" name="name" placeholder="Nombre del usuario"
                          value="{{old('name',$user->name)}}">
            </x-wire-input>

            <x-wire-input label="Email" name="email" type="email" placeholder="correo@ejemplo.com"
                          value="{{old('email',$user->email)}}">
            </x-wire-input>

            <div class="flex justify-end mt-4">
                <x-wire-button type="submit" blue>Actualizar</x-wire-button>
            </div>

        </form>
    </x-wire-card>
</x-admin-layout>
