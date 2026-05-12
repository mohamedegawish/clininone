@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'true',
])

<div {{ $attributes->class('soft-card overflow-hidden') }}>
    @if($title)
        <div class="p-3 border-bottom">
            <h6 class="fw-bold mb-0">{{ $title }}</h6>
            @if($subtitle)
                <small class="text-muted">{{ $subtitle }}</small>
            @endif
        </div>
    @endif
    <div class="{{ $padding === 'false' ? '' : 'p-3' }}">
        {{ $slot }}
    </div>
</div>
