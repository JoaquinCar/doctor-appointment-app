@props(['tab', 'title' => null])

<div
    x-show="activeTab === '{{ $tab }}'"
    class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm"
    role="tabpanel"
>
    @if ($title)
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h3>
    @endif

    {{ $slot }}
</div>
