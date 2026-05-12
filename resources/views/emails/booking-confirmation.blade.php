<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تأكيد الحجز — ClinicOne</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
    background: #0f172a;
    color: #e2e8f0;
    direction: rtl;
    -webkit-font-smoothing: antialiased;
  }
  .wrapper { max-width: 600px; margin: 0 auto; padding: 32px 16px; }

  /* Header */
  .header {
    background: linear-gradient(135deg, #1f5d96 0%, #7c3aed 100%);
    border-radius: 16px 16px 0 0;
    padding: 32px 24px;
    text-align: center;
  }
  .header-logo {
    display: inline-flex; align-items: center; gap: 10px;
    margin-bottom: 16px;
  }
  .header-logo svg { width: 28px; height: 28px; }
  .header-logo-text { font-size: 20px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
  .header-logo-sub { font-size: 10px; color: rgba(255,255,255,0.6); letter-spacing: 2px; text-transform: uppercase; }
  .header-check {
    width: 64px; height: 64px;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
    border: 2px solid rgba(255,255,255,0.3);
  }
  .header-title { font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 6px; }
  .header-sub { font-size: 14px; color: rgba(255,255,255,0.75); }

  /* Body card */
  .body-card {
    background: #1e293b;
    padding: 28px 24px;
  }

  /* Ticket */
  .ticket {
    background: #0f172a;
    border: 2px dashed #334155;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 24px;
  }
  .ticket-header {
    background: linear-gradient(90deg, rgba(31,93,150,0.3) 0%, rgba(124,58,237,0.3) 100%);
    border-bottom: 2px dashed #334155;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .ticket-header-label { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
  .queue-badge {
    background: rgba(16,185,129,0.15);
    border: 1px solid rgba(16,185,129,0.4);
    color: #10b981;
    font-size: 28px;
    font-weight: 900;
    padding: 6px 20px;
    border-radius: 50px;
    direction: ltr;
  }
  .ticket-rows { padding: 20px; }
  .ticket-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 10px 0;
    border-bottom: 1px solid #1e293b;
  }
  .ticket-row:last-child { border-bottom: none; }
  .ticket-row-label { font-size: 12px; color: #64748b; font-weight: 600; padding-top: 2px; white-space: nowrap; }
  .ticket-row-value { font-size: 15px; font-weight: 700; color: #e2e8f0; text-align: left; direction: ltr; max-width: 65%; word-break: break-word; }
  .ticket-row-value.rtl { direction: rtl; text-align: right; }
  .status-badge {
    display: inline-block;
    background: rgba(251,191,36,0.15);
    border: 1px solid rgba(251,191,36,0.4);
    color: #fbbf24;
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
  }

  /* Notice box */
  .notice {
    background: rgba(251,191,36,0.08);
    border: 1px solid rgba(251,191,36,0.25);
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 24px;
    font-size: 13px;
    color: #fbbf24;
    line-height: 1.6;
  }
  .notice strong { font-weight: 700; }

  /* Divider */
  .divider {
    border: none;
    border-top: 1px solid #334155;
    margin: 24px 0;
  }

  /* Footer */
  .footer {
    background: #0f172a;
    border-radius: 0 0 16px 16px;
    padding: 20px 24px;
    text-align: center;
    border-top: 1px solid #1e293b;
  }
  .footer p { font-size: 12px; color: #475569; line-height: 1.7; }
  .footer a { color: #1f5d96; text-decoration: none; }

  /* Responsive */
  @media (max-width: 480px) {
    .wrapper { padding: 16px 8px; }
    .header { padding: 24px 16px; }
    .body-card { padding: 20px 16px; }
    .ticket-rows { padding: 16px; }
    .ticket-row { flex-direction: column; gap: 4px; }
    .ticket-row-value { max-width: 100%; text-align: right; }
    .queue-badge { font-size: 22px; }
  }
</style>
</head>
<body>
<div class="wrapper">

  <!-- Header -->
  <div class="header">
    <div class="header-logo">
      <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
      </svg>
      <div>
        <div class="header-logo-text">ClinicOne</div>
        <div class="header-logo-sub">Smart Medical</div>
      </div>
    </div>

    <div class="header-check">
      <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="20 6 9 17 4 12"/>
      </svg>
    </div>

    <div class="header-title">تم استلام حجزك!</div>
    <div class="header-sub">احتفظ بهذا الإيميل كتأكيد للحجز</div>
  </div>

  <!-- Body -->
  <div class="body-card">

    <!-- Ticket -->
    <div class="ticket">
      <div class="ticket-header">
        <div>
          <div class="ticket-header-label">رقم دورك في الطابور</div>
        </div>
        <div class="queue-badge">#{{ $appointment->queue_number ?? '—' }}</div>
      </div>

      <div class="ticket-rows">

        <div class="ticket-row">
          <span class="ticket-row-label">اسم المريض</span>
          <span class="ticket-row-value rtl">{{ $appointment->patient?->full_name ?? '—' }}</span>
        </div>

        <div class="ticket-row">
          <span class="ticket-row-label">رقم الهاتف</span>
          <span class="ticket-row-value">{{ $appointment->patient?->phone ?? '—' }}</span>
        </div>

        <div class="ticket-row">
          <span class="ticket-row-label">الطبيب المعالج</span>
          <span class="ticket-row-value rtl">
            {{ $appointment->doctor?->name ?? '—' }}
            @if($appointment->doctor?->specialty)
              <br><small style="font-size:12px;color:#64748b;font-weight:500;">{{ $appointment->doctor->specialty }}</small>
            @endif
          </span>
        </div>

        <div class="ticket-row">
          <span class="ticket-row-label">العيادة</span>
          <span class="ticket-row-value rtl">{{ $appointment->clinic?->name ?? '—' }}</span>
        </div>

        <div class="ticket-row">
          <span class="ticket-row-label">تاريخ الموعد</span>
          <span class="ticket-row-value">
            {{ $appointment->appointment_date?->translatedFormat('l، d F Y') ?? '—' }}
          </span>
        </div>

        <div class="ticket-row">
          <span class="ticket-row-label">وقت الموعد</span>
          <span class="ticket-row-value">
            {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}
            —
            {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
          </span>
        </div>

        <div class="ticket-row">
          <span class="ticket-row-label">حالة الحجز</span>
          <span class="ticket-row-value">
            <span class="status-badge">بانتظار تأكيد العيادة</span>
          </span>
        </div>

        @if($appointment->notes)
        <div class="ticket-row">
          <span class="ticket-row-label">ملاحظاتك</span>
          <span class="ticket-row-value rtl">{{ $appointment->notes }}</span>
        </div>
        @endif

      </div>
    </div>

    <!-- Notice -->
    <div class="notice">
      <strong>📋 ملاحظة هامة:</strong><br>
      رقم الدور المعروض هو رقم مؤقت. سيتم تأكيده نهائياً من قِبَل العيادة.
      يُنصح بالحضور قبل موعدك بـ <strong>15 دقيقة</strong>.
    </div>

    <hr class="divider">

    <p style="font-size:13px; color:#64748b; text-align:center; line-height:1.7;">
      إذا كنت تريد إلغاء الحجز أو لديك استفسار، يرجى التواصل مع العيادة مباشرة.<br>
      شكراً لاختيارك ClinicOne.
    </p>

  </div>

  <!-- Footer -->
  <div class="footer">
    <p>
      هذا البريد أُرسل تلقائياً — يرجى عدم الرد عليه.<br>
      © {{ date('Y') }} ClinicOne · All rights reserved
    </p>
  </div>

</div>
</body>
</html>
