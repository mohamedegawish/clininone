/**
 * booking.js — Booking Logic
 */

import { fetchDoctor, fetchAvailableSlots, bookAppointment } from './api.js';

let doctor = null;
let slots = [];
let step = 1;
let visitType = 'new';
let selectedSlot = '';
let form = { patientName: '', patientPhone: '', patientAge: '', patientGender: 'male', patientEmail: '', notes: '' };
let loading = false;
let toastMsg = null;

const today = new Date().toISOString().split('T')[0];

function showToast(msg, type = 'info') {
  toastMsg = { msg, type };
  render();
  setTimeout(() => { toastMsg = null; render(); }, 3000);
}

function updateBackLink(docId) {
  const link = document.getElementById('back-link');
  if (link) link.href = `/doctor?id=${docId}`;
}

window.setVisitType = function(type) { visitType = type; render(); };
window.selectSlot = function(slot) { selectedSlot = slot; render(); };
window.nextStep = function() {
  if (step === 1 && !selectedSlot) { showToast('يرجى اختيار ميعاد', 'error'); return; }
  if (step === 2) {
    const fn = document.getElementById('fn')?.value || '';
    const fp = document.getElementById('fp')?.value || '';
    const fa = document.getElementById('fa')?.value || '';
    if (!fn || !fp || !fa) { showToast('يرجى ملء الحقول المطلوبة', 'error'); return; }
    form.patientName = fn;
    form.patientPhone = fp;
    form.patientAge = fa;
    form.patientGender = document.getElementById('fg')?.value || 'male';
    form.patientEmail = document.getElementById('femail')?.value || '';
    form.notes = document.getElementById('fnotes')?.value || '';
  }
  step++;
  render();
};
window.prevStep = function() { step--; render(); };
window.confirmBooking = async function() {
  if (loading) return;
  loading = true;
  render();
  try {
    const payload = {
      doctor_id: parseInt(doctor.id),
      clinic_id: parseInt(doctor.clinicId || 1),
      appointment_date: today,
      start_time: selectedSlot,
      full_name: form.patientName,
      phone: form.patientPhone,
      gender: form.patientGender.toLowerCase(),
      notes: form.notes,
      ...(form.patientEmail ? { email: form.patientEmail } : {}),
    };

    const response = await bookAppointment(payload);
    const appointment = response.data || response;
    
    step = 4;
    window.bookedAppointment = appointment;
    loading = false;
    render();
  } catch (e) {
    console.error("Booking Confirm Error:", e);
    loading = false;
    showToast(e.message || 'حدث خطأ أثناء الحجز', 'error');
  }
};

function render() {
  const container = document.getElementById('booking-container');
  if (!container) return;
  if (!doctor) return;

  const initial = (doctor.name || '?').replace('د. ', '').charAt(0);
  const effectivePrice = visitType === 'followup' ? Math.round((doctor.price || 0) * 0.6) : (doctor.price || 0);

  let html = '';
  
  if (step === 4) {
    const appt = window.bookedAppointment || {};
    html = `
      <div class="booking-card card animate-fade-in" style="text-align:center; padding: 40px 20px;">
        <div style="width: 64px; height: 64px; background: #10B981; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2 style="margin-bottom: 8px; color: var(--text-primary); font-size: 24px; font-weight: 700;">تم تأكيد الحجز!</h2>
        <p style="color: var(--text-secondary); margin-bottom: 24px;">يرجى الاحتفاظ بلقطة شاشة لهذه البطاقة لتأكيد حضورك</p>
        
        <div class="ticket-card" style="background: var(--bg-alt); border: 2px dashed var(--border-light); border-radius: 12px; padding: 24px; max-width: 380px; margin: 0 auto; text-align: right; position: relative;">
          <!-- Ticket notches -->
          <div style="position: absolute; left: -12px; top: 70px; width: 24px; height: 24px; border-radius: 50%; background: var(--bg-body); border-right: 2px dashed var(--border-light);"></div>
          <div style="position: absolute; right: -12px; top: 70px; width: 24px; height: 24px; border-radius: 50%; background: var(--bg-body); border-left: 2px dashed var(--border-light);"></div>
          
          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px dashed var(--border-light); padding-bottom: 20px; margin-bottom: 20px;">
            <span style="color: var(--text-muted); font-size: 15px; font-weight: 600;">رقم الدور (الطابور)</span>
            <span style="background: rgba(16, 185, 129, 0.15); color: #10B981; padding: 6px 16px; border-radius: 20px; font-weight: 800; font-size: 22px;">
              #${appt.queue_number || '1'}
            </span>
          </div>
          
          <div style="margin-bottom: 16px;">
            <div style="color: var(--text-muted); font-size: 13px; margin-bottom: 4px;">اسم المريض</div>
            <div style="font-weight: 700; color: var(--text-primary); font-size: 16px;">${form.patientName}</div>
          </div>
          
          <div style="margin-bottom: 16px;">
            <div style="color: var(--text-muted); font-size: 13px; margin-bottom: 4px;">رقم الهاتف</div>
            <div style="font-weight: 700; color: var(--text-primary); font-size: 16px;" dir="ltr" style="text-align: right">${form.patientPhone}</div>
          </div>
          
          <div style="margin-bottom: 16px;">
            <div style="color: var(--text-muted); font-size: 13px; margin-bottom: 4px;">التاريخ والميعاد</div>
            <div style="font-weight: 700; color: var(--text-primary); font-size: 16px;">${today} | ${selectedSlot}</div>
          </div>
          
          <div style="margin-bottom: 0;">
            <div style="color: var(--text-muted); font-size: 13px; margin-bottom: 4px;">الطبيب المعالج</div>
            <div style="font-weight: 700; color: var(--text-primary); font-size: 16px;">${doctor.name} - <span style="font-weight: 500; font-size: 14px; color: var(--text-muted);">${doctor.specialty}</span></div>
          </div>
        </div>
        
        <div style="margin-top: 32px; max-width: 380px; margin-left: auto; margin-right: auto;">
          <a href="/" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">العودة للرئيسية</a>
        </div>
      </div>
    `;
  } else {
    html = `
      <!-- Doctor summary -->
      <div class="booking-doctor-card glass">
        <div class="avatar" style="background:linear-gradient(135deg,var(--primary),var(--purple))">
          <span>${initial}</span>
        </div>
        <div>
          <div class="bk-doc-name">${doctor.name || 'طبيب'}</div>
          <div class="specialty-tag">${doctor.specialty || 'تخصص'}</div>
        </div>
        <div class="bk-doc-price">
          <span class="price-val">EGP ${doctor.price || 0}</span>
          <span class="price-label">لكل زيارة</span>
        </div>
      </div>

      <!-- Steps indicator -->
      <div class="steps-bar">
        ${[1, 2, 3].map(s => `
          <div class="step-item ${step >= s ? 'active' : ''} ${step === s ? 'current' : ''}">
            <div class="step-circle">
              ${step > s 
                ? `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>` 
                : s}
            </div>
            <span class="step-label">${['اختر الميعاد', 'بياناتك', 'الدفع'][s - 1]}</span>
          </div>
          ${s < 3 ? `<div class="step-line ${step > s ? 'done' : ''}"></div>` : ''}
        `).join('')}
      </div>
    `;

    if (step === 1) {
    html += `
      <div class="booking-card card animate-fade-in">
        <div class="booking-section-header" style="margin-bottom:12px">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <h2 class="booking-section-title">نوع الزيارة</h2>
        </div>
        <div class="visit-type-row">
          <button class="visit-type-btn ${visitType === 'new' ? 'active' : ''}" onclick="setVisitType('new')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14m-7-7h14"/></svg>
            <span class="vt-label">كشف جديد</span>
            <span class="vt-price">EGP ${doctor.price || 0}</span>
          </button>
          <button class="visit-type-btn ${visitType === 'followup' ? 'active' : ''}" onclick="setVisitType('followup')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 2v6h-6"/></svg>
            <span class="vt-label">إعادة كشف</span>
            <span class="vt-price">EGP ${Math.round((doctor.price || 0) * 0.6)} <span class="vt-discount">-40%</span></span>
          </button>
        </div>

        <div class="divider"></div>

        <div class="booking-section-header">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <h2 class="booking-section-title">اختر ميعادك</h2>
        </div>
        <p class="booking-date-display">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          اليوم — ${today}
        </p>
        <div class="slots-grid">
          ${(Array.isArray(slots) ? slots : []).map(s => {
            const time = typeof s === 'string' ? s : (s.start_time || '');
            if (!time) return '';
            return `<button class="slot-btn ${selectedSlot === time ? 'selected' : ''}" onclick="selectSlot('${time}')">${time}</button>`;
          }).join('')}
          ${(!Array.isArray(slots) || slots.length === 0) ? '<p style="grid-column:1/-1;color:var(--text-muted);font-size:13px">لا يوجد مواعيد اليوم</p>' : ''}
        </div>
        <div class="step-actions">
          <a class="btn btn-ghost" href="/doctor?id=${doctor.id}">إلغاء</a>
          <button class="btn btn-primary" onclick="nextStep()" ${!selectedSlot ? 'disabled' : ''}>
            التالي: بياناتك
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    `;
  } else if (step === 2) {
    html += `
      <div class="booking-card card animate-fade-in">
        <div class="booking-section-header">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <h2 class="booking-section-title">بيانات المريض</h2>
        </div>
        <div class="selected-slot-badge">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          الميعاد: ${selectedSlot}
        </div>
        <div class="form-grid">
          <div class="input-group" style="grid-column:1/-1">
            <label class="input-label">الاسم كاملاً *</label>
            <input class="input" id="fn" type="text" placeholder="أدخل اسمك كاملاً" value="${form.patientName}" />
          </div>
          <div class="input-group">
            <label class="input-label">رقم الهاتف *</label>
            <input class="input" id="fp" type="tel" placeholder="+20 1XX XXX XXXX" value="${form.patientPhone}" />
          </div>
          <div class="input-group">
            <label class="input-label">العمر *</label>
            <input class="input" id="fa" type="number" placeholder="عمرك" min="1" max="120" value="${form.patientAge}" />
          </div>
          <div class="input-group">
            <label class="input-label">الجنس</label>
            <select class="input" id="fg">
              <option value="male" ${form.patientGender === 'male' ? 'selected' : ''}>ذكر</option>
              <option value="female" ${form.patientGender === 'female' ? 'selected' : ''}>أنثى</option>
            </select>
          </div>
          <div class="input-group" style="grid-column:1/-1">
            <label class="input-label">البريد الإلكتروني (اختياري — لاستلام تأكيد الحجز)</label>
            <input class="input" id="femail" type="email" placeholder="example@email.com" value="${form.patientEmail}" dir="ltr" />
          </div>
          <div class="input-group" style="grid-column:1/-1">
            <label class="input-label">ملاحظات / أعراض (اختياري)</label>
            <textarea class="input" id="fnotes" rows="3" placeholder="اوصف أعراضك أو أي ملاحظات للطبيب...">${form.notes}</textarea>
          </div>
        </div>
        <div class="step-actions">
          <button class="btn btn-ghost" onclick="prevStep()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
            رجوع
          </button>
          <button class="btn btn-primary" onclick="nextStep()">
            التالي: الدفع
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
          </button>
        </div>
      </div>
    `;
  } else if (step === 3) {
    html += `
      <div class="booking-card card animate-fade-in">
        <div class="booking-section-header">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
          <h2 class="booking-section-title">ملخص الدفع</h2>
        </div>
        <div class="payment-summary">
          <div class="summary-row"><span>الطبيب</span><span class="summary-val">${doctor.name}</span></div>
          <div class="summary-row"><span>التخصص</span><span class="summary-val">${doctor.specialty}</span></div>
          <div class="summary-row"><span>المريض</span><span class="summary-val">${form.patientName}</span></div>
          <div class="summary-row"><span>الميعاد</span><span class="summary-val">${today} في ${selectedSlot}</span></div>
          ${form.patientEmail ? `<div class="summary-row"><span>البريد الإلكتروني</span><span class="summary-val" dir="ltr">${form.patientEmail}</span></div>` : ''}
          <div class="divider"></div>
          <div class="summary-row total">
            <span>الإجمالي</span>
            <span class="total-price">EGP ${effectivePrice}</span>
          </div>
        </div>

        <div class="payment-methods">
          <div class="pm-chip active">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M22 10H2"/></svg>
            نقدي في العيادة
          </div>
        </div>

        <div class="step-actions">
          <button class="btn btn-ghost" onclick="prevStep()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
            رجوع
          </button>
          <button class="btn btn-accent btn-lg" onclick="confirmBooking()" ${loading ? 'disabled' : ''}>
            ${loading 
              ? `<span class="spinner"></span> جاري المعالجة...` 
              : `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> تأكيد الحجز`
            }
          </button>
        </div>
      </div>
    `;
  }
} // Close the else block for step 1-3

  if (toastMsg) {
    html += `
      <div class="toast toast-${toastMsg.type}">
        ${toastMsg.type === 'success'
          ? `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`
          : `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`
        }
        ${toastMsg.msg}
      </div>
    `;
  }

  container.innerHTML = html;
}

async function init() {
  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get('id');
  if (!id) {
    document.getElementById('booking-container').innerHTML = '<div style="text-align:center;padding:50px">الطبيب غير موجود.</div>';
    return;
  }
  updateBackLink(id);

  try {
    const docData = await fetchDoctor(id);
    if (!docData) throw new Error("لم يتم العثور على بيانات الطبيب");
    doctor = docData;
    
    const docSlots = await fetchAvailableSlots(id, today, doctor.clinicId || null);
    slots = docSlots || [];
    
    render();
  } catch (err) {
    console.error("Booking Init Error:", err);
    document.getElementById('booking-container').innerHTML = `<div style="text-align:center;padding:50px;color:red">فشل تحميل البيانات: ${err.message}</div>`;
  }
}

init();
