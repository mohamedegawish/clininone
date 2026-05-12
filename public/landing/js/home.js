/**
 * home.js — ClinicOne Patient Portal Homepage Logic
 */

import { fetchDoctors, fetchSpecialties, fetchLocations } from './api.js';

console.log("home.js loaded");

/* ─── State ─── */
let allDoctors       = [];
let filteredDoctors  = [];
let locationMap      = {};
let selectedSpecialty = 'all';
let selectedCity      = 'all';
let selectedGovernorate = 'all';
let searchQuery      = '';

/* ─── DOM Refs ─── */
const doctorsGrid   = document.getElementById('doctors-grid');
const filterScroll  = document.getElementById('filter-scroll');
const searchInput   = document.getElementById('search-input');
const resultsCount  = document.getElementById('results-count');
const resultsFilter = document.getElementById('results-filter');
const statsCount    = document.getElementById('stats-doctors-count');
const citySelect    = document.getElementById('city-filter');
const govSelect     = document.getElementById('gov-filter');

/* ─── Mobile Drawer Logic ─── */
const btn   = document.getElementById('mobile-menu-btn');
const menu  = document.getElementById('mobile-drawer');
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
  const initial = (doctor.name || '?').replace('د. ', '').replace('Dr. ', '').charAt(0);
  const exp = doctor.experience || 0;
  const address = doctor.address || (document.documentElement.lang === 'ar' ? 'القاهرة' : 'Cairo');
  const hours = document.documentElement.lang === 'ar' ? '09:00 ص – 05:00 م' : '09:00 AM – 05:00 PM';
  
  const yearsExpText = doctorsGrid.dataset.yearsExp || 'سنوات خبرة';
  const consultationText = doctorsGrid.dataset.consultation || 'الاستشارة';
  const bookNowText = doctorsGrid.dataset.bookNow || 'احجز الآن';
  const currencyText = doctorsGrid.dataset.currency || 'EGP';

  return `
    <a class="doctor-card card-hover" href="/doctor?id=${doctor.id}" style="--i:${i}">
      <div class="doctor-card-top">
        <div class="avatar avatar-lg doctor-avatar" style="overflow: hidden">
          ${doctor.photo ? `<img src="${doctor.photo}" style="width: 100%; height: 100%; object-fit: cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'" />` : ''}
          <span ${doctor.photo ? 'style="display:none"' : ''}>${initial}</span>
        </div>
        <div class="doctor-info">
          <h3 class="doctor-name text-truncate" style="max-width: 180px;">${doctor.name}</h3>
          <span class="specialty-tag">${doctor.specialty}</span>
          <div class="doctor-rating">
            <div class="stars-row">${renderStars(doctor.rating)}</div>
            <span class="rating-val">${(doctor.rating||0).toFixed(1)}</span>
            <span class="review-count">(${doctor.reviewCount || 0})</span>
          </div>
        </div>
      </div>

      <div class="doctor-meta">
        <div class="meta-item">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="3" width="20" height="14" rx="2" />
            <path d="M8 21h8m-4-4v4" />
          </svg>
          ${exp} ${yearsExpText}
        </div>
        <div class="meta-item">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
            <circle cx="12" cy="10" r="3" />
          </svg>
          ${address}
        </div>
        <div class="meta-item">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
          </svg>
          ${hours}
        </div>
      </div>

      <div class="doctor-footer">
        <div class="doctor-price">
          <span class="price-label">${consultationText}</span>
          <span class="price-val">${currencyText} ${doctor.price || 0}</span>
        </div>
        <button class="btn btn-primary btn-sm" onclick="event.preventDefault(); window.location.href='/booking?id=${doctor.id}'">
          ${bookNowText}
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
    const noDocs = doctorsGrid.dataset.noDoctors || 'لا يوجد أطباء';
    const tryAdjust = doctorsGrid.dataset.tryAdjust || 'جرب تعديل البحث أو فلتر التخصص';
    const clearText = doctorsGrid.dataset.clearText || 'مسح الفلاتر';

    doctorsGrid.innerHTML = `
      <div class="empty-state" style="grid-column:1/-1">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--text-muted); opacity: 0.4">
          <circle cx="11" cy="11" r="8" />
          <path d="m21 21-4.35-4.35" />
        </svg>
        <h3>${noDocs}</h3>
        <p>${tryAdjust}</p>
        <button class="btn btn-ghost" onclick="clearFilters()">${clearText}</button>
      </div>`;
    return;
  }

  doctorsGrid.innerHTML = filteredDoctors.map((d, i) => renderDoctorCard(d, i)).join('');
  updateResultsInfo();
}

/* ─── Filter Functions ─── */
function normalizeArabic(text) {
  if (!text) return '';
  return text.trim()
    .replace(/[أإآ]/g, 'ا')
    .replace(/ة/g, 'ه')
    .replace(/ى/g, 'ي')
    .replace(/\s+/g, ' ');
}

function applyFilters() {
  let list = [...allDoctors];
  if (selectedSpecialty !== 'all') {
    const normSelected = normalizeArabic(selectedSpecialty);
    list = list.filter(d => normalizeArabic(d.specialty) === normSelected);
  }
  if (selectedCity !== 'all') {
    list = list.filter(d => d.city === selectedCity);
  }
  if (selectedGovernorate !== 'all') {
    list = list.filter(d => d.governorate === selectedGovernorate);
  }
  if (searchQuery.trim()) {
    const q = normalizeArabic(searchQuery.toLowerCase());
    list = list.filter(d => 
        normalizeArabic((d.name || '').toLowerCase()).includes(q) || 
        normalizeArabic((d.specialty || '').toLowerCase()).includes(q)
    );
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
  if (citySelect) citySelect.value = 'all';
  if (govSelect) govSelect.value = 'all';
  searchQuery = '';
  selectedCity = 'all';
  selectedGovernorate = 'all';
  applyFilters();
};

function updateResultsInfo() {
  const doctorText = resultsCount?.dataset.doctorText || 'طبيب';
  const inText = resultsCount?.dataset.inText || 'في';
  
  if (resultsCount) resultsCount.textContent = `${filteredDoctors.length} ${doctorText}`;
  if (resultsFilter) resultsFilter.textContent = selectedSpecialty !== 'all' ? `${inText} ${selectedSpecialty}` : '';
}

/* ─── Search Input ─── */
if (searchInput) {
  searchInput.addEventListener('input', e => {
    searchQuery = e.target.value;
    applyFilters();
  });
}

if (citySelect) {
  citySelect.addEventListener('change', e => {
    selectedCity = e.target.value;
    applyFilters();
  });
}

if (govSelect) {
  govSelect.addEventListener('change', e => {
    selectedGovernorate = e.target.value;
    updateCityOptions();
    applyFilters();
  });
}

function updateCityOptions() {
  if (!citySelect) return;
  
  // Clear current options except 'all'
  const allText = citySelect.options[0].textContent;
  citySelect.innerHTML = `<option value="all" style="color: black;">${allText}</option>`;
  
  let citiesToShow = [];
  if (selectedGovernorate === 'all') {
    // Show all cities from all governorates
    Object.values(locationMap).forEach(cities => {
      citiesToShow = citiesToShow.concat(cities);
    });
  } else if (locationMap[selectedGovernorate]) {
    citiesToShow = locationMap[selectedGovernorate];
  }
  
  // Unique and sorted
  citiesToShow = [...new Set(citiesToShow)].sort();
  
  citiesToShow.forEach(city => {
    const opt = document.createElement('option');
    opt.value = city;
    opt.textContent = city;
    opt.style.color = 'black';
    citySelect.appendChild(opt);
  });
  
  // Reset selected city if not in the new list
  if (selectedCity !== 'all' && !citiesToShow.includes(selectedCity)) {
    selectedCity = 'all';
    citySelect.value = 'all';
  }
}

/* ─── Init ─── */
async function init() {
  console.log("init started");

  try {
    console.log("fetching data...");
    const [doctors, specialties, locations] = await Promise.all([ 
      fetchDoctors(), 
      fetchSpecialties(),
      fetchLocations()
    ]);
    console.log("data received:", doctors);
    allDoctors = doctors;
    filteredDoctors = [...doctors];
    locationMap = locations.map || {};

    // Populate specialties
    if (filterScroll) {
      const allText = doctorsGrid.dataset.allText || 'الكل';
      filterScroll.innerHTML = `<button class="specialty-btn ${selectedSpecialty === 'all' ? 'active' : ''}" data-spec="all" onclick="setSpecialty('all')">${allText}</button>` +
        specialties.map(s => `<button class="specialty-btn" data-spec="${s}" onclick="setSpecialty('${s}')">${s}</button>`).join('');
    }

    // Populate governorates
    if (govSelect && locations.governorates) {
      locations.governorates.sort().forEach(gov => {
        const opt = document.createElement('option');
        opt.value = gov;
        opt.textContent = gov;
        opt.style.color = 'black';
        govSelect.appendChild(opt);
      });
    }

    // Initial populate of cities
    updateCityOptions();

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
    console.error("Init Error:", err);
    if (doctorsGrid) doctorsGrid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:40px;color:red">فشل تحميل البيانات. الرجاء المحاولة لاحقاً.</div>';
  }
}

// Call init directly since it's a module
init();
