@extends('layouts.app')
@section('title', __('clinic.consultations.details_title'))
@section('content')

{{-- ══════════════════ PROFESSIONAL PRESCRIPTION PRINT ══════════════════ --}}
@php
    $rxColor   = $clinic->primaryColor();
    $hex       = ltrim($rxColor, '#');
    $rC = hexdec(substr($hex,0,2)); $gC = hexdec(substr($hex,2,2)); $bC = hexdec(substr($hex,4,2));
    $rxAlpha10 = "rgba($rC,$gC,$bC,0.10)";
    $rxAlpha20 = "rgba($rC,$gC,$bC,0.20)";
    $rxAlpha04 = "rgba($rC,$gC,$bC,0.04)";
    $doc       = $consultation->doctor;
    $docName   = 'Dr. ' . ($doc->name ?? auth()->user()->name);
    $docSpec   = $doc->specialty ?? 'Specialist Physician';
    $docPhone  = $doc->phone ?? '';
    $docPhoto  = $doc->photo_path ?? null;
    $docInit   = mb_substr($doc->name ?? auth()->user()->name, 0, 2);
@endphp
<div id="RX_PRINT">
<div class="rxp-page">

    <x-prescription.header
        :clinic="$clinic"
        :doctorName="$docName"
        :doctorSpec="$docSpec"
        :doctorPhone="$docPhone"
        :doctorPhoto="$docPhoto"
        :doctorInitials="$docInit"
    />

    {{-- ── PATIENT STRIP ── --}}
    <div class="rxp-patient-strip" style="background:{{ $rxAlpha10 }};border-color:{{ $rxAlpha20 }};">
        <div class="rxp-pcell" style="border-color:{{ $rxAlpha20 }};">
            <div class="rxp-pcell-lbl" style="color:{{ $rxColor }};">Patient Name</div>
            <div class="rxp-pcell-val">{{ $consultation->patient->full_name }}</div>
        </div>
        <div class="rxp-pcell" style="border-color:{{ $rxAlpha20 }};">
            <div class="rxp-pcell-lbl" style="color:{{ $rxColor }};">Age / Gender</div>
            <div class="rxp-pcell-val">
                {{ $consultation->patient->birth_date
                    ? \Carbon\Carbon::parse($consultation->patient->birth_date)->age . ' yrs'
                    : ($consultation->patient->age ?? '—') }}
                / {{ ucfirst($consultation->patient->gender ?? '—') }}
            </div>
        </div>
        <div class="rxp-pcell" style="border-color:{{ $rxAlpha20 }};">
            <div class="rxp-pcell-lbl" style="color:{{ $rxColor }};">Visit Date</div>
            <div class="rxp-pcell-val">{{ $consultation->created_at->format('d M Y') }}</div>
        </div>
        <div class="rxp-pcell rxp-pcell-last">
            <div class="rxp-pcell-lbl" style="color:{{ $rxColor }};">Reference No.</div>
            <div class="rxp-pcell-val">#{{ str_pad($consultation->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="rxp-body">

        {{-- Vitals --}}
        @php
            $vitals = array_filter([
                'BP'     => $consultation->bp,
                'Temp'   => $consultation->temp   ? $consultation->temp   . ' °C'  : null,
                'Pulse'  => $consultation->pulse  ? $consultation->pulse  . ' bpm' : null,
                'HR'     => $consultation->hr     ? $consultation->hr     . ' bpm' : null,
                'RR'     => $consultation->rr     ? $consultation->rr     . ' /min': null,
                'SpO2'   => $consultation->spo2   ? $consultation->spo2   . ' %'   : null,
                'Weight' => $consultation->weight ? $consultation->weight . ' kg'  : null,
                'Height' => $consultation->height ? $consultation->height . ' cm'  : null,
            ]);
        @endphp
        @if(count($vitals))
        <div class="rxp-vitals-section" style="border-color:{{ $rxAlpha20 }};">
            <div class="rxp-section-ttl" style="color:{{ $rxColor }};">&#9877; Vital Signs</div>
            <div class="rxp-vitals-chips">
                @foreach($vitals as $label => $val)
                <span class="rxp-chip" style="background:{{ $rxAlpha10 }};border-color:{{ $rxAlpha20 }};color:{{ $rxColor }};font-size:12px;padding:3px 12px;">
                    {{ $label }}: <b style="margin-inline-start:3px;">{{ $val }}</b>
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Prescription Header --}}
        <div class="rxp-rx-header-row">
            <span class="rxp-rx-glyph" style="color:{{ $rxColor }};">&#8478;</span>
            <span class="rxp-rx-word">Prescription</span>
            <div class="rxp-rx-line" style="background:linear-gradient(90deg,{{ $rxAlpha20 }},transparent);"></div>
        </div>

        {{-- Medications --}}
        @forelse($consultation->medicationRecords as $idx => $med)
        <div class="rxp-med-card" style="border-inline-start-color:{{ $rxColor }};">
            <div class="rxp-med-circle" style="background:{{ $rxColor }};">{{ $idx + 1 }}</div>
            <div class="rxp-med-inner">
                <div class="rxp-med-title">{{ $med->name }}</div>
                <div class="rxp-med-chips">
                    @if($med->dosage || $med->generic)
                    <span class="rxp-chip" style="background:{{ $rxAlpha10 }};border-color:{{ $rxAlpha20 }};color:{{ $rxColor }};">&#128138; {{ $med->dosage ?: $med->generic }}</span>
                    @endif
                    @if($med->frequency)
                    <span class="rxp-chip" style="background:{{ $rxAlpha10 }};border-color:{{ $rxAlpha20 }};color:{{ $rxColor }};">&#9201; {{ $med->frequency }}</span>
                    @endif
                    @if($med->duration)
                    <span class="rxp-chip" style="background:{{ $rxAlpha10 }};border-color:{{ $rxAlpha20 }};color:{{ $rxColor }};">&#128197; {{ $med->duration }}</span>
                    @endif
                    @if($med->route)
                    <span class="rxp-chip" style="background:{{ $rxAlpha10 }};border-color:{{ $rxAlpha20 }};color:{{ $rxColor }};">{{ $med->route }}</span>
                    @endif
                </div>
                @if($med->instructions)
                <div class="rxp-med-note">{{ $med->instructions }}</div>
                @endif
            </div>
        </div>
        @empty
        <div class="rxp-no-meds">No medications prescribed.</div>
        @endforelse

    </div>

    <x-prescription.footer :clinic="$clinic" :date="$consultation->created_at->format('d M Y')" />

    {{-- ── WATERMARK ── --}}
    <div class="rxp-wm" style="color:{{ $rxAlpha04 }};">&#8478;</div>

</div>
</div>

{{-- ═══════════════ MAIN VIEW (non-print) ═══════════════ --}}

{{-- Consultation Header --}}
<div class="csh-header d-print-none">
    <div class="csh-left">
        <div class="csh-avatar">{{ mb_substr($consultation->patient->full_name,0,2) }}</div>
        <div>
            <div class="csh-eyebrow">
                <i class="ph-fill ph-stethoscope"></i>
                {{ __('clinic.consultations.details_report') }}
                &nbsp;·&nbsp; <span style="font-weight:700; color:var(--clr-n-400);">#{{ str_pad($consultation->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="csh-name">{{ $consultation->patient->full_name }}</h1>
            <div class="csh-meta">
                <span><i class="ph-bold ph-calendar-check"></i> {{ $consultation->created_at->format('d M Y') }}</span>
                <span><i class="ph-bold ph-clock"></i> {{ $consultation->created_at->format('h:i A') }}</span>
                @if($consultation->doctor)
                <span><i class="ph-bold ph-user-circle"></i> Dr. {{ $consultation->doctor->name }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="csh-actions">
        <button onclick="window.print()" class="btn btn-accent">
            <i class="ph-bold ph-printer"></i>
            <span>{{ __('clinic.consultations.print') }}</span>
        </button>
        <a href="{{ route('clinic.patients.show', $consultation->patient_id) }}" class="btn btn-ghost">
            <i class="ph-bold ph-arrow-left"></i>
            <span>{{ __('clinic.consultations.patient_file') }}</span>
        </a>
    </div>
</div>

<div class="csh-layout d-print-none">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="csh-sidebar">

        {{-- Patient Profile --}}
        <div class="card csh-patient-card">
            <div class="card-body">
                <div class="csh-patient-top">
                    <div class="csh-p-avatar">{{ mb_substr($consultation->patient->full_name,0,2) }}</div>
                </div>
                <div class="csh-p-name">{{ $consultation->patient->full_name }}</div>
                @if($consultation->patient->english_name)
                <div class="csh-p-en">{{ $consultation->patient->english_name }}</div>
                @endif

                <div class="csh-p-stats">
                    <div class="csh-stat-row">
                        <span class="csh-stat-lbl">{{ __('clinic.consultations.label_age') }}</span>
                        <span class="csh-stat-val">
                            {{ $consultation->patient->birth_date
                                ? \Carbon\Carbon::parse($consultation->patient->birth_date)->age . ' ' . __('clinic.patients.years')
                                : ($consultation->patient->age ?: '—') }}
                        </span>
                    </div>
                    <div class="csh-stat-row">
                        <span class="csh-stat-lbl">{{ __('clinic.consultations.label_gender') }}</span>
                        <span class="csh-stat-val">{{ ucfirst($consultation->patient->gender ?? '—') }}</span>
                    </div>
                    <div class="csh-stat-row">
                        <span class="csh-stat-lbl">{{ __('clinic.consultations.label_blood_group') }}</span>
                        <span class="badge badge-primary" style="font-size:12px;">{{ $consultation->patient->blood_group ?: '—' }}</span>
                    </div>
                    @if($consultation->patient->phone)
                    <div class="csh-stat-row">
                        <span class="csh-stat-lbl">Phone</span>
                        <span class="csh-stat-val" dir="ltr">{{ $consultation->patient->phone }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Vital Signs --}}
        @if(count($vitals))
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ph-fill ph-heartbeat" style="color:var(--clr-danger-400);font-size:18px;"></i>
                    {{ __('clinic.consultations.vital_signs') }}
                </h3>
            </div>
            <div class="card-body csh-vitals-grid">
                @foreach($vitals as $label => $val)
                <div class="csh-vital-chip">
                    <div class="csh-vital-label">{{ $label }}</div>
                    <div class="csh-vital-val">{{ $val }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Consultation Date Summary --}}
        <div class="csh-date-card">
            <div class="csh-date-icon"><i class="ph-fill ph-calendar-check"></i></div>
            <div>
                <div class="csh-date-label">{{ __('clinic.consultations.date') }}</div>
                <div class="csh-date-val">{{ $consultation->created_at->format('d M Y') }}</div>
                <div class="csh-date-time">{{ $consultation->created_at->format('h:i A') }}</div>
            </div>
        </div>

    </aside>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <div class="csh-main">

        {{-- Diagnosis Block --}}
        <div class="card csh-dx-card">
            <div class="card-header">
                <div class="csh-section-icon csh-icon-dx"><i class="ph-bold ph-stethoscope"></i></div>
                <h3 class="card-title">{{ __('clinic.consultations.clinical_diagnosis') }}</h3>
            </div>
            <div class="card-body">
                <div class="csh-dx-display">
                    <div class="csh-dx-badge">Dx</div>
                    <div class="csh-dx-text">{{ $consultation->diagnosis }}</div>
                </div>

                @if($consultation->symptoms)
                <div class="csh-symptoms-block">
                    <div class="csh-block-title">
                        <i class="ph-bold ph-clipboard-text"></i>
                        {{ __('clinic.consultations.symptoms_complaints') }}
                    </div>
                    <div class="csh-symptoms-text">{{ $consultation->symptoms }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Medication Cards --}}
        <div class="card">
            <div class="card-header">
                <div class="csh-section-icon csh-icon-rx"><span style="font-style:italic;font-weight:900;font-size:18px;">℞</span></div>
                <h3 class="card-title">{{ __('clinic.consultations.medication_plan') }}</h3>
                <span class="badge badge-accent dot" style="margin-inline-start:auto;">
                    {{ $consultation->medicationRecords->count() }} {{ __('clinic.consultations.med_medication') }}
                </span>
            </div>
            <div class="card-body" style="padding:16px;">
                @forelse($consultation->medicationRecords as $idx => $med)
                <div class="csh-med-card">
                    <div class="csh-med-num">{{ $idx + 1 }}</div>
                    <div class="csh-med-body">
                        <div class="csh-med-name">{{ $med->name }}</div>
                        <div class="csh-med-chips">
                            @if($med->dosage || $med->generic)
                            <span class="csh-chip"><i class="ph-bold ph-pill"></i> {{ $med->dosage ?: $med->generic }}</span>
                            @endif
                            @if($med->frequency)
                            <span class="csh-chip"><i class="ph-bold ph-clock"></i> {{ $med->frequency }}</span>
                            @endif
                            @if($med->duration)
                            <span class="csh-chip"><i class="ph-bold ph-calendar-blank"></i> {{ $med->duration }}</span>
                            @endif
                            @if($med->route)
                            <span class="csh-chip csh-chip-route"><i class="ph-bold ph-arrow-right"></i> {{ $med->route }}</span>
                            @endif
                        </div>
                        @if($med->instructions)
                        <div class="csh-med-instructions">
                            <i class="ph-bold ph-note" style="font-size:11px;"></i>
                            {{ $med->instructions }}
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="csh-meds-empty">
                    <i class="ph-fill ph-pill"></i>
                    <span>{{ __('clinic.consultations.no_medications') }}</span>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Private Notes (doctor-only) --}}
        @if($consultation->notes)
        <div class="card csh-notes-card">
            <div class="card-header">
                <div class="csh-section-icon csh-icon-notes"><i class="ph-bold ph-lock"></i></div>
                <h3 class="card-title" style="color:var(--clr-danger-600);">{{ __('clinic.consultations.private_notes_card') }}</h3>
                <span class="badge badge-neutral" style="margin-inline-start:auto;font-size:11px;">
                    <i class="ph-bold ph-eye-slash" style="font-size:10px;"></i> Not Printed
                </span>
            </div>
            <div class="card-body">
                <div class="csh-notes-text">{{ $consultation->notes }}</div>
                <div class="csh-notes-hint">* {{ __('clinic.consultations.private_note_hint') }}</div>
            </div>
        </div>
        @endif

    </div>{{-- /csh-main --}}
</div>{{-- /csh-layout --}}

<style>
/* ══ PRESCRIPTION PRINT CSS ══ */
#RX_PRINT{display:none;}
.rxp-page{font-family:'Segoe UI','Helvetica Neue',Arial,sans-serif;max-width:800px;margin:0 auto;background:#fff;position:relative;overflow:hidden;color:#1a202c;line-height:1.55;}
.rxp-header{background:linear-gradient(135deg,#0a2a6e 0%,#1040a0 30%,#1a56c8 60%,#2d70e0 85%,#4285f4 100%);padding:22px 30px;display:flex;justify-content:space-between;align-items:center;}
.rxp-brand{display:flex;align-items:center;gap:15px;}
/* Header layout */
.rxp-header{padding:20px 28px;display:flex;justify-content:space-between;align-items:stretch;gap:20px;}
.rxp-brand{display:flex;align-items:center;gap:14px;flex:1;}
.rxp-brand-text{display:flex;flex-direction:column;gap:2px;}
.rxp-logo{width:56px;height:56px;border:2px solid rgba(255,255,255,0.35);color:#fff;font-size:22px;font-weight:900;font-style:italic;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 6px 20px rgba(0,0,0,0.2);letter-spacing:-1px;}
.rxp-logo-img{width:56px;height:56px;border-radius:14px;object-fit:contain;background:#fff;padding:4px;box-shadow:0 4px 14px rgba(0,0,0,0.2);}
.rxp-clinic-name{font-size:21px;font-weight:800;color:#fff;letter-spacing:-.02em;line-height:1.2;}
.rxp-clinic-sub{font-size:9.5px;color:rgba(255,255,255,0.65);text-transform:uppercase;letter-spacing:.08em;}
.rxp-clinic-phone{font-size:11px;color:rgba(255,255,255,0.9);font-weight:600;margin-top:4px;letter-spacing:.02em;}
.rxp-hdr-divider{width:1px;background:rgba(255,255,255,0.25);margin:4px 0;flex-shrink:0;}
.rxp-doc-section{display:flex;align-items:center;gap:12px;flex-direction:row-reverse;text-align:end;}
.rxp-doc-avatar{width:52px;height:52px;border-radius:50%;border:2px solid rgba(255,255,255,0.4);color:#fff;font-size:17px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.rxp-doc-info{display:flex;flex-direction:column;gap:2px;}
.rxp-doc-name{font-size:16px;font-weight:700;color:#fff;}
.rxp-doc-spec{font-size:10.5px;color:rgba(255,255,255,0.75);}
.rxp-doc-phone{font-size:11px;color:rgba(255,255,255,0.9);font-weight:600;margin-top:3px;}
.rxp-doc-addr{font-size:10px;color:rgba(255,255,255,0.7);margin-top:2px;}
/* Accent line */
.rxp-accent-line{height:3px;}
/* Patient strip */
.rxp-patient-strip{display:grid;grid-template-columns:repeat(4,1fr);border-bottom:1px solid;}
.rxp-pcell{padding:11px 16px;border-inline-end:1px solid;}
.rxp-pcell-last{border-inline-end:none;}
.rxp-pcell-lbl{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;}
.rxp-pcell-val{font-size:13px;font-weight:700;color:#1a202c;}
/* Body */
.rxp-body{padding:18px 28px;}
.rxp-vitals-section{margin-bottom:16px;padding-bottom:14px;border-bottom:1px dashed;}
.rxp-section-ttl{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;display:flex;align-items:center;gap:6px;margin-bottom:10px;}
.rxp-vitals-chips{display:flex;flex-wrap:wrap;gap:7px;}
.rxp-rx-header-row{display:flex;align-items:center;gap:10px;margin-bottom:14px;margin-top:4px;}
.rxp-rx-glyph{font-size:40px;font-weight:900;font-style:italic;line-height:1;}
.rxp-rx-word{font-size:13px;font-weight:800;color:#1a202c;text-transform:uppercase;letter-spacing:.08em;}
.rxp-rx-line{flex:1;height:2px;margin-inline-start:4px;}
/* Med cards */
.rxp-med-card{display:flex;gap:14px;padding:12px 15px;margin-bottom:10px;background:#f8faff;border:1px solid #e2e8f0;border-inline-start-width:5px;border-radius:0 10px 10px 0;}
.rxp-med-card:nth-child(even){background:#f0f4ff;}
.rxp-med-circle{width:30px;height:30px;color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0;margin-top:1px;box-shadow:0 2px 8px rgba(0,0,0,.2);}
.rxp-med-inner{flex:1;}
.rxp-med-title{font-size:15px;font-weight:800;color:#0f172a;text-transform:uppercase;letter-spacing:.02em;margin-bottom:7px;}
.rxp-med-chips{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:5px;}
.rxp-chip{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:2px 10px;border-radius:20px;border:1px solid;}
.rxp-med-note{font-size:11px;color:#5a7a8e;font-style:italic;margin-top:5px;}
.rxp-no-meds{text-align:center;padding:24px;color:#94a3b8;font-style:italic;font-size:13px;}
/* Footer */
.rxp-footer{display:flex;justify-content:space-between;align-items:center;padding:16px 28px;margin-top:0;}
.rxp-footer-sig{flex:1;}
.rxp-sig-line{width:180px;border-bottom:1px solid rgba(255,255,255,0.5);margin-bottom:7px;}
.rxp-sig-lbl{font-size:9.5px;color:rgba(255,255,255,0.75);font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
.rxp-footer-center{text-align:center;flex:1;}
.rxp-getwell{font-size:16px;font-weight:800;color:#fff;letter-spacing:.01em;}
.rxp-footer-right{text-align:end;flex:1;}
.rxp-footer-logo{height:36px;opacity:0.85;margin-bottom:6px;border-radius:6px;background:#fff;padding:3px;}
.rxp-footer-clinic-name{font-size:13px;font-weight:700;color:#fff;margin-top:4px;}
.rxp-doc-img{width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.5);}
.rxp-genline{font-size:10.5px;color:rgba(255,255,255,0.8);line-height:1.6;}
.rxp-wm{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-22deg);font-size:300px;font-weight:900;font-style:italic;pointer-events:none;user-select:none;z-index:0;line-height:1;}

/* ════════════════════════════════
   CONSULTATION SHOW — UI STYLES
════════════════════════════════ */
.csh-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:24px;padding:20px 24px;background:#fff;border-radius:var(--radius-lg);border:1px solid var(--clr-n-200);box-shadow:0 2px 8px -2px rgba(0,0,0,.06);}
.csh-left{display:flex;align-items:center;gap:16px;}
.csh-actions{display:flex;gap:8px;align-items:center;}
.csh-avatar{width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,var(--clr-primary-500),var(--clr-primary-700));color:#fff;font-size:20px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px -2px rgba(42,127,98,.35);}
.csh-eyebrow{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--clr-primary-500);margin-bottom:2px;display:flex;align-items:center;gap:5px;}
.csh-name{font-size:22px;font-weight:800;color:var(--clr-n-900);margin:0 0 6px;}
.csh-meta{display:flex;align-items:center;flex-wrap:wrap;gap:12px;font-size:13px;color:var(--clr-n-500);}
.csh-meta span{display:flex;align-items:center;gap:5px;}
.csh-meta i{color:var(--clr-primary-400);}

/* Layout */
.csh-layout{display:grid;grid-template-columns:280px 1fr;gap:24px;align-items:start;}
.csh-sidebar{display:flex;flex-direction:column;gap:20px;position:sticky;top:80px;}
.csh-main{display:flex;flex-direction:column;gap:20px;}

/* Patient card */
.csh-patient-card .card-body{padding-top:24px!important;text-align:center;}
.csh-patient-top{margin-bottom:14px;}
.csh-p-avatar{width:76px;height:76px;border-radius:50%;background:linear-gradient(135deg,var(--clr-primary-400),var(--clr-primary-700));color:#fff;font-size:26px;font-weight:800;display:flex;align-items:center;justify-content:center;margin:0 auto;box-shadow:0 6px 20px -4px rgba(42,127,98,.4);}
.csh-p-name{font-size:16px;font-weight:800;color:var(--clr-n-900);margin-bottom:3px;}
.csh-p-en{font-size:12px;color:var(--clr-n-400);margin-bottom:16px;}
.csh-p-stats{text-align:start;border-top:1px solid var(--clr-n-100);padding-top:14px;display:flex;flex-direction:column;gap:10px;}
.csh-stat-row{display:flex;justify-content:space-between;align-items:center;font-size:13px;}
.csh-stat-lbl{color:var(--clr-n-500);font-weight:500;}
.csh-stat-val{font-weight:700;color:var(--clr-n-800);}

/* Vitals */
.csh-vitals-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px!important;padding-top:12px!important;}
.csh-vital-chip{background:var(--clr-primary-50);border:1px solid var(--clr-primary-100);padding:10px;border-radius:10px;text-align:center;}
.csh-vital-label{font-size:9px;font-weight:800;color:var(--clr-primary-500);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;}
.csh-vital-val{font-size:16px;font-weight:800;color:var(--clr-primary-700);}

/* Date card */
.csh-date-card{display:flex;align-items:center;gap:14px;padding:16px 20px;background:#fff;border-radius:var(--radius-md);border:1px solid var(--clr-n-200);}
.csh-date-icon{width:40px;height:40px;border-radius:12px;background:var(--clr-primary-50);color:var(--clr-primary-500);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.csh-date-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--clr-n-400);margin-bottom:2px;}
.csh-date-val{font-size:15px;font-weight:800;color:var(--clr-n-900);}
.csh-date-time{font-size:12px;color:var(--clr-n-400);}

/* Section icons */
.csh-section-icon{width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.csh-icon-dx{background:var(--clr-primary-50);color:var(--clr-primary-600);}
.csh-icon-rx{background:var(--clr-accent-50,#f0fdf4);color:var(--clr-primary-600);}
.csh-icon-notes{background:var(--clr-danger-50,#fef2f2);color:var(--clr-danger-500);}

/* Diagnosis block */
.csh-dx-display{display:flex;align-items:flex-start;gap:14px;background:var(--clr-primary-50);border:1px solid var(--clr-primary-100);border-inline-start:5px solid var(--clr-primary-500);border-radius:0 12px 12px 0;padding:16px 18px;margin-bottom:20px;}
.csh-dx-badge{background:linear-gradient(135deg,var(--clr-primary-500),var(--clr-primary-700));color:#fff;font-size:13px;font-weight:900;font-style:italic;padding:5px 14px;border-radius:8px;flex-shrink:0;box-shadow:0 3px 10px -2px rgba(42,127,98,.4);}
.csh-dx-text{font-size:17px;font-weight:700;color:var(--clr-primary-900);line-height:1.4;}
.csh-symptoms-block{background:var(--clr-n-50);border-radius:10px;padding:14px 16px;border:1px solid var(--clr-n-200);}
.csh-block-title{font-size:11px;font-weight:800;color:var(--clr-n-500);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;display:flex;align-items:center;gap:6px;}
.csh-symptoms-text{font-size:14px;color:var(--clr-n-700);line-height:1.7;white-space:pre-wrap;}

/* Medication cards */
.csh-med-card{display:flex;gap:14px;padding:14px 16px;margin-bottom:10px;background:#fafffe;border:1px solid var(--clr-primary-100);border-inline-start:4px solid var(--clr-primary-500);border-radius:0 12px 12px 0;transition:box-shadow .15s;}
.csh-med-card:last-child{margin-bottom:0;}
.csh-med-card:hover{box-shadow:0 4px 16px -4px rgba(42,127,98,.2);}
.csh-med-card:nth-child(even){background:var(--clr-primary-50);border-inline-start-color:var(--clr-primary-600);}
.csh-med-num{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--clr-primary-500),var(--clr-primary-700));color:#fff;font-size:14px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 10px -2px rgba(42,127,98,.4);}
.csh-med-body{flex:1;}
.csh-med-name{font-size:16px;font-weight:800;color:var(--clr-n-900);text-transform:capitalize;margin-bottom:8px;}
.csh-med-chips{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:6px;}
.csh-chip{display:inline-flex;align-items:center;gap:5px;background:#fff;border:1px solid var(--clr-primary-200);color:var(--clr-primary-700);font-size:12px;font-weight:600;padding:3px 12px;border-radius:20px;}
.csh-chip i{font-size:11px;}
.csh-chip-route{background:var(--clr-n-50);border-color:var(--clr-n-300);color:var(--clr-n-600);}
.csh-med-instructions{font-size:12px;color:var(--clr-n-500);font-style:italic;display:flex;align-items:center;gap:5px;}
.csh-meds-empty{display:flex;align-items:center;gap:10px;padding:32px;color:var(--clr-n-400);justify-content:center;font-size:14px;}
.csh-meds-empty i{font-size:28px;opacity:.4;}

/* Private notes */
.csh-notes-card{border-inline-start:4px solid var(--clr-danger-400);}
.csh-notes-text{font-size:14px;line-height:1.7;white-space:pre-wrap;color:var(--clr-danger-700);background:var(--clr-danger-50,#fef2f2);padding:12px 16px;border-radius:8px;margin-bottom:8px;}
.csh-notes-hint{font-size:11px;color:var(--clr-n-400);font-style:italic;}

@media (max-width: 960px){
    .csh-layout{grid-template-columns:1fr;}
    .csh-sidebar{position:static;}
    .csh-header{flex-direction:column;align-items:flex-start;}
}

/* ── PRINT ── */
@media print {
    * { visibility: hidden !important; }
    #RX_PRINT { display: block !important; position: fixed !important; top: 0; left: 0; right: 0; width: 100%; background: #fff !important; visibility: visible !important; }
    #RX_PRINT * { visibility: visible !important; }
    @page { size: A4 portrait; margin: 10mm 12mm; }
}
</style>
@endsection
