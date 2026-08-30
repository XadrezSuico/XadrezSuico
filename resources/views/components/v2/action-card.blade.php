@php
    $href = $href ?? '#';
    $label = $label ?? '';
    $description = $description ?? null;
    $target = $target ?? null;
    $disabled = $disabled ?? false;
    $variant = $variant ?? 'default';
    $class = $class ?? '';

    $variantClass = [
        'default' => '',
        'success' => 'v2-action-card--success',
        'warning' => 'v2-action-card--warning',
        'primary' => 'v2-action-card--primary',
    ][$variant] ?? '';

    $className = trim('v2-action-card ' . $variantClass . ($disabled ? ' is-disabled' : '') . ' ' . $class);
@endphp

@if($disabled)
    <div class="{{ $className }}">
        @if(!empty($icon))
            <span class="v2-action-card__icon">{!! $icon !!}</span>
        @endif
        <span class="v2-action-card__label">{{ $label }}</span>
        @if($description)
            <span class="mt-1 text-xs font-normal text-gray-500">{{ $description }}</span>
        @endif
    </div>
@else
    <a href="{{ $href }}"
       class="{{ $className }}"
       @if($target) target="{{ $target }}" rel="noopener noreferrer" @endif>
        @if(!empty($icon))
            <span class="v2-action-card__icon">{!! $icon !!}</span>
        @endif
        <span class="v2-action-card__label">{{ $label }}</span>
        @if($description)
            <span class="mt-1 text-xs font-normal text-gray-500">{{ $description }}</span>
        @endif
    </a>
@endif
