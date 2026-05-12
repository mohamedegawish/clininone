<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class SpecialtyService
{
    private static $map = [
        'أسنان' => 'Dentistry',
        'جلدية' => 'Dermatology',
        'نساء وتوليد' => 'Obstetrics and Gynecology',
        'باطنة' => 'Internal Medicine',
        'أطفال' => 'Pediatrics',
        'عيون' => 'Ophthalmology',
        'عظام' => 'Orthopedics',
        'أنف وأذن وحنجرة' => 'ENT',
        'جراحة عامة' => 'General Surgery',
        'قلب وأوعية دموية' => 'Cardiology',
        'مخ وأعصاب' => 'Neurology',
        'مسالك بولية' => 'Urology',
        'نفسية' => 'Psychiatry',
        'تخسيس وتغذية' => 'Nutrition and Dietetics',
        'علاج طبيعي' => 'Physical Therapy',
        'أشعة' => 'Radiology',
        'تحاليل' => 'Laboratory',
        'كبد' => 'Hepatology',
        'كلى' => 'Nephrology',
        'أورام' => 'Oncology',
        'صدر وجهاز تنفسي' => 'Pulmonology',
        'غدد صماء وسكر' => 'Endocrinology',
        'مسالك بولية وتناسلية' => 'Urology and Venereology',
        'جراحة سمنة ومناظير' => 'Bariatric and Laparoscopic Surgery',
    ];

    public static function translate(string $name): string
    {
        $locale = App::getLocale();
        if ($locale === 'ar') {
            return $name;
        }

        return self::$map[$name] ?? $name;
    }

    public static function getAllTranslated(iterable $specialties): array
    {
        $result = [];
        foreach ($specialties as $s) {
            $result[] = self::translate($s);
        }
        return $result;
    }

    public static function getReverseMap(): array
    {
        return array_flip(self::$map);
    }
}
