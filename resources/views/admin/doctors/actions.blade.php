<div class="flex items-center space-x-2">
    <x-wire-button href="{{ route('admin.doctors.schedules', $doctor) }}" secondary xs title="Gestionar horario">
        <i class="fa-solid fa-calendar-week"></i>
    </x-wire-button>

    <x-wire-button href="{{ route('admin.doctors.show', $doctor) }}" secondary xs>
        <i class="fa-solid fa-eye"></i>
    </x-wire-button>

    <x-wire-button href="{{ route('admin.doctors.edit', $doctor) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" class="delete-form">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
