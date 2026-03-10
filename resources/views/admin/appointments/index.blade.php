<x-admin-layout
    title="Citas médicas | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas médicas'],
    ]"
>
    <x-slot name="action">
        <x-wire-button blue href="{{ route('admin.appointments.create') }}">
            <i class="fa-solid fa-plus"></i>
            Nueva cita
        </x-wire-button>
    </x-slot>

    @livewire('admin.data-tables.appointment-table')

</x-admin-layout>
