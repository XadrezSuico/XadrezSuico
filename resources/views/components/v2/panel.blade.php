<div class="overflow-hidden rounded-xl border border-purple-100 bg-white shadow-sm">
    @if(!empty($title))
        <div class="border-b border-purple-100 bg-brand-surface px-4 py-3 sm:px-6">
            <h3 class="text-base font-semibold text-brand-dark">{{ $title }}</h3>
        </div>
    @endif
    <div class="p-4 sm:p-6">
        {!! $slot ?? '' !!}
    </div>
</div>
