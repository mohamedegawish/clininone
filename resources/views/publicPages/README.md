# ClinicOne Patient Portal

بوابة المريض — Vanilla JS / CSS

## هيكل المجلدات

```
patient-portal/
├── index.html          ← الصفحة الرئيسية
├── doctor.html         ← ملف الطبيب وتفاصيله
├── booking.html        ← صفحة الحجز (3 خطوات)
│
├── css/
│   ├── main.css        ← نظام التصميم المشترك
│   ├── home.css        ← أنماط الصفحة الرئيسية
│   ├── doctor.css      ← أنماط ملف الطبيب
│   └── booking.css     ← أنماط صفحة الحجز
│
└── js/
    ├── api.js          ← طبقة API (Laravel + Mock Data)
    ├── app.js          ← أدوات مشتركة
    ├── home.js         ← منطق الصفحة الرئيسية
    ├── doctor.js       ← منطق ملف الطبيب
    └── booking.js      ← منطق الحجز
```

## الميزات

### 📄 الصفحة الرئيسية (`index.html`)
- Hero section مع صورة خلفية وتأثيرات CSS متحركة
- بحث فوري بالاسم أو التخصص
- فلترة الأطباء حسب التخصص
- شبكة كروت الأطباء مع تأثير skeleton loading
- إحصائيات متحركة
- Responsive كامل مع قائمة جانبية للموبايل

### 👨‍⚕️ ملف الطبيب (`doctor.html`)
- بانر علوي يعرض: صورة، تخصص، تقييم، موقع، خبرة
- إحصائيات (خبرة، تقييم، عدد التقييمات)
- جدول مواعيد الأسبوع
- قائمة تقييمات المرضى
- نموذج إضافة تقييم مع star rating
- Sidebar ثابتة بجانب يمين يعرض المواعيد المتاحة وزر الحجز

### 📅 صفحة الحجز (`booking.html`)
- **الخطوة 1**: اختيار نوع الزيارة (جديد / إعادة) + اختيار الميعاد
- **الخطوة 2**: بيانات المريض (اسم، هاتف، عمر، جنس، ملاحظات)
- **الخطوة 3**: ملخص الدفع + تأكيد الحجز
- **شاشة التأكيد**: رقم الحجز ورقم الدور في الطابور

## الـ API

يتصل بـ `http://127.0.0.1:8000/api` (Laravel Backend)

**Endpoints المستخدمة:**
- `GET /public/doctors` — قائمة الأطباء
- `GET /public/doctors/{id}` — تفاصيل طبيب
- `GET /public/specialties` — التخصصات
- `GET /public/appointments/available-slots` — المواعيد المتاحة
- `POST /public/appointments/book` — حجز موعد
- `GET /public/reviews?doctor_id=` — التقييمات
- `POST /public/reviews` — إضافة تقييم

> **ملاحظة:** عند عدم توفر الـ API تلقائياً يستخدم البرنامج Mock Data.

## تشغيل المشروع

افتح XAMPP وشغّل Apache، ثم اذهب إلى:
```
http://localhost/ClinicOne/patient-portal/
```
