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
        <form action="{{route('admin.roles.update',$role)}}" method="POST">
            @csrf

            @method('PUT')

            <x-wire-input label="Nombre" name="name" placeholder="Nombre del rol"
                          value="{{old('name',$role->name)}}">
            </x-wire-input>
            <div class="flex justify-end mt-4">
                <x-wire-button type="submit" blue>Actualizar</x-wire-button>

            </div>

        </form>
    </x-wire-card>
</x-admin-layout>
