@props(['tab', 'active' => null, 'hasError' => false, 'icon' => null, 'last' => false])

<li @class(['me-2' => !$last]) role="presentation">
    <button
        @class([
            'inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg transition-colors',
            'text-red-600 border-red-500 hover:text-red-700' => $hasError,
        ])
        id="{{ $tab }}-tab"
        data-tabs-target="#{{ $tab }}"
        type="button"
        role="tab"
        aria-controls="{{ $tab }}"
        aria-selected="{{ $active === $tab ? 'true' : 'false' }}"
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
