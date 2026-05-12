/**
 * api.js — ClinicOne Patient Portal
 * Handles all API calls to the Laravel backend
 */

const API_BASE = 'http://127.0.0.1:8000/api';

/* ──────────────────────────────────────────
   Mock Data (fallback when API is offline)
   ────────────────────────────────────────── */
const MOCK_DOCTORS = [
  {
    id: 1, name: 'د. محمد الطناني', specialty: 'تخصص القلب',
    experience_years: 14, qualification: 'دكتوراه أمراض القلب',
    bio: 'متخصص في أمراض القلب والأوعية الدموية مع خبرة واسعة في التدخلات الجراحية.',
    gender: 'male', governorate: 'القاهرة', city: 'مدينة نصر',
    rating: 4.9, reviewCount: 312, price: 350,
    photo: null,
    availableSlots: ['09:00 ص','09:30 ص','10:00 ص','10:30 ص','11:00 ص','11:30 ص','12:00 م','12:30 م','02:00 م','02:30 م','03:00 م','03:30 م'],
    schedule: {السبت:'9ص-1م', الأحد:'9ص-1م', الاثنين:'2م-6م', الثلاثاء:'9ص-1م', الأربعاء:null, الخميس:'9ص-1م', الجمعة:null},
    status: 'active', clinicId: 1
  },
  {
    id: 2, name: 'د. عمرو سكوت', specialty: 'تخصص الجلدية',
    experience_years: 9, qualification: 'بورد الجلدية والتجميل',
    bio: 'خبير في علاج أمراض الجلد والليزر وإجراءات التجميل الطبية.',
    gender: 'male', governorate: 'الجيزة', city: 'المهندسين',
    rating: 4.7, reviewCount: 189, price: 280,
    photo: null,
    availableSlots: ['10:00 ص','10:30 ص','11:00 ص','11:30 ص','02:00 م','02:30 م','03:00 م','03:30 م','04:00 م'],
    schedule: {السبت:'10ص-1م', الأحد:null, الاثنين:'2م-5م', الثلاثاء:null, الأربعاء:'10ص-1م', الخميس:'2م-5م', الجمعة:null},
    status: 'active', clinicId: 1
  },
  {
    id: 3, name: 'د. أسامة مرزوق', specialty: 'تخصص الأعصاب',
    experience_years: 18, qualification: 'أستاذ طب الأعصاب',
    bio: 'رائد في علاج اضطرابات الجهاز العصبي المركزي والمحيطي.',
    gender: 'male', governorate: 'القاهرة', city: 'المعادي',
    rating: 4.8, reviewCount: 245, price: 400,
    photo: null,
    availableSlots: ['09:00 ص','09:30 ص','10:00 ص','10:30 ص','11:00 ص','03:00 م','03:30 م','04:00 م'],
    schedule: {السبت:'9ص-12م', الأحد:'3م-6م', الاثنين:null, الثلاثاء:'9ص-12م', الأربعاء:'3م-6م', الخميس:null, الجمعة:null},
    status: 'active', clinicId: 1
  },
  {
    id: 4, name: 'د. إبراهيم شبار', specialty: 'تخصص العظام',
    experience_years: 12, qualification: 'ماجستير جراحة العظام',
    bio: 'متخصص في جراحة العظام والمفاصل وإعادة التأهيل بعد الكسور.',
    gender: 'male', governorate: 'الإسكندرية', city: 'سيدي جابر',
    rating: 4.6, reviewCount: 134, price: 320,
    photo: null,
    availableSlots: ['11:00 ص','11:30 ص','12:00 م','12:30 م','01:00 م','04:00 م','04:30 م','05:00 م'],
    schedule: {السبت:null, الأحد:'11ص-2م', الاثنين:'4م-7م', الثلاثاء:'11ص-2م', الأربعاء:null, الخميس:'4م-7م', الجمعة:null},
    status: 'active', clinicId: 2
  },
  {
    id: 5, name: 'د. مصطفى خريم', specialty: 'تخصص الأطفال',
    experience_years: 11, qualification: 'زمالة طب الأطفال',
    bio: 'طبيب أطفال متخصص في طب حديثي الولادة ومتابعة نمو الأطفال.',
    gender: 'male', governorate: 'القاهرة', city: 'عين شمس',
    rating: 4.9, reviewCount: 378, price: 250,
    photo: null,
    availableSlots: ['09:00 ص','09:30 ص','10:00 ص','10:30 ص','11:00 ص','11:30 ص','12:00 م','01:00 م'],
    schedule: {السبت:'9ص-1م', الأحد:'9ص-1م', الاثنين:'9ص-1م', الثلاثاء:'9ص-1م', الأربعاء:null, الخميس:'9ص-12م', الجمعة:null},
    status: 'active', clinicId: 1
  },
  {
    id: 6, name: 'د. نشأت مندور', specialty: 'طب عام',
    experience_years: 7, qualification: 'بكالوريوس الطب والجراحة',
    bio: 'طبيب عام يوفر رعاية شاملة لجميع الأعمار مع تخصص في الأمراض المزمنة.',
    gender: 'male', governorate: 'الجيزة', city: 'إمبابة',
    rating: 4.5, reviewCount: 89, price: 180,
    photo: null,
    availableSlots: ['08:00 ص','08:30 ص','09:00 ص','09:30 ص','10:00 ص','05:00 م','05:30 م','06:00 م'],
    schedule: {السبت:'8ص-10ص', الأحد:'8ص-10ص', الاثنين:'5م-8م', الثلاثاء:'8ص-10ص', الأربعاء:'5م-8م', الخميس:'8ص-10ص', الجمعة:null},
    status: 'active', clinicId: 2
  },
  {
    id: 7, name: 'د. عبد الوهاب محمد', specialty: 'تخصص العيون',
    experience_years: 16, qualification: 'أستاذ طب وجراحة العيون',
    bio: 'رائد في جراحة الليزك والمياه البيضاء وعلاج أمراض الشبكية.',
    gender: 'male', governorate: 'القاهرة', city: 'وسط البلد',
    rating: 4.8, reviewCount: 201, price: 380,
    photo: null,
    availableSlots: ['10:00 ص','10:30 ص','11:00 ص','11:30 ص','12:00 م','02:00 م','02:30 م','03:00 م'],
    schedule: {السبت:'10ص-1م', الأحد:null, الاثنين:'10ص-1م', الثلاثاء:null, الأربعاء:'2م-5م', الخميس:'10ص-1م', الجمعة:null},
    status: 'active', clinicId: 1
  },
  {
    id: 8, name: 'د. أحمد الأباصيري', specialty: 'تخصص الأسنان',
    experience_years: 8, qualification: 'ماجستير طب الأسنان',
    bio: 'متخصص في زراعة الأسنان والتجميل وتقويم الأسنان بأحدث الأساليب.',
    gender: 'male', governorate: 'الجيزة', city: 'الدقي',
    rating: 4.7, reviewCount: 156, price: 300,
    photo: null,
    availableSlots: ['09:00 ص','09:30 ص','10:00 ص','03:00 م','03:30 م','04:00 م','04:30 م','05:00 م'],
    schedule: {السبت:'9ص-11ص', الأحد:'3م-6م', الاثنين:'9ص-11ص', الثلاثاء:'3م-6م', الأربعاء:'9ص-11ص', الخميس:null, الجمعة:null},
    status: 'active', clinicId: 2
  },
  {
    id: 9, name: 'د. محمد عبادة', specialty: 'تخصص الغدد',
    experience_years: 13, qualification: 'دكتوراه الغدد الصماء',
    bio: 'متخصص في أمراض الغدة الدرقية والسكري واضطرابات الهرمونات.',
    gender: 'male', governorate: 'القاهرة', city: 'مصر الجديدة',
    rating: 4.6, reviewCount: 118, price: 350,
    photo: null,
    availableSlots: ['10:00 ص','10:30 ص','11:00 ص','11:30 ص','12:00 م','01:00 م'],
    schedule: {السبت:null, الأحد:'10ص-2م', الاثنين:null, الثلاثاء:'10ص-2م', الأربعاء:null, الخميس:'10ص-1م', الجمعة:null},
    status: 'active', clinicId: 1
  },
  {
    id: 10, name: 'د. شريف حمودة', specialty: 'تخصص الجهاز الهضمي',
    experience_years: 10, qualification: 'زمالة أمراض الجهاز الهضمي',
    bio: 'خبير في تشخيص وعلاج أمراض الجهاز الهضمي والكبد بالمنظار.',
    gender: 'male', governorate: 'الإسكندرية', city: 'لوران',
    rating: 4.7, reviewCount: 143, price: 330,
    photo: null,
    availableSlots: ['09:00 ص','09:30 ص','10:00 ص','10:30 ص','02:00 م','02:30 م','03:00 م'],
    schedule: {السبت:'9ص-12م', الأحد:null, الاثنين:'2م-5م', الثلاثاء:'9ص-12م', الأربعاء:null, الخميس:'2م-5م', الجمعة:null},
    status: 'active', clinicId: 2
  }
];

const MOCK_REVIEWS = {
  1: [
    { id:1, reviewer_name:'أحمد محمود', rating:5, comment:'دكتور ممتاز جداً، شرح الحالة بالتفصيل وكان صبور مع الأسئلة.', created_at:'2025-04-10' },
    { id:2, reviewer_name:'سارة علي', rating:5, comment:'من أفضل الأطباء اللي زرتهم. التشخيص كان دقيق جداً.', created_at:'2025-04-05' },
    { id:3, reviewer_name:'محمد عبد الله', rating:4, comment:'خبرة عالية في المجال، بس الانتظار كان طويل شوية.', created_at:'2025-03-28' },
  ],
  2: [
    { id:4, reviewer_name:'هدى إبراهيم', rating:5, comment:'علاج ممتاز لمشكلة الجلد. النتيجة ظهرت بسرعة.', created_at:'2025-04-08' },
    { id:5, reviewer_name:'كريم الشافعي', rating:4, comment:'دكتور محترم ومتمكن من تخصصه.', created_at:'2025-04-01' },
  ]
};

/* ──────────────────────────────────────────
   API Functions
   ────────────────────────────────────────── */

/**
 * Fetch all public doctors (with optional specialty filter)
 */
async function fetchDoctors(specialty = null) {
  try {
    const url = specialty && specialty !== 'all'
      ? `${API_BASE}/public/doctors?specialty=${encodeURIComponent(specialty)}`
      : `${API_BASE}/public/doctors`;
    const res = await fetch(url, { signal: AbortSignal.timeout(5000) });
    if (!res.ok) throw new Error('API error');
    const data = await res.json();
    return data.data || data;
  } catch {
    // Return mock data if API is offline
    if (specialty && specialty !== 'all') {
      return MOCK_DOCTORS.filter(d => d.specialty === specialty);
    }
    return MOCK_DOCTORS;
  }
}

/**
 * Fetch a single doctor by ID
 */
async function fetchDoctor(id) {
  try {
    const res = await fetch(`${API_BASE}/public/doctors/${id}`, { signal: AbortSignal.timeout(5000) });
    if (!res.ok) throw new Error('Not found');
    const data = await res.json();
    return data.data || data;
  } catch {
    return MOCK_DOCTORS.find(d => d.id == id) || null;
  }
}

/**
 * Fetch specialties list
 */
async function fetchSpecialties() {
  try {
    const res = await fetch(`${API_BASE}/public/specialties`, { signal: AbortSignal.timeout(5000) });
    if (!res.ok) throw new Error('API error');
    const data = await res.json();
    return data.data || data;
  } catch {
    return [...new Set(MOCK_DOCTORS.map(d => d.specialty))];
  }
}

/**
 * Fetch available slots for a doctor on a given date
 */
async function fetchAvailableSlots(doctorId, date, clinicId = 1) {
  try {
    const url = `${API_BASE}/public/appointments/available-slots?doctor_id=${doctorId}&date=${date}&clinic_id=${clinicId}`;
    const res = await fetch(url, { signal: AbortSignal.timeout(5000) });
    if (!res.ok) throw new Error('API error');
    const data = await res.json();
    return data.slots || data.data || data;
  } catch {
    const doctor = MOCK_DOCTORS.find(d => d.id == doctorId);
    return doctor ? doctor.availableSlots : [];
  }
}

/**
 * Book an appointment (public endpoint)
 */
async function bookAppointment(payload) {
  try {
    const res = await fetch(`${API_BASE}/public/appointments/book`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
      signal: AbortSignal.timeout(10000)
    });
    if (!res.ok) {
      const err = await res.json();
      throw new Error(err.message || 'Booking failed');
    }
    return await res.json();
  } catch (e) {
    if (e.name === 'AbortError' || e.message.includes('fetch')) {
      // Simulate success in offline mode
      return {
        success: true,
        data: {
          id: Math.floor(Math.random() * 9000) + 1000,
          queue_number: Math.floor(Math.random() * 20) + 1,
          ...payload
        }
      };
    }
    throw e;
  }
}

/**
 * Fetch reviews for a doctor
 */
async function fetchReviews(doctorId) {
  try {
    const res = await fetch(`${API_BASE}/public/reviews?doctor_id=${doctorId}`, { signal: AbortSignal.timeout(5000) });
    if (!res.ok) throw new Error('API error');
    const data = await res.json();
    return data.data || data;
  } catch {
    return MOCK_REVIEWS[doctorId] || [];
  }
}

/**
 * Submit a review for a doctor
 */
async function submitReview(payload) {
  try {
    const res = await fetch(`${API_BASE}/public/reviews`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
      signal: AbortSignal.timeout(8000)
    });
    if (!res.ok) {
      const err = await res.json();
      throw new Error(err.message || 'Submit failed');
    }
    return await res.json();
  } catch (e) {
    if (e.name === 'AbortError' || e.message.includes('fetch')) {
      return { success: true, data: { id: Date.now(), ...payload } };
    }
    throw e;
  }
}

export { fetchDoctors, fetchDoctor, fetchSpecialties, fetchAvailableSlots, bookAppointment, fetchReviews, submitReview, MOCK_DOCTORS };
