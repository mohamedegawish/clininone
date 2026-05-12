/**
 * api.js — ClinicOne Patient Portal
 * Handles all API calls to the Laravel backend
 */

const API_BASE = '/api';
const CURRENT_LOCALE = document.documentElement.lang || 'ar';


/* ──────────────────────────────────────────
   API Functions
   ────────────────────────────────────────── */

async function fetchDoctors(specialty = null) {
  try {
    const url = specialty && specialty !== 'all'
      ? `${API_BASE}/public/doctors?specialty=${encodeURIComponent(specialty)}`
      : `${API_BASE}/public/doctors`;
    
    const res = await fetch(url, {
      headers: { 'Accept-Language': CURRENT_LOCALE }
    });
    if (!res.ok) throw new Error('API error: ' + res.status);
    const data = await res.json();
    const result = data.data || data;
    return Array.isArray(result) ? result : (result.data || []);
  } catch (err) {
    console.error("fetchDoctors failed:", err);
    return MOCK_DOCTORS;
  }
}

async function fetchDoctor(id) {
  try {
    const res = await fetch(`${API_BASE}/public/doctors/${id}`, {
      headers: { 'Accept-Language': CURRENT_LOCALE }
    });
    if (!res.ok) throw new Error('Not found: ' + res.status);
    const data = await res.json();
    return data.data || data;
  } catch (err) {
    console.error("fetchDoctor failed:", err);
    return MOCK_DOCTORS.find(d => d.id == id) || MOCK_DOCTORS[0];
  }
}

async function fetchSpecialties() {
  try {
    const res = await fetch(`${API_BASE}/public/specialties`, {
      headers: { 'Accept-Language': CURRENT_LOCALE }
    });
    if (!res.ok) throw new Error('API error');
    const data = await res.json();
    return data.data || data;
  } catch (err) {
    console.error("fetchSpecialties failed:", err);
    return [
      'تخصص القلب', 'تخصص الجلدية', 'تخصص الباطنة', 'تخصص العظام', 'تخصص الأطفال', 'تخصص العيون', 'تخصص النساء والتوليد', 'تخصص الأسنان', 'تخصص المخ والأعصاب',
      'تخصص الأنف والأذن والحنجرة', 'تخصص المسالك البولية', 'تخصص الجراحة العامة', 'تخصص العلاج الطبيعي', 'تخصص التغذية', 'تخصص الأمراض النفسية'
    ];
  }
}

async function fetchLocations() {
  try {
    const res = await fetch(`${API_BASE}/public/locations`, {
      headers: { 'Accept-Language': CURRENT_LOCALE }
    });
    if (!res.ok) throw new Error('API error');
    const data = await res.json();
    return data.data || data;
  } catch (err) {
    console.error("fetchLocations failed:", err);
    return { 
      cities: ['مدينة نصر', 'المعادي', 'التجمع', 'الدقي', 'المهندسين', 'سيدي جابر'], 
      governorates: [
        'القاهرة', 'الجيزة', 'الإسكندرية', 'القليوبية', 'الغربية', 'المنوفية', 'الشرقية', 'الدقهلية', 'كفر الشيخ', 'البحيرة',
        'الإسماعيلية', 'بورسعيد', 'السويس', 'دمياط', 'شمال سيناء', 'جنوب سيناء', 'البحر الأحمر', 'مطروح',
        'الفيوم', 'بني سويف', 'المنيا', 'أسيوط', 'سوهاج', 'قنا', 'الأقصر', 'أسوان', 'الوادي الجديد'
      ],
      map: {
        'القاهرة': ['مدينة نصر', 'مصر الجديدة', 'المعادي', 'حلوان', 'شبرا', 'التجمع', 'عين شمس', 'السلام'],
        'الجيزة': ['الدقي', 'المهندسين', 'الهرم', 'فيصل', 'أكتوبر', 'الشيخ زايد'],
        'الإسكندرية': ['سيدي جابر', 'المنتزه', 'العجمي', 'محرم بك', 'برج العرب'],
        'القليوبية': ['بنها', 'شبرا الخيمة', 'قليوب', 'القناطر الخيرية', 'طوخ'],
        'الغربية': ['طنطا', 'المحلة الكبرى', 'زفتى', 'كفر الزيات'],
        'المنوفية': ['شبين الكوم', 'السادات', 'منوف', 'أشمون'],
        'الشرقية': ['الزقازيق', 'العاشر من رمضان', 'بلبيس', 'منيا القمح'],
        'الدقهلية': ['المنصورة', 'ميت غمر', 'طلخا', 'بلقاس'],
        'كفر الشيخ': ['دسوق', 'بيلا', 'الحامول', 'بلطيم'],
        'البحيرة': ['دمنهور', 'كفر الدوار', 'رشيد', 'إدكو'],
        'الإسماعيلية': ['الإسماعيلية', 'فايد', 'القنطرة'],
        'بورسعيد': ['بورسعيد', 'بورفؤاد'],
        'السويس': ['السويس', 'الجناين', 'عتاقة'],
        'دمياط': ['دمياط', 'رأس البر', 'فارسكور', 'كفر سعد'],
        'الفيوم': ['الفيوم', 'سنورس', 'إطسا'],
        'بني سويف': ['بني سويف', 'الواسطى', 'الفشن'],
        'المنيا': ['المنيا', 'ملوي', 'بني مزار', 'سمالوط'],
        'أسيوط': ['أسيوط', 'ديروط', 'منفلوط'],
        'سوهاج': ['سوهاج', 'أخميم', 'جرجا', 'طهطا'],
        'قنا': ['قنا', 'نجع حمادي', 'دشنا'],
        'الأقصر': ['الأقصر', 'إسنا', 'أرمنت'],
        'أسوان': ['أسوان', 'كوم أمبو', 'إدفو']
      }
    };
  }
}

async function fetchAvailableSlots(doctorId, date, clinicId = null) {
  try {
    let url = `${API_BASE}/public/appointments/available-slots?doctor_id=${doctorId}&date=${date}`;
    if (clinicId) url += `&clinic_id=${clinicId}`;
    
    const res = await fetch(url, {
      headers: { 'Accept-Language': CURRENT_LOCALE }
    });
    if (!res.ok) throw new Error('API error: ' + res.status);
    const data = await res.json();
    const result = data.data || data;
    
    // Extract array from various possible structures
    if (Array.isArray(result)) return result;
    if (result.available_slots && Array.isArray(result.available_slots)) return result.available_slots;
    if (result.slots && Array.isArray(result.slots)) return result.slots;
    
    return [];
  } catch (err) {
    console.error("fetchAvailableSlots failed:", err);
    return ['09:00 ص', '10:00 ص', '11:00 ص', '01:00 م', '02:00 م'];
  }
}

async function bookAppointment(payload) {
  const res = await fetch(`${API_BASE}/public/appointments/book`, {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Accept-Language': CURRENT_LOCALE
    },
    body: JSON.stringify(payload)
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({ message: 'خطأ في الحجز' }));
    throw new Error(err.message || 'Booking failed');
  }
  return await res.json();
}

async function fetchReviews(doctorId) {
  try {
    const res = await fetch(`${API_BASE}/public/reviews?doctor_id=${doctorId}`, {
      headers: { 'Accept-Language': CURRENT_LOCALE }
    });
    if (!res.ok) throw new Error('API error');
    const data = await res.json();
    const result = data.data || data;
    return Array.isArray(result) ? result : [];
  } catch {
    return [];
  }
}

export { fetchDoctors, fetchDoctor, fetchSpecialties, fetchLocations, fetchAvailableSlots, bookAppointment, fetchReviews };
