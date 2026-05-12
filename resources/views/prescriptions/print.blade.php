<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription — {{ $consultation->patient->full_name }}</title>
    <style>
        /* ─── Reset & Base ─── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', 'Arial', sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #f1f5f9;
            line-height: 1.5;
        }

        /* ─── Page Container ─── */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            padding: 28mm 20mm 20mm;
            box-shadow: 0 4px 32px rgba(0,0,0,.15);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* ─── Header ─── */
        .rx-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 4px solid #2A7F62;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        .rx-logo-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .rx-logo {
            width: 58px;
            height: 58px;
            background: linear-gradient(135deg, #2A7F62, #3dbf8f);
            color: #fff;
            font-size: 24px;
            font-weight: 900;
            font-style: italic;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(42,127,98,.3);
        }

        .rx-clinic-name {
            font-size: 22px;
            font-weight: 800;
            color: #2A7F62;
            letter-spacing: -.02em;
        }

        .rx-clinic-sub {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: 2px;
        }

        .rx-doctor-info {
            text-align: right;
        }

        .rx-doctor-name {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
        }

        .rx-doctor-spec {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        .rx-doctor-phone {
            font-size: 13px;
            color: #2A7F62;
            font-weight: 600;
            margin-top: 5px;
        }

        /* ─── Patient Info Bar ─── */
        .rx-patient-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            background: #EAF7F2;
            border: 1px solid #b7e4d4;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 22px;
        }

        .rx-patient-cell {
            padding: 11px 14px;
            border-right: 1px solid #b7e4d4;
        }

        .rx-patient-cell:last-child { border-right: none; }

        .rx-cell-label {
            font-size: 9px;
            font-weight: 700;
            color: #6b9e8a;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 4px;
        }

        .rx-cell-value {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }

        /* ─── Diagnosis block ─── */
        @php
            $dxDisplay = $consultation->diagnosis;
        @endphp

        .rx-dx-block {
            background: #EAF7F2;
            border-left: 4px solid #2A7F62;
            border-radius: 0 8px 8px 0;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .rx-section-label {
            font-size: 10px;
            font-weight: 800;
            color: #2A7F62;
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rx-dx-text {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        /* ─── Vitals ─── */
        .rx-vitals-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .rx-vital-chip {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px 12px;
            text-align: center;
            min-width: 68px;
        }

        .rx-vital-label {
            font-size: 8px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
        }

        .rx-vital-val {
            font-size: 13px;
            font-weight: 700;
            color: #2A7F62;
        }

        /* ─── Rx Symbol ─── */
        .rx-symbol-row {
            display: flex;
            align-items: baseline;
            gap: 10px;
            border-bottom: 2px solid #EAF7F2;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .rx-symbol {
            font-size: 30px;
            font-weight: 900;
            color: #2A7F62;
            font-style: italic;
            line-height: 1;
        }

        .rx-plan-label {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        /* ─── Medication Table ─── */
        .rx-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .rx-table thead tr {
            background: #EAF7F2;
        }

        .rx-table th {
            padding: 9px 12px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: #2A7F62;
            border-bottom: 2px solid #b7e4d4;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .rx-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
            vertical-align: top;
        }

        .rx-table tbody tr:nth-child(even) { background: #f8fafc; }

        .rx-med-name {
            font-weight: 700;
            font-size: 13px;
            color: #1e293b;
        }

        .rx-med-num {
            font-weight: 700;
            color: #2A7F62;
            font-size: 13px;
        }

        .rx-empty-meds {
            text-align: center;
            padding: 28px;
            color: #94a3b8;
            font-style: italic;
        }

        /* ─── Footer ─── */
        .rx-footer {
            margin-top: auto;
            padding-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .rx-sig-line {
            width: 200px;
            border-bottom: 1px solid #cbd5e1;
            margin-bottom: 8px;
        }

        .rx-sig-label {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .rx-get-well {
            font-size: 14px;
            font-weight: 700;
            color: #2A7F62;
            margin-bottom: 4px;
            text-align: right;
        }

        .rx-timestamp {
            font-size: 10px;
            color: #94a3b8;
            text-align: right;
        }

        /* ─── No-print controls ─── */
        .no-print-bar {
            width: 210mm;
            margin: 0 auto 0;
            display: flex;
            gap: 10px;
            padding: 12px 0;
        }

        .btn-print {
            background: #2A7F62;
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* ─── Print media ─── */
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm 12mm;
            }

            body { background: #fff; }

            .no-print-bar { display: none !important; }

            .page {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                min-height: unset;
            }
        }
    </style>
</head>
<body>

{{-- No-print action bar --}}
<div class="no-print-bar">
    <button class="btn-print" onclick="window.print()">
        &#128438; Print Prescription
    </button>
    <a href="{{ route('clinic.consultations.show', $consultation->id) }}" class="btn-back">
        &#8592; Back to Consultation
    </a>
</div>

<div class="page">

    {{-- ── Header ── --}}
    <div class="rx-header">
        <div class="rx-logo-wrap">
            <div class="rx-logo">Rx</div>
            <div>
                <div class="rx-clinic-name">{{ App\Models\Setting::get('system_name','ClinicOne') }}</div>
                <div class="rx-clinic-sub">Medical Center &amp; Specialized Care</div>
            </div>
        </div>
        <div class="rx-doctor-info">
            <div class="rx-doctor-name">Dr. {{ $consultation->doctor->name }}</div>
            <div class="rx-doctor-spec">{{ $consultation->doctor->specialization ?? 'Specialist Physician' }}</div>
            <div class="rx-doctor-phone">{{ $consultation->doctor->phone ?? '—' }}</div>
        </div>
    </div>

    {{-- ── Patient Info Bar ── --}}
    <div class="rx-patient-bar">
        <div class="rx-patient-cell">
            <div class="rx-cell-label">Patient Name</div>
            <div class="rx-cell-value">{{ $consultation->patient->full_name }}</div>
        </div>
        <div class="rx-patient-cell">
            <div class="rx-cell-label">Age / Gender</div>
            <div class="rx-cell-value">
                {{ $consultation->patient->birth_date
                    ? \Carbon\Carbon::parse($consultation->patient->birth_date)->age . ' Yrs'
                    : ($consultation->patient->age ?? '—') }}
                / {{ ucfirst($consultation->patient->gender ?? '—') }}
            </div>
        </div>
        <div class="rx-patient-cell">
            <div class="rx-cell-label">Visit Date</div>
            <div class="rx-cell-value">{{ $consultation->created_at->format('d M Y') }}</div>
        </div>
        <div class="rx-patient-cell">
            <div class="rx-cell-label">Record ID</div>
            <div class="rx-cell-value">#{{ str_pad($consultation->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>

    {{-- ── Vitals ── --}}
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
    <div style="margin-bottom:20px;">
        <div class="rx-section-label">⚕ Vital Signs</div>
        <div class="rx-vitals-wrap">
            @foreach($vitals as $label => $val)
            <div class="rx-vital-chip">
                <div class="rx-vital-label">{{ $label }}</div>
                <div class="rx-vital-val">{{ $val }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Diagnosis ── --}}
    <div style="margin-bottom:{{ $consultation->symptoms ? '14px' : '20px' }};">
        <div class="rx-section-label">🔬 Clinical Diagnosis</div>
        <div class="rx-dx-block">
            <div class="rx-dx-text">{{ $consultation->diagnosis }}</div>
        </div>
    </div>

    {{-- ── Symptoms ── --}}
    @if($consultation->symptoms)
    <div style="margin-bottom:20px;">
        <div class="rx-section-label">📋 Patient Complaints</div>
        <div style="font-size:13px;color:#475569;font-style:italic;line-height:1.7;white-space:pre-wrap;">{{ $consultation->symptoms }}</div>
    </div>
    @endif

    {{-- ── Prescription ── --}}
    <div class="rx-symbol-row">
        <span class="rx-symbol">℞</span>
        <span class="rx-plan-label">Medication Plan</span>
    </div>

    <table class="rx-table">
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th>Medication</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
                <th>Instructions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consultation->medicationRecords as $idx => $med)
            <tr>
                <td class="rx-med-num">{{ $idx + 1 }}</td>
                <td class="rx-med-name">{{ $med->name }}</td>
                <td>{{ $med->dosage ?: ($med->generic ?: '—') }}</td>
                <td>{{ $med->frequency ?: '—' }}</td>
                <td>{{ $med->duration ?: '—' }}</td>
                <td style="color:#64748b;">{{ $med->instructions ?: '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="rx-empty-meds">No medications prescribed.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── Footer ── --}}
    <div class="rx-footer">
        <div style="text-align:center;">
            <div class="rx-sig-line"></div>
            <div class="rx-sig-label">Clinic Stamp &amp; Doctor's Signature</div>
        </div>
        <div>
            <div class="rx-get-well">🌿 Get Well Soon</div>
            <div class="rx-timestamp">{{ $consultation->created_at->format('Y-m-d H:i') }} &nbsp;|&nbsp; {{ App\Models\Setting::get('system_name','ClinicOne') }}</div>
        </div>
    </div>

</div>

</body>
</html>
