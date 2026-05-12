<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            ['name' => 'أسنان', 'icon' => 'ph-bold ph-tooth'],
            ['name' => 'جلدية', 'icon' => 'ph-bold ph-drop'],
            ['name' => 'نساء وتوليد', 'icon' => 'ph-bold ph-gender-female'],
            ['name' => 'باطنة', 'icon' => 'ph-bold ph-stethoscope'],
            ['name' => 'أطفال', 'icon' => 'ph-bold ph-baby'],
            ['name' => 'عيون', 'icon' => 'ph-bold ph-eye'],
            ['name' => 'عظام', 'icon' => 'ph-bold ph-bone'],
            ['name' => 'أنف وأذن وحنجرة', 'icon' => 'ph-bold ph-ear'],
            ['name' => 'جراحة عامة', 'icon' => 'ph-bold ph-scissors'],
            ['name' => 'قلب وأوعية دموية', 'icon' => 'ph-bold ph-heartbeat'],
            ['name' => 'مخ وأعصاب', 'icon' => 'ph-bold ph-brain'],
            ['name' => 'مسالك بولية', 'icon' => 'ph-bold ph-drop'],
            ['name' => 'نفسية', 'icon' => 'ph-bold ph-brain'],
            ['name' => 'تخسيس وتغذية', 'icon' => 'ph-bold ph-bowl-food'],
            ['name' => 'علاج طبيعي', 'icon' => 'ph-bold ph-accessibility'],
            ['name' => 'أشعة', 'icon' => 'ph-bold ph-radioactive'],
            ['name' => 'تحاليل', 'icon' => 'ph-bold ph-test-tube'],
            ['name' => 'كبد', 'icon' => 'ph-bold ph-activity'],
            ['name' => 'كلى', 'icon' => 'ph-bold ph-activity'],
            ['name' => 'أورام', 'icon' => 'ph-bold ph-virus'],
            ['name' => 'صدر وجهاز تنفسي', 'icon' => 'ph-bold ph-wind'],
            ['name' => 'غدد صماء وسكر', 'icon' => 'ph-bold ph-thermometer'],
            ['name' => 'مسالك بولية وتناسلية', 'icon' => 'ph-bold ph-drop'],
            ['name' => 'جراحة سمنة ومناظير', 'icon' => 'ph-bold ph-gauge'],
        ];

        foreach ($specialties as $specialty) {
            \App\Models\saas\Specialty::firstOrCreate(['name' => $specialty['name']], $specialty);
        }
    }
}
