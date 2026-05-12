/**
 * home.js — ClinicOne Patient Portal Homepage Logic (Vanilla JS matching Angular layout)
 */

import { fetchDoctors, fetchSpecialties } from './api.js';

/* ─── State ─── */
let allDoctors       = [];
let filteredDoctors  = [];
let selectedSpecialty = 'all';
let searchQuery      = '';

/* ─── DOM Refs ─── */
const doctorsGrid   = document.getElementById('doctors-grid');
const filterScroll  = document.getElementById('filter-scroll');
const searchInput   = document.getElementById('search-input');
const resultsCount  = document.getElementById('results-count');
const resultsFilter = document.getElementById('results-filter');
const statsCount    = document.getElementById('stats-doctors-count');

/* ─── Mobile Drawer Logic ─── */
const btn   = document.getElementById('mobile-menu-btn');
const menu  = document.getElementById('mobile-menu');
const close = document.getElementById('mobile-menu-close');
const overlay = document.getElementById('mobile-overlay');

if (btn && menu && overlay && close) {
  const openMenu = () => { menu.classList.add('open'); overlay.classList.add('open'); };
  const closeMenu = () => { menu.classList.remove('open'); overlay.classList.remove('open'); };
  btn.addEventListener('click', openMenu);
  close.addEventListener('click', closeMenu);
  overlay.addEventListener('click', closeMenu);
}

/* ─── Render Stars ─── */
function renderStars(rating) {
  return Array(5).fill(0).map((_, i) => {
    const filled = i < Math.round(rating || 0);
    return `<svg width="13" height="13" viewBox="0 0 24 24"
      fill="${filled ? '#F59E0B' : 'none'}"
      stroke="${filled ? '#F59E0B' : '#475569'}" stroke-width="2">
      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
    </svg>`;
  }).join('');
}

/* ─── Render Doctor Card ─── */
function renderDoctorCard(doctor, i) {
  const initial = (doctor.name || '?').replace('د. ', '').charAt(0);
  const exp = doctor.experience_years || doctor.experience || 0;
  const address = doctor.address || doctor.city || doctor.governorate || 'غير محدد';
  const hours = doctor.workHours ? \`\${doctor.workHours.start} – \${doctor.workHours.end}\` : 'متاح';

  return `
    <a class="doctor-card card-hover" href="doctor.html?id=\${doctor.id}" style="--i:\${i}">
      <div class="doctor-card-top">
        <div class="avatar avatar-lg doctor-avatar" style="overflow: hidden">
          \${doctor.photo ? \`<img src="\${doctor.photo}" style="width: 100%; height: 100%; object-fit: cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'" />\` : ''}
          <span \${doctor.photo ? 'style="display:none"' : ''}>\${initial}</span>
        </div>
        <div class="doctor-info">
          <h3 class="doctor-name">\${doctor.name}</h3>
          <span class="specialty-tag">\${doctor.specialty}</span>
          <div class="doctor-rating">
            <div class="stars-row">\${renderStars(doctor.rating)}</div>
            <span class="rating-val">\${(doctor.rating||0).toFixed(1)}</span>
            <span class="review-count">(\${doctor.reviewCount || doctor.reviews_count || 0})</span>
          </div>
        </div>
      </div>

      <div class="doctor-meta">
        <div class="meta-item">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="3" width="20" height="14" rx="2" />
            <path d="M8 21h8m-4-4v4" />
          </svg>
          \${exp} سنوات خبرة
        </div>
        <div class="meta-item">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
          \${address}
        </div>
        <div class="meta-item">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
          </svg>
          \${hours}
        </div>
      </div>

      <div class="doctor-footer">
        <div class="doctor-price">
          <span class="price-label">الاستشارة</span>
          <span class="price-val">EGP \${doctor.price || 0}</span>
        </div>
        <button class="btn btn-primary btn-sm" onclick="event.preventDefault(); window.location.href='booking.html?id=\${doctor.id}'">
          احجز الآن
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M5 12h14m-7-7 7 7-7 7" />
          </svg>
        </button>
      </div>
    </a>`;
}

/* ─── Render Grid ─── */
function renderGrid() {
  if (!doctorsGrid) return;

  if (filteredDoctors.length === 0) {
    doctorsGrid.innerHTML = `
      <div class="empty-state" style="grid-column:1/-1">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--text-muted); opacity: 0.4">
          <circle cx="11" cy="11" r="8" />
          <path d="m21 21-4.35-4.35" />
        </svg>
        <h3>لا يوجد أطباء</h3>
        <p>جرب تعديل البحث أو فلتر التخصص</p>
        <button class="btn btn-ghost" onclick="clearFilters()">مسح الفلاتر</button>
      </div>`;
    return;
  }

  doctorsGrid.innerHTML = filteredDoctors.map((d, i) => renderDoctorCard(d, i)).join('');
  updateResultsInfo();
}

/* ─── Filter Functions ─── */
function applyFilters() {
  let list = [...allDoctors];
  if (selectedSpecialty !== 'all') {
    list = list.filter(d => d.specialty === selectedSpecialty);
  }
  if (searchQuery.trim()) {
    const q = searchQuery.trim().toLowerCase();
    list = list.filter(d => (d.name || '').toLowerCase().includes(q) || (d.specialty || '').toLowerCase().includes(q));
  }
  filteredDoctors = list;
  renderGrid();
}

window.setSpecialty = function(spec) {
  selectedSpecialty = spec;
  document.querySelectorAll('.specialty-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.spec === spec);
  });
  applyFilters();
};

window.clearFilters = function() {
  window.setSpecialty('all');
  if (searchInput) searchInput.value = '';
  searchQuery = '';
  applyFilters();
};

function updateResultsInfo() {
  if (resultsCount) resultsCount.textContent = \`\${filteredDoctors.length} طبيب\`;
  if (resultsFilter) resultsFilter.textContent = selectedSpecialty !== 'all' ? \`في \${selectedSpecialty}\` : '';
}

/* ─── Search Input ─── */
if (searchInput) {
  searchInput.addEventListener('input', e => {
    searchQuery = e.target.value;
    applyFilters();
  });
}

/* ─── Init ─── */
async function init() {
  if (doctorsGrid) {
    doctorsGrid.innerHTML = Array(6).fill().map(() => `
      <div class="doctor-card" style="padding:22px;border:1px solid rgba(255,255,255,0.25);border-radius:20px;height:240px">
        <div style="width:100%;height:100%;background:rgba(255,255,255,0.05);border-radius:10px;animation:pulse 1.5s infinite"></div>
      </div>
    `).join('');
  }

  try {
    const [doctors, specialties] = await Promise.all([ fetchDoctors(), fetchSpecialties() ]);
    allDoctors = doctors;
    filteredDoctors = [...doctors];

    if (filterScroll) {
      filterScroll.innerHTML = \`<button class="specialty-btn \${selectedSpecialty === 'all' ? 'active' : ''}" data-spec="all" onclick="setSpecialty('all')">الكل</button>\` +
        specialties.map(s => \`<button class="specialty-btn" data-spec="\${s}" onclick="setSpecialty('\${s}')">\${s}</button>\`).join('');
    }

    renderGrid();

    if (statsCount) {
      let n = 0;
      const target = doctors.length;
      const interval = setInterval(() => {
        n = Math.min(n + Math.ceil(target / 15), target);
        statsCount.textContent = n + '+';
        if (n >= target) clearInterval(interval);
      }, 50);
    }
  } catch (err) {
    console.error(err);
    if (doctorsGrid) doctorsGrid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:red">فشل تحميل البيانات. الرجاء المحاولة لاحقاً.</div>';
  }
}

document.addEventListener('DOMContentLoaded', init);
