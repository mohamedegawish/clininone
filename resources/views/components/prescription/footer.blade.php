@props(['clinic', 'date' => null])

@php
    $color  = $clinic->primaryColor();
    $hex    = ltrim($color, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $dark   = sprintf('#%02x%02x%02x', max(0, $r - 50), max(0, $g - 50), max(0, $b - 50));
    $light  = sprintf('#%02x%02x%02x', min(255, $r + 55), min(255, $g + 55), min(255, $b + 55));
    $phone  = $clinic->phone ?? '01007056015';
@endphp

<div class="rxp-footer" style="background:linear-gradient(135deg,{{ $dark }} 0%,{{ $color }} 50%,{{ $light }} 100%);">

    {{-- Signature --}}
    <div class="rxp-footer-sig">
        @if($clinic->logoUrl())
            <img src="{{ $clinic->logoUrl() }}" class="rxp-footer-logo" alt="{{ $clinic->name }}">
        @endif
        <div class="rxp-sig-line"></div>
        <div class="rxp-sig-lbl">Doctor's Signature &amp; Clinic Stamp</div>
    </div>

    {{-- Center --}}
    <div class="rxp-footer-center">
        <div class="rxp-getwell">Get Well Soon &#10024;</div>
        <div class="rxp-footer-clinic-name">{{ $clinic->name }}</div>
    </div>

    {{-- Right: Contact + Date --}}
    <div class="rxp-footer-right">
        @if($phone)
            <div class="rxp-genline">&#9742; {{ $phone }}</div>
        @endif
        @if($date)
            <div class="rxp-genline">{{ $date }}</div>
        @endif
    </div>

</div>
