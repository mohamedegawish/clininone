@extends('layouts.app')
@section('title', __('clinic.consultations.start'))
@section('content')

{{-- ══════════════════ PROFESSIONAL PRESCRIPTION PRINT ══════════════════ --}}
@php
    $rxColor   = $clinic->primaryColor();
    $hex       = ltrim($rxColor, '#');
    $rC = hexdec(substr($hex,0,2)); $gC = hexdec(substr($hex,2,2)); $bC = hexdec(substr($hex,4,2));
    $rxAlpha10 = "rgba($rC,$gC,$bC,0.10)";
    $rxAlpha20 = "rgba($rC,$gC,$bC,0.20)";
    $rxAlpha04 = "rgba($rC,$gC,$bC,0.04)";
    $authDoc   = auth()->user()->doctor;
    $docName   = 'Dr. ' . ($authDoc?->name ?? auth()->user()->name);
    $docSpec   = $authDoc?->specialty ?? 'Specialist Physician';
    $docPhone  = auth()->user()->phone ?? $authDoc?->phone ?? '';
    $docPhoto  = $authDoc?->photo_path ?? null;
    $docInit   = mb_substr($authDoc?->name ?? auth()->user()->name, 0, 2);
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
            <div class="rxp-pcell-val">{{ $patient->full_name }}</div>
        </div>
        <div class="rxp-pcell" style="border-color:{{ $rxAlpha20 }};">
            <div class="rxp-pcell-lbl" style="color:{{ $rxColor }};">Age / Gender</div>
            <div class="rxp-pcell-val">
                {{ $patient->birth_date
                    ? \Carbon\Carbon::parse($patient->birth_date)->age . ' yrs'
                    : ($patient->age ?? '—') }}
                / {{ ucfirst($patient->gender ?? '—') }}
            </div>
        </div>
        <div class="rxp-pcell" style="border-color:{{ $rxAlpha20 }};">
            <div class="rxp-pcell-lbl" style="color:{{ $rxColor }};">Visit Date</div>
            <div class="rxp-pcell-val">{{ now()->format('d M Y') }}</div>
        </div>
        <div class="rxp-pcell rxp-pcell-last">
            <div class="rxp-pcell-lbl" style="color:{{ $rxColor }};">Reference No.</div>
            <div class="rxp-pcell-val">#{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="rxp-body">

        {{-- Vitals (JS-populated) --}}
        <div id="rx_vitals_section" class="rxp-vitals-section" style="display:none;border-color:{{ $rxAlpha20 }};">
            <div class="rxp-section-ttl" style="color:{{ $rxColor }};">&#9877; Vital Signs</div>
            <div id="rx_vitals_grid" class="rxp-vitals-chips"></div>
        </div>

        {{-- Prescription Header --}}
        <div class="rxp-rx-header-row">
            <span class="rxp-rx-glyph" style="color:{{ $rxColor }};">&#8478;</span>
            <span class="rxp-rx-word">Prescription</span>
            <div class="rxp-rx-line" style="background:linear-gradient(90deg,{{ $rxAlpha20 }},transparent);"></div>
        </div>

        {{-- Medications (JS-populated) --}}
        <div id="rx_meds_body">
            <div class="rxp-no-meds">No medications prescribed</div>
        </div>

    </div>

    <x-prescription.footer :clinic="$clinic" :date="now()->format('d M Y')" />

    {{-- ── WATERMARK ── --}}
    <div class="rxp-wm" style="color:{{ $rxAlpha04 }};">&#8478;</div>

</div>
</div>

{{-- ═══════════════ MAIN VIEW ═══════════════ --}}

{{-- Consultation Header --}}
<div class="cs-header">
    <div class="cs-header-info">
        <div class="cs-patient-avatar">{{ mb_substr($patient->full_name, 0, 2) }}</div>
        <div>
            <div class="cs-header-label">{{ __('clinic.consultations.start') }}</div>
            <h1 class="cs-header-name">{{ $patient->full_name }}</h1>
            <div class="cs-header-meta">
                @if($patient->birth_date || $patient->age)
                    <span><i class="ph-bold ph-calendar-blank"></i> {{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->age : ($patient->age ?? '—') }} {{ __('clinic.patients.years') }}</span>
                @endif
                @if($patient->gender)
                    <span><i class="ph-bold ph-gender-intersex"></i> {{ ucfirst($patient->gender) }}</span>
                @endif
                @if($patient->blood_group)
                    <span class="cs-blood-badge">{{ $patient->blood_group }}</span>
                @endif
                <span><i class="ph-bold ph-calendar-check"></i> #{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>
    </div>
    <div class="cs-header-actions">
        <button type="button" onclick="handlePrint()" class="btn btn-accent" id="btn-print" style="display:none;">
            <i class="ph-bold ph-printer"></i>
            <span>{{ __('clinic.consultations.print') }}</span>
        </button>
        <a href="{{ route('clinic.queue.show') }}" class="btn btn-ghost">
            <i class="ph-bold ph-arrow-left"></i>
            <span>{{ __('clinic.queue.title') }}</span>
        </a>
    </div>
</div>

<form method="POST" action="{{ route('clinic.consultations.store', $appointment->id) }}" id="form-consultation">
    @csrf
    <div class="cs-layout">

        {{-- ═══ SIDEBAR ═══ --}}
        <aside class="cs-sidebar">

            {{-- Patient Card --}}
            <div class="card cs-patient-card">
                <div class="card-body" style="padding-bottom:20px;">
                    <div class="cs-patient-top">
                        <div class="cs-sidebar-avatar">{{ mb_substr($patient->full_name,0,2) }}</div>
                        <div class="cs-online-dot"></div>
                    </div>
                    <div class="cs-sidebar-name">{{ $patient->full_name }}</div>
                    @if($patient->english_name)
                        <div class="cs-sidebar-en">{{ $patient->english_name }}</div>
                    @endif
                    <div class="cs-stat-chips">
                        <div class="cs-chip">
                            <i class="ph-bold ph-calendar-blank"></i>
                            {{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->age . ' ' . __('clinic.patients.years') : ($patient->age ?? '—') }}
                        </div>
                        <div class="cs-chip">
                            <i class="ph-bold ph-phone"></i>
                            <span dir="ltr">{{ $patient->phone ?? '—' }}</span>
                        </div>
                        @if($patient->blood_group)
                        <div class="cs-chip cs-chip-blood">
                            <i class="ph-fill ph-drop"></i>
                            {{ $patient->blood_group }}
                        </div>
                        @endif
                    </div>
                    @if($patient->medical_history)
                    <div class="cs-history-box">
                        <div class="cs-history-label"><i class="ph-fill ph-note-pencil"></i> {{ __('clinic.patients.medical_history') }}</div>
                        <div class="cs-history-text">{{ $patient->medical_history }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Vital Signs --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ph-fill ph-heartbeat" style="color:var(--clr-danger-400);font-size:18px;"></i>
                        {{ __('clinic.consultations.vital_signs') }}
                    </h3>
                    <span class="badge badge-neutral" style="font-size:10px;">{{ __('clinic.common.no') != 'No' ? 'اختياري' : 'Optional' }}</span>
                </div>
                <div class="card-body" style="padding-top:12px;">
                    <div class="vitals-grid">
                        @foreach([
                            ['bp','BP','mmHg','120/80'],
                            ['temp','Temp','°C','37.0'],
                            ['pulse','Pulse','bpm','72'],
                            ['hr','HR','bpm','75'],
                            ['rr','RR','/min','16'],
                            ['spo2','SpO2','%','98'],
                            ['weight','Weight','kg','70'],
                            ['height','Height','cm','170']
                        ] as [$id, $label, $unit, $placeholder])
                        <div class="vital-wrap">
                            <label class="vital-label">{{ $label }} <span class="vital-unit">({{ $unit }})</span></label>
                            <input type="text" name="{{ $id }}" id="v_{{ $id }}"
                                   class="form-control vital-input"
                                   placeholder="{{ $placeholder }}">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </aside>

        {{-- ═══ MAIN ═══ --}}
        <div class="cs-main">

            {{-- Step 1: Symptoms --}}
            <div class="card cs-step-card">
                <div class="card-header">
                    <div class="cs-step-num">1</div>
                    <h3 class="card-title">{{ __('clinic.consultations.symptoms') }}</h3>
                </div>
                <div class="card-body">
                    <textarea name="symptoms" id="field_symptoms" class="form-control" rows="4"
                              placeholder="Describe symptoms, duration, and patient complaints..."></textarea>
                </div>
            </div>

            {{-- Step 2: Diagnosis --}}
            <div class="card cs-step-card">
                <div class="card-header">
                    <div class="cs-step-num">2</div>
                    <h3 class="card-title">{{ __('clinic.consultations.diagnosis') }}</h3>
                    <span class="badge badge-primary dot" style="margin-inline-start:auto;">Smart Search</span>
                </div>
                <div class="card-body">
                    <div id="box-dx-tags" class="dx-tags-container"></div>
                    <input type="hidden" name="diagnosis" id="field_dx_hidden" required>
                    <div style="position:relative;">
                        <div class="cs-input-wrap">
                            <i class="ph-bold ph-stethoscope cs-input-icon"></i>
                            <input type="text" id="input_dx" class="form-control cs-input-padded"
                                   placeholder="Search diagnosis or type new..." autocomplete="off">
                        </div>
                        <div id="drop_dx" class="dx-dropdown"></div>
                    </div>
                    <p class="form-hint mt-8"><i class="ph-bold ph-keyboard" style="font-size:11px;"></i> Press Enter or click "+ Add" to save a new diagnosis.</p>
                </div>
            </div>

            {{-- Step 3: Medications --}}
            <div class="card cs-step-card">
                <div class="card-header" style="flex-wrap:wrap;gap:10px;">
                    <div class="cs-step-num">3</div>
                    <h3 class="card-title">{{ __('clinic.consultations.treatment') }}</h3>
                    <div class="cs-med-actions">
                        <button type="button" onclick="openLoadTemplate()" class="btn btn-sm btn-ghost">
                            <i class="ph-bold ph-clipboard-text"></i> <span>Load</span>
                        </button>
                        <button type="button" onclick="openSaveTemplate()" class="btn btn-sm btn-ghost">
                            <i class="ph-bold ph-floppy-disk"></i> <span>Save</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addNewMedRow()">
                            <i class="ph-bold ph-plus"></i> <span>{{ __('clinic.consultations.med_medication') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Quick Add --}}
                <div class="cs-quick-add">
                    <i class="ph-fill ph-lightning-a" style="color:var(--clr-primary-500);font-size:18px;flex-shrink:0;"></i>
                    <input type="text" id="quick_add_input" class="form-control" style="font-size:13px;"
                           placeholder='Quick: "Panadol 500mg 2x 5d" → Enter'>
                    <button type="button" onclick="quickAddMedication()" class="btn btn-sm btn-outline" style="white-space:nowrap;flex-shrink:0;">
                        <i class="ph-bold ph-arrow-bend-down-left"></i> Parse
                    </button>
                </div>

                <div style="overflow-x:auto;">
                    <table class="table" id="table_meds" style="margin-bottom:0;min-width:880px;">
                        <thead>
                            <tr>
                                <th style="width:36px;">#</th>
                                <th style="min-width:190px;">{{ __('clinic.consultations.med_medication') }} <span class="text-danger">*</span></th>
                                <th style="min-width:95px;">{{ __('clinic.consultations.med_dosage') }}</th>
                                <th style="min-width:145px;">{{ __('clinic.consultations.med_frequency') }}</th>
                                <th style="min-width:125px;">{{ __('clinic.consultations.med_route') }}</th>
                                <th style="min-width:115px;">{{ __('clinic.consultations.med_duration') }}</th>
                                <th style="min-width:125px;">{{ __('clinic.consultations.med_instructions') }}</th>
                                <th style="width:72px;"></th>
                            </tr>
                        </thead>
                        <tbody id="meds_tbody">
                            <tr id="meds_empty_state">
                                <td colspan="8" class="cs-meds-empty">
                                    <div class="cs-meds-empty-icon"><i class="ph-fill ph-pill"></i></div>
                                    <p class="fw-600 mb-4">{{ __('clinic.consultations.no_medications') }}</p>
                                    <p class="text-sm text-muted m-0">Use "+ {{ __('clinic.consultations.med_medication') }}" or Quick Add bar above</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Step 4: Private Notes --}}
            <div class="card cs-step-card">
                <div class="card-header">
                    <div class="cs-step-num cs-step-muted">4</div>
                    <h3 class="card-title">{{ __('clinic.consultations.private_notes') }}</h3>
                    <span class="badge badge-neutral" style="margin-inline-start:auto;font-size:11px;">
                        <i class="ph-bold ph-lock" style="font-size:10px;"></i> {{ __('clinic.consultations.private_note_hint') }}
                    </span>
                </div>
                <div class="card-body">
                    <textarea name="notes" class="form-control" rows="2"
                              placeholder="Private notes for medical staff only — will not appear on printed prescription..."></textarea>
                </div>
            </div>

            {{-- Submit Area --}}
            <div class="cs-submit-bar">
                <div class="cs-submit-info">
                    <i class="ph-fill ph-info" style="color:var(--clr-primary-500);font-size:20px;flex-shrink:0;"></i>
                    <div>
                        <div class="fw-700" style="font-size:14px;">{{ __('clinic.consultations.finish_btn') }}</div>
                        <div class="text-sm text-muted">Submitting will mark this appointment as <strong>Completed</strong> and lock the record.</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary cs-submit-btn">
                    <i class="ph-bold ph-check-circle" style="font-size:22px;"></i>
                    <span>{{ __('clinic.consultations.finish_btn') }}</span>
                </button>
            </div>

        </div>{{-- /cs-main --}}
    </div>{{-- /cs-layout --}}
</form>

{{-- ═══ MODAL: Save Template ═══ --}}
<div id="modal-template-save" class="rx-modal-overlay" style="display:none;" onclick="if(event.target===this)closeModal('modal-template-save')">
    <div class="rx-modal">
        <div class="rx-modal-header">
            <h3><i class="ph-bold ph-floppy-disk"></i> Save as Template</h3>
            <button type="button" onclick="closeModal('modal-template-save')" class="btn-action"><i class="ph-bold ph-x"></i></button>
        </div>
        <div class="rx-modal-body">
            <label class="form-label">Template Name</label>
            <input type="text" id="template_name_input" class="form-control" placeholder="e.g. Respiratory Infection Standard" maxlength="255">
            <div class="form-hint mt-4">This template saves the current medication rows and is available only to you.</div>
        </div>
        <div class="rx-modal-footer">
            <button type="button" onclick="closeModal('modal-template-save')" class="btn btn-ghost">Cancel</button>
            <button type="button" onclick="confirmSaveTemplate()" class="btn btn-primary">
                <i class="ph-bold ph-floppy-disk"></i> Save Template
            </button>
        </div>
    </div>
</div>

{{-- ═══ MODAL: Load Template ═══ --}}
<div id="modal-template-load" class="rx-modal-overlay" style="display:none;" onclick="if(event.target===this)closeModal('modal-template-load')">
    <div class="rx-modal">
        <div class="rx-modal-header">
            <h3><i class="ph-bold ph-clipboard-text"></i> Load Template</h3>
            <button type="button" onclick="closeModal('modal-template-load')" class="btn-action"><i class="ph-bold ph-x"></i></button>
        </div>
        <div class="rx-modal-body" style="padding:0;">
            <div id="template_list" style="max-height:320px;overflow-y:auto;"></div>
        </div>
        <div class="rx-modal-footer">
            <button type="button" onclick="closeModal('modal-template-load')" class="btn btn-ghost">Cancel</button>
        </div>
    </div>
</div>

{{-- ═══ NOTIFICATION TOAST ═══ --}}
<div id="rx-toast" style="display:none;position:fixed;bottom:24px;right:24px;background:#2A7F62;color:#fff;padding:12px 20px;border-radius:10px;font-size:14px;font-weight:600;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,.2);"></div>

<style>
/* ══════════════════════════════════════
   CONSULTATION PAGE — UI STYLES
══════════════════════════════════════ */

/* ── Page Header ── */
.cs-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:24px;padding:20px 24px;background:#fff;border-radius:var(--radius-lg);border:1px solid var(--clr-n-200);box-shadow:0 2px 8px -2px rgba(0,0,0,.06);}
.cs-header-info{display:flex;align-items:center;gap:16px;}
.cs-header-actions{display:flex;gap:8px;align-items:center;}
.cs-patient-avatar{width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,var(--clr-primary-500),var(--clr-primary-700));color:#fff;font-size:20px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px -2px rgba(42,127,98,.35);}
.cs-header-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--clr-primary-500);margin-bottom:2px;}
.cs-header-name{font-size:22px;font-weight:800;color:var(--clr-n-900);margin:0 0 6px;}
.cs-header-meta{display:flex;align-items:center;flex-wrap:wrap;gap:12px;font-size:13px;color:var(--clr-n-500);}
.cs-header-meta span{display:flex;align-items:center;gap:5px;}
.cs-header-meta i{color:var(--clr-primary-400);}
.cs-blood-badge{background:var(--clr-danger-50,#fef2f2);color:var(--clr-danger-600,#dc2626);border:1px solid var(--clr-danger-200,#fecaca);border-radius:20px;padding:2px 10px;font-size:12px;font-weight:700;}

/* ── Layout ── */
.cs-layout{display:grid;grid-template-columns:280px 1fr;gap:24px;align-items:start;}
.cs-sidebar{display:flex;flex-direction:column;gap:20px;position:sticky;top:80px;}
.cs-main{display:flex;flex-direction:column;gap:20px;}

/* ── Sidebar Patient Card ── */
.cs-patient-card .card-body{padding-top:24px!important;text-align:center;}
.cs-patient-top{position:relative;display:inline-block;margin-bottom:14px;}
.cs-sidebar-avatar{width:76px;height:76px;border-radius:50%;background:linear-gradient(135deg,var(--clr-primary-400),var(--clr-primary-700));color:#fff;font-size:26px;font-weight:800;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 20px -4px rgba(42,127,98,.4);}
.cs-online-dot{position:absolute;bottom:2px;right:2px;width:18px;height:18px;border-radius:50%;background:var(--clr-success-500,#22c55e);border:3px solid #fff;box-shadow:0 0 0 2px rgba(34,197,94,.2);}
.cs-sidebar-name{font-size:16px;font-weight:800;color:var(--clr-n-900);margin-bottom:3px;}
.cs-sidebar-en{font-size:12px;color:var(--clr-n-400);margin-bottom:14px;}
.cs-stat-chips{display:flex;flex-wrap:wrap;justify-content:center;gap:6px;margin-bottom:14px;}
.cs-chip{display:flex;align-items:center;gap:5px;background:var(--clr-n-50);border:1px solid var(--clr-n-200);border-radius:20px;padding:4px 10px;font-size:12px;font-weight:600;color:var(--clr-n-600);}
.cs-chip i{color:var(--clr-primary-500);font-size:12px;}
.cs-chip-blood{background:var(--clr-danger-50,#fef2f2);border-color:var(--clr-danger-200,#fecaca);color:var(--clr-danger-600,#dc2626);}
.cs-chip-blood i{color:var(--clr-danger-500,#ef4444);}
.cs-history-box{text-align:start;background:var(--clr-n-50);border:1px solid var(--clr-n-200);border-radius:10px;padding:12px;margin-top:8px;}
.cs-history-label{font-size:10px;font-weight:700;color:var(--clr-n-500);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;display:flex;align-items:center;gap:5px;}
.cs-history-text{font-size:12px;color:var(--clr-n-600);line-height:1.6;}

/* ── Vitals ── */
.vitals-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.vital-wrap{}
.vital-label{font-size:10px;font-weight:700;color:var(--clr-n-500);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;display:block;}
.vital-unit{font-weight:400;opacity:.65;font-size:9px;}
.vital-input{padding:7px 8px!important;font-size:13px!important;text-align:center;font-weight:600;}

/* ── Step cards ── */
.cs-step-card{}
.cs-step-num{width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--clr-primary-500),var(--clr-primary-700));color:#fff;font-size:13px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 10px -2px rgba(42,127,98,.4);}
.cs-step-muted{background:var(--clr-n-400)!important;box-shadow:none;}

/* ── Diagnosis ── */
.dx-tags-container{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;min-height:4px;}
.tag-dx{display:inline-flex;align-items:center;gap:6px;background:var(--clr-primary-50);color:var(--clr-primary-700);border:1px solid var(--clr-primary-200);border-radius:20px;padding:5px 14px;font-size:12px;font-weight:700;}
.tag-dx button{background:none;border:none;color:var(--clr-primary-400);cursor:pointer;font-size:15px;padding:0;margin-inline-start:4px;line-height:1;transition:color .15s;}
.tag-dx button:hover{color:var(--clr-danger-500);}
.cs-input-wrap{position:relative;}
.cs-input-icon{position:absolute;inset-inline-start:13px;top:50%;transform:translateY(-50%);color:var(--clr-n-400);font-size:16px;pointer-events:none;}
.cs-input-padded{padding-inline-start:40px!important;}
.dx-dropdown{display:none;position:absolute;top:calc(100% + 5px);inset-inline-start:0;inset-inline-end:0;background:#fff;border:1px solid var(--clr-n-200);border-radius:10px;box-shadow:0 12px 30px -6px rgba(0,0,0,.12);z-index:200;max-height:230px;overflow-y:auto;}
.dx-item{padding:10px 16px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--clr-n-100);transition:background .12s;}
.dx-item:last-child{border-bottom:none;}
.dx-item:hover{background:var(--clr-n-50);color:var(--clr-primary-600);}
.dx-item-add{color:var(--clr-primary-600);font-weight:700;background:var(--clr-primary-50);}

/* ── Medications ── */
.cs-med-actions{display:flex;gap:8px;flex-wrap:wrap;margin-inline-start:auto;}
.cs-quick-add{display:flex;gap:10px;align-items:center;padding:12px 20px;background:var(--clr-n-50);border-top:1px solid var(--clr-n-100);border-bottom:1px solid var(--clr-n-100);}
.cs-meds-empty{text-align:center!important;padding:52px 20px!important;color:var(--clr-n-400);}
.cs-meds-empty-icon{font-size:44px;opacity:.3;margin-bottom:14px;}
.med-row-active td{vertical-align:middle;padding:6px 8px;}
.med-drop{position:absolute;top:calc(100% + 3px);left:0;right:0;background:#fff;border:1px solid var(--clr-n-200);border-radius:8px;box-shadow:0 8px 24px -4px rgba(0,0,0,.12);z-index:300;max-height:210px;overflow-y:auto;display:none;}
.med-suggest-item{padding:8px 13px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--clr-n-50);display:flex;align-items:center;gap:7px;transition:background .1s;}
.med-suggest-item:last-child{border-bottom:none;}
.med-suggest-item:hover{background:var(--clr-n-50);}
.med-suggest-item.is-fav{background:#fffbeb;}
.med-suggest-new{color:var(--clr-primary-600);font-weight:600;background:var(--clr-primary-50);}
.med-suggest-new:hover{background:var(--clr-primary-100);}
.med-fav-btn{background:none;border:none;cursor:pointer;padding:4px 6px;border-radius:6px;color:#94a3b8;transition:all .15s;line-height:1;}
.med-fav-btn:hover{color:#f59e0b;background:#fffbeb;}
.med-fav-btn.is-favorite{color:#f59e0b;}

/* ── Submit Bar ── */
.cs-submit-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:20px 24px;background:#fff;border-radius:var(--radius-lg);border:1px solid var(--clr-n-200);box-shadow:0 4px 20px -4px rgba(0,0,0,.08);flex-wrap:wrap;}
.cs-submit-info{display:flex;align-items:center;gap:12px;}
.cs-submit-btn{padding:14px 36px!important;font-size:16px!important;font-weight:700!important;}

/* ── Modals ── */
.rx-modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:1000;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(3px);}
.rx-modal{background:#fff;border-radius:18px;width:480px;max-width:calc(100vw - 32px);box-shadow:0 24px 64px -12px rgba(0,0,0,.3);overflow:hidden;}
.rx-modal-header{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--clr-n-100);}
.rx-modal-header h3{font-size:16px;font-weight:700;margin:0;display:flex;align-items:center;gap:8px;}
.rx-modal-body{padding:22px;}
.rx-modal-footer{display:flex;justify-content:flex-end;gap:8px;padding:16px 22px;border-top:1px solid var(--clr-n-100);}
.template-list-item{display:flex;align-items:center;gap:12px;padding:14px 22px;border-bottom:1px solid var(--clr-n-100);cursor:pointer;transition:background .15s;font-size:14px;}
.template-list-item:hover{background:var(--clr-primary-50);}
.template-list-item i{color:var(--clr-primary-500);}
.template-list-item .arrow{margin-inline-start:auto;color:var(--clr-n-400);}

@media (max-width: 960px){
    .cs-layout{grid-template-columns:1fr;}
    .cs-sidebar{position:static;}
    .cs-header{flex-direction:column;align-items:flex-start;}
}

/* ══ PROFESSIONAL PRESCRIPTION DESIGN ══ */
#RX_PRINT { display: none; }

.rxp-page {
    font-family: 'Segoe UI','Helvetica Neue',Arial,sans-serif;
    max-width: 800px; margin: 0 auto;
    background: #fff; position: relative;
    overflow: hidden; color: #1a202c; line-height: 1.55;
}
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
/* Watermark */
.rxp-wm{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-22deg);font-size:300px;font-weight:900;font-style:italic;pointer-events:none;user-select:none;z-index:0;line-height:1;}

/* ══ PRINT ══ */
@media print {
    * { visibility: hidden !important; }
    #RX_PRINT { display: block !important; position: fixed !important; top: 0; left: 0; right: 0; width: 100%; background: #fff !important; visibility: visible !important; }
    #RX_PRINT * { visibility: visible !important; }
    @page { size: A4 portrait; margin: 10mm 12mm; }
}
</style>

<script>
function esc(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str ?? ''));
    return d.innerHTML;
}

// ═══════════════ DIAGNOSIS ═══════════════
let selectedDx = [];
const availDx = @json($diagnoses->map(fn($d) => ['id' => $d->id, 'name' => $d->name]));

function renderDxTags() {
    const c = document.getElementById('box-dx-tags');
    c.innerHTML = selectedDx.map((d, i) => `
        <div class="tag-dx">
            <i class="ph-bold ph-stethoscope"></i>
            <span>${esc(d.name)}</span>
            <button type="button" onclick="removeDx(${i})">×</button>
        </div>
    `).join('');
    document.getElementById('field_dx_hidden').value = selectedDx.map(d => d.name).join('; ');
    checkPrintButton();
}

function removeDx(i) { selectedDx.splice(i, 1); renderDxTags(); }

function addDx(name, id = null) {
    if (!name.trim() || selectedDx.some(d => d.name.toLowerCase() === name.toLowerCase())) return;
    selectedDx.push({ id, name: name.trim() });
    renderDxTags();
    document.getElementById('input_dx').value = '';
    document.getElementById('drop_dx').style.display = 'none';
}

function ajaxSaveDx(name) {
    fetch("{{ route('clinic.consultations.diagnoses.store') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ name })
    }).then(r => r.json()).then(d => {
        availDx.push({ id: d.id, name: d.name });
        addDx(d.name, d.id);
    });
}

const dxInput = document.getElementById('input_dx');
const dxDrop  = document.getElementById('drop_dx');

dxInput.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    if (!q) { dxDrop.style.display = 'none'; return; }
    const matches = availDx.filter(d => d.name.toLowerCase().includes(q));
    const exact   = availDx.some(d => d.name.toLowerCase() === q);
    let html = matches.map(d => `<div class="dx-item" data-name="${esc(d.name)}" data-id="${d.id}">${esc(d.name)}</div>`).join('');
    if (!exact) html += `<div class="dx-item dx-item-add" data-new="1" data-name="${esc(this.value.trim())}"><i class="ph-bold ph-plus-circle"></i> + Add new: "<b>${esc(this.value.trim())}</b>"</div>`;
    dxDrop.innerHTML = html || '<div class="dx-item text-muted">No matches found</div>';
    dxDrop.style.display = 'block';
});

dxInput.addEventListener('keydown', e => {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    const q = dxInput.value.trim();
    if (!q) return;
    const ex = availDx.find(d => d.name.toLowerCase() === q.toLowerCase());
    if (ex) addDx(ex.name, ex.id); else ajaxSaveDx(q);
});

dxDrop.addEventListener('click', e => {
    const item = e.target.closest('.dx-item');
    if (!item) return;
    if (item.dataset.new) {
        ajaxSaveDx(item.dataset.name);
    } else {
        addDx(item.dataset.name, item.dataset.id ? parseInt(item.dataset.id) : null);
    }
});

document.addEventListener('click', e => {
    if (!e.target.closest('#input_dx') && !e.target.closest('#drop_dx')) dxDrop.style.display = 'none';
});

// ═══════════════ MEDICATION SYSTEM ═══════════════
let medCounter = 0;
const FREQ = ['Once Daily','Twice Daily','Three times Daily','Four times Daily','Every 8 Hours','Every 12 Hours','Before Bedtime','As Needed (PRN)','Once Weekly'];
const ROUTE = ['فم (Oral)','حقن (Injection)','حقن وريدي (IV)','حقن عضلي (IM)','استنشاق (Inhale)','موضعي (Topical)','قطرة (Drops)','تحت اللسان'];
const DUR   = ['3 Days','5 Days','7 Days','10 Days','14 Days','1 Month','3 Months','Ongoing'];

function buildMedRowHTML(index) {
    return `
        <td class="fw-700 text-muted med-row-num" style="width:36px;">—</td>
        <td style="min-width:200px;position:relative;">
            <input type="hidden" name="medications[${index}][medication_id]" id="med_${index}_id" class="med-id-input">
            <input type="text"   name="medications[${index}][name]"          id="med_${index}_name"
                   class="form-control med-name-input" placeholder="Medicine name…" autocomplete="off" required style="font-size:13px;">
            <div class="med-drop" id="med_${index}_drop"></div>
        </td>
        <td style="min-width:100px;">
            <input type="text" name="medications[${index}][dosage]" class="form-control med-dosage-input" placeholder="e.g. 500mg" style="font-size:13px;">
        </td>
        <td style="min-width:150px;">
            <select name="medications[${index}][frequency]" class="form-control med-freq-input" style="font-size:13px;">
                <option value="">— Frequency —</option>
                ${FREQ.map(f => `<option value="${f}">${f}</option>`).join('')}
            </select>
        </td>
        <td style="min-width:130px;">
            <select name="medications[${index}][route]" class="form-control med-route-input" style="font-size:13px;">
                <option value="">— Route —</option>
                ${ROUTE.map(r => `<option value="${r}">${r}</option>`).join('')}
            </select>
        </td>
        <td style="min-width:120px;">
            <select name="medications[${index}][duration]" class="form-control med-dur-input" style="font-size:13px;">
                <option value="">— Duration —</option>
                ${DUR.map(d => `<option value="${d}">${d}</option>`).join('')}
            </select>
        </td>
        <td style="min-width:130px;">
            <input type="text" name="medications[${index}][instructions]" class="form-control med-instr-input" placeholder="e.g. After meals" style="font-size:13px;">
        </td>
        <td style="white-space:nowrap;padding:6px 8px;">
            <button type="button" class="med-fav-btn" id="med_${index}_fav" onclick="toggleFavMed(${index})" title="Favorite">
                <i class="ph-bold ph-star" style="font-size:16px;"></i>
            </button>
            <button type="button" class="btn-action delete" onclick="removeMedRow(${index})" style="padding:4px 6px;">
                <i class="ph-bold ph-x"></i>
            </button>
        </td>`;
}

function addMedRowFromData(index, data) {
    document.getElementById('meds_empty_state').style.display = 'none';
    const tbody = document.getElementById('meds_tbody');
    const tr = document.createElement('tr');
    tr.id        = `med-row-${index}`;
    tr.className = 'med-row-active';
    tr.innerHTML = buildMedRowHTML(index);
    tbody.appendChild(tr);

    if (data.medication_id) document.getElementById(`med_${index}_id`).value   = data.medication_id;
    if (data.name)          document.getElementById(`med_${index}_name`).value = data.name;
    if (data.dosage)        tr.querySelector('.med-dosage-input').value = data.dosage;
    if (data.frequency)     tr.querySelector('.med-freq-input').value   = data.frequency;
    if (data.route)         tr.querySelector('.med-route-input').value  = data.route;
    if (data.duration)      tr.querySelector('.med-dur-input').value    = data.duration;
    if (data.instructions)  tr.querySelector('.med-instr-input').value  = data.instructions;
    if (data.is_favorite)   markFavoriteBtn(index, true);

    initMedAutocomplete(index);
    updateRowNumbers();
    checkPrintButton();
}

function addNewMedRow() {
    addMedRowFromData(medCounter++, {});
}

function removeMedRow(index) {
    const row = document.getElementById(`med-row-${index}`);
    if (row) row.remove();
    if (!document.querySelectorAll('tr.med-row-active').length) {
        document.getElementById('meds_empty_state').style.display = 'table-row';
    }
    updateRowNumbers();
    checkPrintButton();
}

function updateRowNumbers() {
    document.querySelectorAll('tr.med-row-active').forEach((row, i) => {
        const cell = row.querySelector('.med-row-num');
        if (cell) cell.textContent = i + 1;
    });
}

// ── Autocomplete ──
function initMedAutocomplete(index) {
    const nameInput = document.getElementById(`med_${index}_name`);
    const drop      = document.getElementById(`med_${index}_drop`);
    let debounce;

    nameInput.addEventListener('input', function () {
        document.getElementById(`med_${index}_id`).value = '';
        markFavoriteBtn(index, false);
        const q = this.value.trim();
        clearTimeout(debounce);
        if (q.length < 1) { drop.style.display = 'none'; return; }
        debounce = setTimeout(() => fetchMedSuggestions(q, index), 250);
    });

    nameInput.addEventListener('blur', function () {
        setTimeout(() => {
            drop.style.display = 'none';
            const name  = this.value.trim();
            const medId = document.getElementById(`med_${index}_id`).value;
            if (!name || medId) return;
            autoCreateMedication(name, index);
        }, 200);
    });

    nameInput.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') drop.style.display = 'none';
    });

    drop.addEventListener('mousedown', e => {
        e.preventDefault();
        const item = e.target.closest('.med-suggest-item');
        if (!item) return;
        if (item.dataset.medNew) {
            createAndSelectMedication(index, item.dataset.medName);
        } else if (item.dataset.medId) {
            selectMedication(index, parseInt(item.dataset.medId), item.dataset.medName, !!parseInt(item.dataset.medFav));
        }
    });

    document.addEventListener('click', e => {
        if (!e.target.closest(`#med-row-${index}`)) drop.style.display = 'none';
    });
}

function fetchMedSuggestions(query, index) {
    fetch(`{{ route('clinic.medications.search') }}?search=${encodeURIComponent(query)}`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => r.json()).then(data => renderMedSuggestions(data, index, query));
}

function renderMedSuggestions(meds, index, query) {
    const drop = document.getElementById(`med_${index}_drop`);
    if (!document.getElementById(`med_${index}_name`).value.trim()) { drop.style.display = 'none'; return; }

    const exact = meds.find(m => m.name.toLowerCase() === query.toLowerCase());
    let html = meds.slice(0, 10).map(m => `
        <div class="med-suggest-item ${m.is_favorite ? 'is-fav' : ''}"
             data-med-id="${m.id}" data-med-name="${esc(m.name)}" data-med-fav="${m.is_favorite ? 1 : 0}" data-med-index="${index}">
            ${m.is_favorite ? '<i class="ph-fill ph-star" style="color:#f59e0b;font-size:12px;"></i>' : '<span style="width:18px;display:inline-block;"></span>'}
            <span style="${m.is_mine ? 'color:#2A7F62;font-weight:600;' : ''}">${esc(m.name)}</span>
        </div>
    `).join('');

    if (!exact) {
        html += `<div class="med-suggest-item med-suggest-new" data-med-new="1" data-med-index="${index}" data-med-name="${esc(query)}">
                    <i class="ph-bold ph-plus-circle"></i>
                    <span>Create: "<b>${esc(query)}</b>"</span>
                 </div>`;
    }
    drop.innerHTML = html || '<div class="med-suggest-item" style="color:#94a3b8;">No matches found</div>';
    drop.style.display = 'block';
}

function selectMedication(index, id, name, isFavorite) {
    document.getElementById(`med_${index}_id`).value   = id;
    document.getElementById(`med_${index}_name`).value = name;
    document.getElementById(`med_${index}_drop`).style.display = 'none';
    markFavoriteBtn(index, isFavorite);
}

function autoCreateMedication(name, index) {
    createAndSelectMedication(index, name);
}

function createAndSelectMedication(index, name) {
    fetch('{{ route("clinic.medications.store") }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body:    JSON.stringify({ name })
    }).then(r => r.json()).then(med => selectMedication(index, med.id, med.name, false));
}

// ── Favorites ──
function toggleFavMed(index) {
    const medId = document.getElementById(`med_${index}_id`).value;
    if (!medId) { showToast('Select a medication first to mark as favorite', '#f59e0b'); return; }
    const btn = document.getElementById(`med_${index}_fav`);
    const isFav = btn.classList.contains('is-favorite');
    fetch(`/clinic/medications/${medId}/favorite`, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        markFavoriteBtn(index, data.is_favorite);
        showToast(data.is_favorite ? 'Added to favorites ⭐' : 'Removed from favorites');
    });
}

function markFavoriteBtn(index, isFavorite) {
    const btn = document.getElementById(`med_${index}_fav`);
    if (!btn) return;
    btn.classList.toggle('is-favorite', isFavorite);
    btn.querySelector('i').className = isFavorite ? 'ph-fill ph-star' : 'ph-bold ph-star';
    btn.querySelector('i').style.fontSize = '16px';
}

// ── Quick Add Parser ──
const FREQ_MAP = {
    '1':1,'1x':1,'od':1,'once':1,
    '2':2,'2x':2,'bid':2,'twice':2,
    '3':3,'3x':3,'tid':3,'tds':3,
    '4':4,'4x':4,'qid':4,'qds':4,
};
const FREQ_LABELS = ['Once Daily','Twice Daily','Three times Daily','Four times Daily'];

function parseQuickAdd(text) {
    const parts = text.trim().split(/\s+/);
    const result = { name: '', dosage: '', frequency: '', duration: '' };
    const nameParts = [];

    for (const part of parts) {
        const lower = part.toLowerCase();

        // Duration: 5d, 7d, 2w, 1m
        if (/^\d+[dmwDMW]$/.test(part)) {
            const num  = parseInt(part);
            const unit = lower.slice(-1);
            if (unit === 'd') {
                const buckets = [[3,'3 Days'],[5,'5 Days'],[7,'7 Days'],[10,'10 Days'],[14,'14 Days']];
                const found = buckets.find(([n]) => num <= n);
                result.duration = found ? found[1] : '1 Month';
            } else if (unit === 'w') {
                result.duration = num <= 1 ? '7 Days' : '14 Days';
            } else if (unit === 'm') {
                result.duration = num === 1 ? '1 Month' : '3 Months';
            }
            continue;
        }

        // Frequency: 1x, 2x, bid…
        const freqKey = lower.replace('x', '');
        if (FREQ_MAP[lower] !== undefined || FREQ_MAP[freqKey] !== undefined) {
            const n = FREQ_MAP[lower] ?? FREQ_MAP[freqKey];
            result.frequency = FREQ_LABELS[n - 1] || 'Once Daily';
            continue;
        }

        // Dosage: 500mg, 250, 0.5g
        if (/^\d+(\.\d+)?(mg|ml|mcg|g|iu|tab|caps?)?$/i.test(part)) {
            result.dosage = part;
            continue;
        }

        nameParts.push(part);
    }

    result.name = nameParts.join(' ');
    return result;
}

document.getElementById('quick_add_input').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); quickAddMedication(); }
});

function quickAddMedication() {
    const input = document.getElementById('quick_add_input');
    const text  = input.value.trim();
    if (!text) return;

    const parsed = parseQuickAdd(text);
    if (!parsed.name) { showToast('Could not parse medication name', '#ef4444'); return; }

    const index = medCounter++;
    addMedRowFromData(index, parsed);

    // Auto-create medication in DB in background
    createAndSelectMedication(index, parsed.name);
    input.value = '';
}

// ── Print Button Visibility ──
function checkPrintButton() {
    const hasDx   = selectedDx.length > 0;
    const hasMeds = document.querySelectorAll('tr.med-row-active').length > 0;
    document.getElementById('btn-print').style.display = (hasDx || hasMeds) ? 'inline-flex' : 'none';
}

// ── Templates ──
function openSaveTemplate() {
    if (!document.querySelectorAll('tr.med-row-active').length) {
        showToast('Add medications first', '#f59e0b'); return;
    }
    document.getElementById('template_name_input').value = '';
    document.getElementById('modal-template-save').style.display = 'flex';
    setTimeout(() => document.getElementById('template_name_input').focus(), 50);
}

function confirmSaveTemplate() {
    const name = document.getElementById('template_name_input').value.trim();
    if (!name) { showToast('Enter a template name', '#f59e0b'); return; }

    const items = [];
    document.querySelectorAll('tr.med-row-active').forEach(row => {
        const medName = row.querySelector('.med-name-input').value.trim();
        if (!medName) return;
        items.push({
            medication_id: row.querySelector('.med-id-input').value || null,
            name:          medName,
            dosage:        row.querySelector('.med-dosage-input').value,
            frequency:     row.querySelector('.med-freq-input').value,
            route:         row.querySelector('.med-route-input').value,
            duration:      row.querySelector('.med-dur-input').value,
            instructions:  row.querySelector('.med-instr-input').value,
        });
    });

    fetch('{{ route("clinic.prescription-templates.store") }}', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body:    JSON.stringify({ name, items })
    }).then(r => r.json()).then(data => {
        closeModal('modal-template-save');
        showToast(`Template "${data.name}" saved ✓`);
    });
}

function openLoadTemplate() {
    const list = document.getElementById('template_list');
    list.innerHTML = '<div style="padding:20px;text-align:center;color:#94a3b8;">Loading…</div>';
    document.getElementById('modal-template-load').style.display = 'flex';

    fetch('{{ route("clinic.prescription-templates.index") }}', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => r.json()).then(templates => {
        if (!templates.length) {
            list.innerHTML = '<div style="padding:30px;text-align:center;color:#94a3b8;">No templates saved yet.</div>';
            return;
        }
        list.innerHTML = templates.map(t => `
            <div class="template-list-item" data-tid="${t.id}">
                <i class="ph-bold ph-clipboard-text"></i>
                <span>${esc(t.name)}</span>
                <i class="ph-bold ph-arrow-right arrow"></i>
            </div>
        `).join('');
        list.addEventListener('click', e => {
            const item = e.target.closest('.template-list-item');
            if (item && item.dataset.tid) loadTemplate(parseInt(item.dataset.tid));
        });
    });
}

function loadTemplate(templateId) {
    fetch(`/clinic/prescription-templates/${templateId}/load`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => r.json()).then(data => {
        // Clear current rows
        document.querySelectorAll('tr.med-row-active').forEach(r => r.remove());
        medCounter = 0;

        data.items.forEach(item => addMedRowFromData(medCounter++, item));

        if (document.querySelectorAll('tr.med-row-active').length) {
            document.getElementById('meds_empty_state').style.display = 'none';
        }

        closeModal('modal-template-load');
        showToast(`Template "${data.name}" loaded ✓`);
    });
}

// ── Modal helpers ──
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeModal('modal-template-save');
        closeModal('modal-template-load');
    }
});

// ── Toast ──
function showToast(msg, color = '#2A7F62') {
    const t = document.getElementById('rx-toast');
    t.textContent  = msg;
    t.style.background = color;
    t.style.display = 'block';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => { t.style.display = 'none'; }, 3000);
}

// ═══════════════ PRINT HANDLER ═══════════════
const _rxColor = '{{ $rxColor }}';
const _rxA10   = '{{ $rxAlpha10 }}';
const _rxA20   = '{{ $rxAlpha20 }}';

function handlePrint() {
    // Vitals
    const vitals = [['bp','BP','mmHg'],['temp','Temp','°C'],['pulse','Pulse','bpm'],['hr','HR','bpm'],['rr','RR','/min'],['spo2','SpO2','%'],['weight','Weight','kg'],['height','Height','cm']];
    let vitHtml = '';
    vitals.forEach(([id, label, unit]) => {
        const val = document.getElementById('v_' + id).value;
        if (val) vitHtml += `<span class="rxp-chip" style="background:${_rxA10};border-color:${_rxA20};color:${_rxColor};font-size:12px;padding:3px 12px;">${label}: <b style="margin-inline-start:3px;">${val}</b><span style="font-size:10px;margin-inline-start:2px;opacity:.7;">${unit}</span></span>`;
    });
    const vitSec = document.getElementById('rx_vitals_section');
    if (vitHtml) { document.getElementById('rx_vitals_grid').innerHTML = vitHtml; vitSec.style.display = 'block'; }
    else vitSec.style.display = 'none';

    // Medications — professional card layout
    const rows = document.querySelectorAll('tr.med-row-active');
    let medsHtml = '';
    rows.forEach((row, idx) => {
        const name  = row.querySelector('.med-name-input').value;
        if (!name) return;
        const dosage = row.querySelector('.med-dosage-input').value;
        const freq   = row.querySelector('.med-freq-input').value;
        const route  = row.querySelector('.med-route-input').value;
        const dur    = row.querySelector('.med-dur-input').value;
        const instr  = row.querySelector('.med-instr-input').value;
        const chip   = (icon, txt) => txt ? `<span class="rxp-chip" style="background:${_rxA10};border-color:${_rxA20};color:${_rxColor};">${icon} ${txt}</span>` : '';
        medsHtml += `
            <div class="rxp-med-card" style="border-inline-start-color:${_rxColor};">
                <div class="rxp-med-circle" style="background:${_rxColor};">${idx + 1}</div>
                <div class="rxp-med-inner">
                    <div class="rxp-med-title">${name}</div>
                    <div class="rxp-med-chips">
                        ${chip('💊', dosage)}
                        ${chip('⏱', freq)}
                        ${chip('📅', dur)}
                        ${chip('🏥', route)}
                    </div>
                    ${instr ? `<div class="rxp-med-note">📝 ${instr}</div>` : ''}
                </div>
            </div>`;
    });
    document.getElementById('rx_meds_body').innerHTML = medsHtml || '<div class="rxp-no-meds">No medications prescribed</div>';

    window.print();
}
</script>
@endsection
