@php
    $tabs = $tabs ?? [];
    $variant = $variant ?? 'underline';
@endphp

<nav class="mb-6 overflow-x-auto border-b border-purple-100" aria-label="Abas">
    <ul class="-mb-px flex min-w-max gap-1">
        @foreach($tabs as $tab)
            <li>
                <a href="{{ $tab['url'] ?? '#' }}"
                   id="tab_{{ $tab['id'] ?? '' }}"
                   class="inline-block whitespace-nowrap rounded-t-lg px-3 py-2 text-sm font-medium transition {{ !empty($tab['active']) ? 'border-b-2 border-brand bg-brand-surface text-brand-dark' : 'text-gray-500 hover:border-b-2 hover:border-purple-200 hover:text-brand' }}"
                   @if(!empty($tab['active'])) aria-current="page" @endif>
                    {{ $tab['label'] ?? '' }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
