/**
 * doctor.js — Doctor Profile Logic (Vanilla JS matching Angular layout)
 */

import { fetchDoctor, fetchReviews } from './api.js';

function renderStars(rating, size = 18) {
  return Array(5).fill(0).map((_, i) => {
    const filled = i < Math.round(rating || 0);
    return `<svg width="\${size}" height="\${size}" viewBox="0 0 24 24"
      fill="\${filled ? '#F59E0B' : 'none'}"
      stroke="\${filled ? '#F59E0B' : 'var(--text-muted)'}" stroke-width="1.5">
      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
    </svg>`;
  }).join('');
}

async function init() {
  const container = document.getElementById('profile-container');
  if (!container) return;

  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get('id');

  if (!id) {
    container.innerHTML = `<div style="display:flex;align-items:center;justify-content:center;height:50vh"><p style="color:var(--text-muted)">الطبيب غير موجود.</p></div>`;
    return;
  }

  try {
    const [doc, reviews] = await Promise.all([ fetchDoctor(id), fetchReviews(id) ]);
    if (!doc) throw new Error('Not found');

    const initial = (doc.name || '?').replace('د. ', '').charAt(0);
    const photoHTML = doc.photo 
      ? `<img src="\${doc.photo}" style="width:100%;height:100%;object-fit:cover" onerror="this.style.display='none';this.nextElementSibling.style.display='inline-block'">
         <span style="display:none">\${initial}</span>`
      : `<span>\${initial}</span>`;

    container.innerHTML = `
      <!-- HERO CARD -->
      <div class="profile-hero glass-strong">
        <div class="profile-bg-decoration"></div>
        <div class="profile-hero-content">
          <div class="avatar avatar-xl profile-avatar" style="overflow:hidden">
            \${photoHTML}
          </div>
          <div class="profile-hero-info">
            <div class="specialty-tag" style="margin-bottom:10px">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
              \${doc.specialty || ''}
            </div>
            <h1 class="profile-name">\${doc.name || ''}</h1>
            <p class="profile-about-short">\${doc.bio || doc.about || 'لا توجد نبذة حالياً.'}</p>
            <div class="profile-rating-row">
              \${renderStars(doc.rating, 18)}
              <span class="rating-big">\${doc.rating || 0}</span>
              <span style="color:var(--text-muted);font-size:13px">(\${doc.reviewCount || doc.reviews_count || 0} تقييم)</span>
            </div>
          </div>
          <div class="profile-cta">
            <div class="price-display">
              <span class="price-label-sm">رسوم الكشف</span>
              <span class="price-big">EGP \${doc.price || 0}</span>
            </div>
            <a class="btn btn-primary btn-lg" href="booking.html?id=\${doc.id}">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              احجز موعد
            </a>
          </div>
        </div>
      </div>

      <!-- STATS ROW -->
      <div class="profile-stats">
        <div class="pstat-card">
          <div class="pstat-icon-wrap"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3v4M8 3v4M2 11h20"/></svg></div>
          <span class="pstat-val">\${doc.experience_years || doc.experience || 0}+</span>
          <span class="pstat-label">سنة خبرة</span>
        </div>
        <div class="pstat-card">
          <div class="pstat-icon-wrap"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
          <span class="pstat-val">\${doc.reviewCount || doc.reviews_count || 0}+</span>
          <span class="pstat-label">مريض</span>
        </div>
        <div class="pstat-card">
          <div class="pstat-icon-wrap"><svg width="20" height="20" viewBox="0 0 24 24" fill="#FCD34D" stroke="#FCD34D" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <span class="pstat-val">\${doc.rating || 0}</span>
          <span class="pstat-label">التقييم</span>
        </div>
        <div class="pstat-card">
          <div class="pstat-icon-wrap"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
          <span class="pstat-val" style="font-size:12px;text-align:center">\${doc.address || doc.city || 'غير محدد'}</span>
          <span class="pstat-label">الموقع</span>
        </div>
      </div>

      <div class="profile-grid">
        <!-- LEFT COL -->
        <div>
          <!-- About -->
          <div class="card" style="margin-bottom:20px">
            <h2 class="section-title">نبذة</h2>
            <p style="color:var(--text-secondary);line-height:1.7">\${doc.bio || doc.about || 'لا توجد نبذة متاحة.'}</p>
          </div>

          <!-- Working Schedule -->
          <div class="card" style="margin-bottom:20px">
            <h2 class="section-title">مواعيد العمل</h2>
            <div class="schedule-time">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
              \${doc.workHours ? doc.workHours.start + ' – ' + doc.workHours.end : '9:00 ص – 5:00 م'}
            </div>
            <div class="days-row">
              \${['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'].map(day => 
                \`<div class="day-chip \${(doc.workDays || []).includes(day) || (doc.schedule && doc.schedule[day]) ? 'active' : ''}">\${day}</div>\`
              ).join('')}
            </div>
          </div>

          <!-- Reviews -->
          <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
              <h2 class="section-title" style="margin:0">تقييمات المرضى</h2>
            </div>
            \${reviews.length > 0 ? reviews.map(r => \`
              <div class="review-item">
                <div class="review-header">
                  <div class="review-avatar">\${(r.reviewer_name || r.patientName || 'م').charAt(0)}</div>
                  <div style="flex:1">
                    <div class="review-name">\${r.reviewer_name || r.patientName || 'مريض'}</div>
                    <div class="review-stars">
                      \${renderStars(r.rating, 13)}
                      <span class="review-date">· \${new Date(r.created_at).toLocaleDateString('ar-EG')}</span>
                    </div>
                  </div>
                </div>
                \${r.comment ? \`<p class="review-text">\${r.comment}</p>\` : ''}
              </div>
            \`).join('') : \`
              <div class="empty-state" style="padding:36px 0; background:none; border:none; box-shadow:none">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--border-light)" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <p style="color:var(--text-muted);margin:8px 0 4px">لا توجد تقييمات بعد</p>
                <p style="font-size:12px;color:var(--text-muted)">كن أول من يقيّم!</p>
              </div>
            \`}
          </div>
        </div>

        <!-- RIGHT COL -->
        <div>
          <!-- Contact -->
          <div class="card" style="margin-bottom:20px">
            <h2 class="section-title">معلومات التواصل</h2>
            <div class="contact-item">
              <div class="contact-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 11.3a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.23 0h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9a16 16 0 0 0 6.93 6.93l1.36-1.36a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 23 16.92z"/></svg></div>
              \${doc.phone || '+20 100 000 0000'}
            </div>
            <div class="contact-item">
              <div class="contact-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
              \${doc.email || 'doctor@clinicone.com'}
            </div>
            <div class="contact-item">
              <div class="contact-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
              \${doc.address || doc.city || 'العيادة'}
            </div>
          </div>

          <!-- Available Slots -->
          <div class="card">
            <h2 class="section-title">المواعيد المتاحة اليوم</h2>
            <div class="slots-preview">
              \${(doc.availableSlots || []).slice(0, 6).map(slot => \`<div class="slot-preview-chip">\${slot}</div>\`).join('')}
              \${(doc.availableSlots || []).length > 6 ? \`<div class="slot-preview-chip more">+\${doc.availableSlots.length - 6} أكثر</div>\` : ''}
              \${!(doc.availableSlots || []).length ? '<div style="color:var(--text-muted);font-size:13px;padding:10px 0">لا توجد مواعيد متاحة</div>' : ''}
            </div>
            <a class="btn btn-primary" style="width:100%;justify-content:center;margin-top:16px;gap:8px" href="booking.html?id=\${doc.id}">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              احجز موعد
            </a>
          </div>
        </div>
      </div>
    `;

  } catch (err) {
    container.innerHTML = `<div style="display:flex;align-items:center;justify-content:center;height:50vh"><p style="color:var(--text-muted)">حدث خطأ. الطبيب غير موجود.</p></div>`;
  }
}

document.addEventListener('DOMContentLoaded', init);
