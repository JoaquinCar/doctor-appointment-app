@props(['id' => 'tabs', 'initialTab' => 'personal'])

<div x-data="{ activeTab: '{{ $initialTab }}' }">

    {{-- Barra de navegación de pestañas --}}
    <div class="mb-6 border-b border-gray-200">
        <ul
            class="flex flex-wrap -mb-px text-sm font-medium text-center"
            id="{{ $id }}"
            role="tablist"
        >
            {{ $links }}
        </ul>
    </div>

    {{-- Paneles de contenido --}}
    <div>
        {{ $slot }}
    </div>

</div>
