<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Models\core\Donor;
use App\Models\core\BloodRequest;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BloodBankController extends Controller
{
    use HttpResponses;

    public function donors(Request $request): JsonResponse
    {
        $query = Donor::query();

        if ($request->has('blood_type') && $request->blood_type !== 'all') {
            $query->where('blood_type', $request->blood_type);
        }

        if ($request->has('governorate') && $request->governorate !== 'all') {
            $query->where('governorate', $request->governorate);
        }

        if ($request->has('sort') && $request->sort === 'name') {
            $query->orderBy('name', 'asc');
        } else {
            $query->latest();
        }

        // Hide phone numbers for public listing
        $donors = $query->select(['id', 'name', 'blood_type', 'governorate', 'city', 'status', 'created_at'])->get();

        return $this->success($donors);
    }

    public function storeDonor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'blood_type' => 'required|string',
            'governorate' => 'required|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'last_donation_date' => 'nullable|date',
        ]);

        $donor = Donor::create($validated);

        return $this->success($donor, 'تم تسجيل المتبرع بنجاح');
    }

    public function requests(Request $request): JsonResponse
    {
        $query = BloodRequest::query();

        if ($request->has('blood_type') && $request->blood_type !== 'all') {
            $query->where('blood_type', $request->blood_type);
        }

        if ($request->has('governorate') && $request->governorate !== 'all') {
            $query->where('governorate', $request->governorate);
        }

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Show only 'new' and 'contacting' cases to public? 
        // User said Admin coordinates, so maybe show all except cancelled/completed
        $requests = $query->whereIn('status', ['new', 'contacting'])
            ->select(['id', 'name', 'blood_type', 'governorate', 'city', 'hospital', 'type', 'urgency_level', 'status', 'created_at'])
            ->latest()
            ->get();

        return $this->success($requests);
    }

    public function storeRequest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'blood_type' => 'required|string',
            'quantity' => 'nullable|string',
            'governorate' => 'required|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'hospital' => 'nullable|string|max:255',
            'type' => 'required|string|in:normal,urgent',
            'urgency_level' => 'nullable|string|in:high,medium,low',
        ]);

        $validated['status'] = 'new';
        $bloodRequest = BloodRequest::create($validated);

        // Auto-add hospital if new
        if (!empty($validated['hospital'])) {
            \App\Models\core\Hospital::firstOrCreate([
                'name' => $validated['hospital'],
                'governorate' => $validated['governorate']
            ]);
        }

        // Smart Matching
        $matchedDonorsCount = Donor::where('blood_type', $validated['blood_type'])
            ->where('governorate', $validated['governorate'])
            ->where('status', 'active')
            ->count();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تقديم طلب الدم بنجاح',
            'data' => $bloodRequest->only(['id', 'name', 'blood_type']),
            'matched_donors_count' => $matchedDonorsCount
        ]);
    }

    public function getHospitals(Request $request)
    {
        $gov = $request->governorate;
        $hospitals = \App\Models\core\Hospital::when($gov && $gov !== 'all', function($q) use ($gov) {
            return $q->where('governorate', $gov);
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => $hospitals
        ]);
    }
}
