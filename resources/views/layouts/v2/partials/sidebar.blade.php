@php
    $menuItems = (new \App\Support\NavigationMenuBuilder())->build();
    $logoName = config('xadrezsuico.name', 'Sistema');
    $logoMini = config('xadrezsuico.name_mini', 'Sys');
    $companyHtml = config('xadrezsuico.company_user_html', '');
@endphp

<aside id="v2-sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transform bg-brand-sidebar text-white transition-transform duration-200 lg:translate-x-0">
    <div class="flex h-16 items-center border-b border-white/10 px-4">
        <a href="{{ url('/home') }}" class="truncate text-lg font-semibold text-white">
            <span class="lg:hidden">{{ $logoMini }}</span>
            <span class="hidden lg:inline">{{ $logoName }}</span>
            @if($companyHtml)
                <span class="block text-xs font-normal text-purple-200">{!! strip_tags($companyHtml) !!}</span>
            @endif
        </a>
    </div>

    <nav class="h-[calc(100vh-4rem)] overflow-y-auto px-3 py-4">
        <ul class="space-y-1">
            @foreach($menuItems as $item)
                @if(is_array($item) && ($item['type'] ?? null) === 'header')
                    <li class="px-3 pb-1 pt-4 text-xs font-semibold uppercase tracking-wide text-purple-200">
                        {{ $item['label'] }}
                    </li>
                @elseif(is_array($item) && ($item['type'] ?? null) === 'link')
                    @php
                        $isActive = request()->is(ltrim($item['url'], '/') . '*') || request()->is(ltrim($item['url'], '/'));
                    @endphp
                    <li>
                        <a href="{{ url($item['url']) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ $isActive ? 'bg-white/15 font-semibold text-white' : 'text-purple-100 hover:bg-white/10 hover:text-white' }}">
                            @include('layouts.v2.partials.icon', ['name' => $item['icon'] ?? 'link'])
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
</aside>
