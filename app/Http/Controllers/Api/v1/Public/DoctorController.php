<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Models\core\Doctor;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Services\LocationService;

class DoctorController extends Controller
{
    use HttpResponses;

    /**
     * Get a list of public visible doctors with optional specialty filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Doctor::where('status', 'active')
            ->whereHas('clinics');

        $locale = app()->getLocale();

        if ($request->has('specialty') && !empty($request->input('specialty'))) {
            $specialty = $request->input('specialty');
            // If English, find the Arabic equivalent for DB query
            if ($locale === 'en') {
                $reverseMap = \App\Services\SpecialtyService::getReverseMap();
                $specialty = $reverseMap[$specialty] ?? $specialty;
            }
            $query->where('specialty', $specialty);
        }

        if ($request->has('city') && !empty($request->input('city'))) {
            $city = $request->input('city');
            // Locations are also translated in LocationService, but DB stores Arabic usually
            // For now, assume DB stores Arabic names
            $query->where('city', $city);
        }

        if ($request->has('governorate') && !empty($request->input('governorate'))) {
            $query->where('governorate', $request->input('governorate'));
        }

        $doctors = $query->withAvg('ratings as rating', 'rating')
            ->withCount('ratings as reviewCount')
            ->with('clinics')
            ->paginate($request->input('per_page', 100));

        $items = collect($doctors->items())->map(function($doctor) use ($locale) {
            $name = $doctor->name;
            if ($locale === 'ar' && !empty($doctor->arabic_name)) {
                $name = $doctor->arabic_name;
            }
            
            return [
                'id' => $doctor->id,
                'name' => $name,
                'specialty' => \App\Services\SpecialtyService::translate($doctor->specialty),
                'experience' => $doctor->experience_years ?? 5,
                'rating' => (float) ($doctor->rating ?? 4.8),
                'reviewCount' => $doctor->reviewCount ?? 24,
                'price' => $doctor->price,
                'photo' => $doctor->photo_path ? asset('storage/' . $doctor->photo_path) : null,
                'clinicId' => $doctor->clinics->first()?->id,
                'address' => $doctor->city ?: ($doctor->governorate ?: 'القاهرة'),
                'city' => $doctor->city,
                'governorate' => $doctor->governorate,
            ];
        });

        return $this->success($items);
    }

    /**
     * Get a single doctor's public profile.
     */
    public function show(Doctor $doctor): JsonResponse
    {
        $doctor->load(['clinics', 'ratings.patient', 'schedules', 'appointments' => function ($q) {
            $q->whereDate('appointment_date', today())->where('status', '!=', 'cancelled');
        }]);
        $doctor->loadAvg('ratings as rating', 'rating');
        $doctor->loadCount('ratings as reviewCount');

        $locale = app()->getLocale();
        $name = $doctor->name;
        if ($locale === 'ar' && !empty($doctor->arabic_name)) {
            $name = $doctor->arabic_name;
        }

        $daysMap = [
            0 => 'الأحد',
            1 => 'الاثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];

        $workDays = [];
        $minStart = '09:00:00';
        $maxEnd = '17:00:00';

        if ($doctor->schedules->isNotEmpty()) {
            $workDays = $doctor->schedules->pluck('day_of_week')->map(fn($d) => $daysMap[$d] ?? '')->filter()->values()->toArray();
            $minStart = $doctor->schedules->min('start_time');
            $maxEnd = $doctor->schedules->max('end_time');
        } else {
            $workDays = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس'];
        }

        $workHours = [
            'start' => str_replace(['AM', 'PM'], ['ص', 'م'], date('h:i A', strtotime($minStart))),
            'end' => str_replace(['AM', 'PM'], ['ص', 'م'], date('h:i A', strtotime($maxEnd)))
        ];

        $availableSlots = [];
        $todayDayOfWeek = now()->dayOfWeek;
        $todaySchedule = $doctor->schedules->where('day_of_week', $todayDayOfWeek)->first();

        if ($todaySchedule) {
            $startTime = \Carbon\Carbon::parse($todaySchedule->start_time);
            $endTime = \Carbon\Carbon::parse($todaySchedule->end_time);
            $slotDuration = $todaySchedule->slot_duration ?? 30;
            
            $bookedTimes = $doctor->appointments->pluck('start_time')->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i:s'))->toArray();

            while ($startTime->copy()->addMinutes($slotDuration)->lte($endTime)) {
                if (!in_array($startTime->format('H:i:s'), $bookedTimes)) {
                    $availableSlots[] = str_replace(['AM', 'PM'], ['ص', 'م'], $startTime->format('h:i A'));
                }
                $startTime->addMinutes($slotDuration);
            }
        } else if ($doctor->schedules->isEmpty()) {
            $availableSlots = ['10:00 ص', '10:30 ص', '11:00 ص', '12:00 م', '01:00 م'];
        }

        $data = [
            'id' => $doctor->id,
            'name' => $name,
            'specialty' => \App\Services\SpecialtyService::translate($doctor->specialty),
            'experience' => $doctor->experience_years ?? 5,
            'bio' => $doctor->bio,
            'rating' => (float) ($doctor->rating ?? 4.8),
            'reviewCount' => $doctor->reviewCount ?? 24,
            'price' => $doctor->price,
            'photo' => $doctor->photo_path ? asset('storage/' . $doctor->photo_path) : null,
            'clinicId' => $doctor->clinics->first()?->id,
            'address' => $doctor->city ?? 'القاهرة',
            'email' => $doctor->email,
            'phone' => $doctor->phone,
            'workHours' => $workHours,
            'workDays' => $workDays,
            'availableSlots' => $availableSlots,
        ];

        return $this->success($data);
    }

    /**
     * Get a distinct list of specialties available.
     */
    public function specialties(): JsonResponse
    {
        $locale = app()->getLocale();
        $arabicSpecialties = array_values(\App\Services\SpecialtyService::getReverseMap());
        
        $specialties = array_map(function($spec) use ($locale) {
            return $locale === 'en' ? (\App\Services\SpecialtyService::translate($spec)) : $spec;
        }, $arabicSpecialties);

        return $this->success($specialties);
    }

    /**
     * Get distinct list of locations (cities and governorates).
     */
    public function locations(): JsonResponse
    {
        $all = LocationService::getAll();
        $governorates = array_keys($all);
        $cities = [];
        foreach ($all as $c) {
            $cities = array_merge($cities, $c);
        }
        $cities = array_unique($cities);

        return $this->success([
            'cities' => array_values($cities),
            'governorates' => $governorates,
            'map' => $all,
        ]);
    }
}
