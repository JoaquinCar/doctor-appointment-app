@props(['tab', 'hasError' => false, 'icon' => null, 'last' => false])

<li @class(['me-2' => !$last]) role="presentation">
    <button
        type="button"
        role="tab"
        @click="activeTab = '{{ $tab }}'"
        :class="activeTab === '{{ $tab }}'
            ? '{{ $hasError ? 'border-red-500 text-red-600' : 'border-blue-600 text-blue-600' }}'
            : '{{ $hasError ? 'border-red-300 text-red-400 hover:border-red-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}'"
        class="inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg transition-colors"
    >
        @if ($icon)
            <i class="fa-solid {{ $icon }}"></i>
        @endif

        {{ $slot }}

        @if ($hasError)
            <i class="fa-solid fa-circle-exclamation text-xs animate-pulse ml-1"></i>
        @endif
    </button>
</li>
