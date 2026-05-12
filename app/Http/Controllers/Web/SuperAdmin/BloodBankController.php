<?php
namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\core\BloodRequest;
use App\Models\core\Donor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BloodBankController extends Controller
{
    public function index(): View
    {
        $requests = BloodRequest::latest()->paginate(10);
        $donorsCount = Donor::count();
        $pendingRequestsCount = BloodRequest::where('status', 'new')->count();
        
        return view('admin.blood-bank.index', compact('requests', 'donorsCount', 'pendingRequestsCount'));
    }

    public function showRequest(BloodRequest $request): View
    {
        // Smart Matching: find donors with same blood type and governorate
        $matchedDonors = Donor::where('blood_type', $request->blood_type)
            ->where('governorate', $request->governorate)
            ->where('status', 'active')
            ->get();

        return view('admin.blood-bank.show-request', compact('request', 'matchedDonors'));
    }

    public function updateRequestStatus(BloodRequest $request, Request $req)
    {
        $request->update([
            'status' => $req->status
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    public function donors(Request $req): View
    {
        $query = Donor::query();
        
        if ($req->blood_type) {
            $query->where('blood_type', $req->blood_type);
        }
        
        if ($req->governorate) {
            $query->where('governorate', $req->governorate);
        }

        $donors = $query->latest()->paginate(20);
        
        return view('admin.blood-bank.donors', compact('donors'));
    }

    public function toggleDonorStatus(Donor $donor)
    {
        $donor->update([
            'status' => $donor->status === 'active' ? 'inactive' : 'active'
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة المتبرع');
    }
}
