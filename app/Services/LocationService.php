<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class LocationService
{
    public static function getAll(): array
    {
        $locale = App::getLocale();

        if ($locale === 'en') {
            return [
                'Cairo' => ['Nasr City', 'Heliopolis', 'Maadi', 'Helwan', 'Shubra', 'New Cairo', 'Ain Shams', 'El Salam'],
                'Giza' => ['Dokki', 'Mohandessin', 'Haram', 'Faisal', 'October', 'Sheikh Zayed'],
                'Qalyubia' => ['Benha', 'Shubra El Kheima', 'Qalyub', 'El Qanater', 'Toukh'],
                'Alexandria' => ['Sidi Gaber', 'Montaza', 'Agami', 'Moharam Bek', 'Borg El Arab'],
                'Beheira' => ['Damanhour', 'Kafr El Dawar', 'Rashid', 'Edko'],
                'Kafr El Sheikh' => ['Desouk', 'Bila', 'Hamoul', 'Baltim'],
                'Gharbia' => ['Tanta', 'Mahalla', 'Zefta', 'Kafr El Zayat'],
                'Monufia' => ['Shibin El Kom', 'Sadat', 'Menouf', 'Ashmoun'],
                'Dakahlia' => ['Mansoura', 'Mit Ghamr', 'Talkha', 'Belqas'],
                'Damietta' => ['Damietta', 'Ras El Bar', 'Faraskour', 'Kafr Saad'],
                'Sharqia' => ['Zagazig', '10th of Ramadan', 'Belbeis', 'Minya El Qamh'],
                'Fayoum' => ['Fayoum', 'Senouris', 'Itsa'],
                'Beni Suef' => ['Beni Suef', 'Wasta', 'Fashn'],
                'Minya' => ['Minya', 'Mallawi', 'Beni Mazar', 'Samalout'],
                'Asyut' => ['Asyut', 'Dairut', 'Manfalut'],
                'Sohag' => ['Sohag', 'Akhmim', 'Girga', 'Tahta'],
                'Qena' => ['Qena', 'Nag Hammadi', 'Deshna'],
                'Luxor' => ['Luxor', 'Esna', 'Armant'],
                'Aswan' => ['Aswan', 'Kom Ombo', 'Edfu'],
                'Ismailia' => ['Ismailia', 'Fayed', 'Qantara'],
                'Suez' => ['Suez', 'Ganayen', 'Attaka'],
                'Port Said' => ['Port Said', 'Fuad'],
                'North Sinai' => ['Arish', 'Sheikh Zuweid', 'Rafah'],
                'South Sinai' => ['Sharm El Sheikh', 'Dahab', 'Tor'],
                'Matrouh' => ['Marsa Matrouh', 'Alamein', 'Sidi Barrani'],
                'Red Sea' => ['Hurghada', 'Safaga', 'Quseir', 'Marsa Alam'],
                'New Valley' => ['Kharga', 'Dakhla', 'Farafra'],
            ];
        }

        return [
            'القاهرة' => ['مدينة نصر', 'مصر الجديدة', 'المعادي', 'حلوان', 'شبرا', 'التجمع', 'عين شمس', 'السلام'],
            'الجيزة' => ['الدقي', 'المهندسين', 'الهرم', 'فيصل', 'أكتوبر', 'الشيخ زايد'],
            'القليوبية' => ['بنها', 'شبرا الخيمة', 'قليوب', 'القناطر الخيرية', 'طوخ'],
            'الإسكندرية' => ['سيدي جابر', 'المنتزه', 'العجمي', 'محرم بك', 'برج العرب'],
            'البحيرة' => ['دمنهور', 'كفر الدوار', 'رشيد', 'إدكو'],
            'كفر الشيخ' => ['دسوق', 'بيلا', 'الحامول', 'بلطيم'],
            'الغربية' => ['طنطا', 'المحلة الكبرى', 'زفتى', 'كفر الزيات'],
            'المنوفية' => ['شبين الكوم', 'السادات', 'منوف', 'أشمون'],
            'الدقهلية' => ['المنصورة', 'ميت غمر', 'طلخا', 'بلقاس'],
            'دمياط' => ['دمياط', 'رأس البر', 'فارسكور', 'كفر سعد'],
            'الشرقية' => ['الزقازيق', 'العاشر من رمضان', 'بلبيس', 'منيا القمح'],
            'الفيوم' => ['الفيوم', 'سنورس', 'إطسا'],
            'بني سويف' => ['بني سويف', 'الواسطى', 'الفشن'],
            'المنيا' => ['المنيا', 'ملوي', 'بني مزار', 'سمالوط'],
            'أسيوط' => ['أسيوط', 'ديروط', 'منفلوط'],
            'سوهاج' => ['سوهاج', 'أخميم', 'جرجا', 'طهطا'],
            'قنا' => ['قنا', 'نجع حمادي', 'دشنا'],
            'الأقصر' => ['الأقصر', 'إسنا', 'أرمنت'],
            'أسوان' => ['أسوان', 'كوم أمبو', 'إدفو'],
            'الإسماعيلية' => ['الإسماعيلية', 'فايد', 'القنطرة'],
            'السويس' => ['السويس', 'الجناين', 'عتاقة'],
            'بورسعيد' => ['بورسعيد', 'بورفؤاد'],
            'شمال سيناء' => ['العريش', 'الشيخ زويد', 'رفح'],
            'جنوب سيناء' => ['شرم الشيخ', 'دهب', 'الطور'],
            'مطروح' => ['مرسى مطروح', 'العلمين', 'سيدي براني'],
            'البحر الأحمر' => ['الغردقة', 'سفاجا', 'القصير', 'مرسى علم'],
            'الوادي الجديد' => ['الخارجة', 'الداخلة', 'الفرافرة'],
        ];
    }
}
