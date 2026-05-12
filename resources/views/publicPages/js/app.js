/**
 * app.js — Shared utilities for ClinicOne Patient Portal
 */

/* ─── Render Stars ─── */
function renderStars(rating, size = 13) {
  return Array(5).fill(0).map((_, i) => {
    const filled = i < Math.floor(rating);
    return `<svg width="${size}" height="${size}" viewBox="0 0 24 24"
      fill="${filled ? '#F59E0B' : 'none'}"
      stroke="${filled ? '#F59E0B' : '#475569'}" stroke-width="2">
      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
    </svg>`;
  }).join('');
}

/* ─── Show Toast ─── */
function showToast(msg, type = 'info', duration = 3200) {
  // Remove existing
  document.querySelectorAll('.toast').forEach(t => t.remove());

  const icons = {
    success: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
    error:   `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
    info:    `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
  };

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `${icons[type] || ''}<span>${msg}</span>`;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), duration);
}

/* ─── Format Date Arabic ─── */
function formatDateAr(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  return d.toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' });
}

/* ─── Get Today as YYYY-MM-DD ─── */
function getToday() {
  return new Date().toISOString().split('T')[0];
}

/* ─── Get URL param ─── */
function getParam(name) {
  return new URLSearchParams(window.location.search).get(name);
}

/* ─── Skeleton HTML ─── */
function doctorCardSkeleton() {
  return `
    <div class="skeleton-card">
      <div class="doctor-card-top">
        <div class="skeleton sk sk-avatar"></div>
        <div style="flex:1">
          <div class="skeleton sk sk-title" style="margin-bottom:8px"></div>
          <div class="skeleton sk sk-tag"></div>
        </div>
      </div>
      <div class="skeleton sk sk-meta" style="margin-bottom:8px"></div>
      <div class="skeleton sk sk-meta2"></div>
    </div>`;
}

/* ─── Animate elements on scroll ─── */
function initScrollAnimations() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('animate-in');
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });

  document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));
}

/* ─── Mobile Menu ─── */
function initMobileMenu() {
  const btn   = document.getElementById('mobile-menu-btn');
  const menu  = document.getElementById('mobile-menu');
  const close = document.getElementById('mobile-menu-close');

  if (!btn || !menu) return;

  btn.addEventListener('click', () => menu.classList.add('open'));
  close?.addEventListener('click', () => menu.classList.remove('open'));
  menu.addEventListener('click', e => {
    if (e.target === menu) menu.classList.remove('open');
  });
}

/* ─── Portal link (staff) ─── */
function goToStaffPortal(e) {
  e.preventDefault();
  window.location.href = '/ClinicOne/dist/browser/index.html#/doctor-login';
}

export { renderStars, showToast, formatDateAr, getToday, getParam, doctorCardSkeleton, initScrollAnimations, initMobileMenu, goToStaffPortal };
