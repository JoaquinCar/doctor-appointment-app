<x-admin-layout
    title="Doctores | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Doctores',
        ],
    ]"
>

    <x-slot name="action">
        <x-wire-button blue href="{{ route('admin.doctors.create') }}">
            <i class="fa-solid fa-plus"></i>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.data-tables.doctor-table')

</x-admin-layout>
