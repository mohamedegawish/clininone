@props([
    'clinic',
    'doctorName',
    'doctorSpec'     => 'Specialist Physician',
    'doctorPhone'    => '',
    'doctorInitials' => 'Dr',
    'doctorPhoto'    => null,
])

@php
    $color  = $clinic->primaryColor();
    $hex    = ltrim($color, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $dark   = sprintf('#%02x%02x%02x', max(0, $r - 50), max(0, $g - 50), max(0, $b - 50));
    $light  = sprintf('#%02x%02x%02x', min(255, $r + 55), min(255, $g + 55), min(255, $b + 55));
    $a20    = "rgba($r,$g,$b,0.20)";
    $phone  = $clinic->phone ?? '01007056015';
    $addr   = $clinic->address ?? '';
@endphp

<div class="rxp-header" style="background:linear-gradient(135deg,{{ $dark }} 0%,{{ $color }} 50%,{{ $light }} 100%);">

    {{-- LEFT: Logo + Clinic Info --}}
    <div class="rxp-brand">
        @if($clinic->logoUrl())
            <img src="{{ $clinic->logoUrl() }}" class="rxp-logo-img" alt="{{ $clinic->name }}">
        @else
            <div class="rxp-logo" style="background:{{ $a20 }};border-color:rgba(255,255,255,0.35);">Rx</div>
        @endif
        <div class="rxp-brand-text">
            <div class="rxp-clinic-name">{{ $clinic->name }}</div>
            <div class="rxp-clinic-sub">Medical Center &amp; Specialized Care</div>
            @if($phone)
                <div class="rxp-clinic-phone">&#9742; {{ $phone }}</div>
            @endif
        </div>
    </div>

    <div class="rxp-hdr-divider"></div>

    {{-- RIGHT: Doctor Info --}}
    <div class="rxp-doc-section">
        @if($doctorPhoto)
            <img src="{{ asset('storage/' . $doctorPhoto) }}" class="rxp-doc-img" alt="{{ $doctorName }}">
        @else
            <div class="rxp-doc-avatar" style="background:{{ $a20 }};border-color:rgba(255,255,255,0.4);">
                {{ $doctorInitials }}
            </div>
        @endif
        <div class="rxp-doc-info">
            <div class="rxp-doc-name">{{ $doctorName }}</div>
            <div class="rxp-doc-spec">{{ $doctorSpec }}</div>
            @if($doctorPhone)
                <div class="rxp-doc-phone">&#9742; {{ $doctorPhone }}</div>
            @endif
            @if($addr)
                <div class="rxp-doc-addr">&#128205; {{ $addr }}</div>
            @endif
        </div>
    </div>

</div>

{{-- Accent line --}}
<div class="rxp-accent-line" style="background:linear-gradient(90deg,{{ $dark }},{{ $light }},{{ $dark }});"></div>
