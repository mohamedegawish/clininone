<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Clinic\Concerns\ResolvesClinic;
use App\Models\core\Expense;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    use ResolvesClinic;

    public function index(): View
    {
        $clinicId = $this->resolveClinic()->id;
        
        $expenses = Expense::where('clinic_id', $clinicId)
            ->latest('date')
            ->paginate(15);
            
        return view('clinic.expenses.index', compact('expenses'));
    }

    public function store(Request $request)
    {
        $clinic   = $this->resolveClinic();
        $clinicId = $clinic->id;
        $doctorId = auth()->user()?->doctor?->id;

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // If 'other', a 'custom_category' might be passed
        if ($validated['category'] === 'other' && $request->filled('custom_category')) {
            $validated['category'] = $request->input('custom_category');
        }

        Expense::create([
            'clinic_id' => $clinicId,
            'doctor_id' => $doctorId,
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('clinic.expenses.index')->with('success', 'Expense added successfully.');
    }

    public function destroy(Expense $expense)
    {
        $clinicId = $this->resolveClinic()->id;
        abort_if($expense->clinic_id !== $clinicId, 403);

        $expense->delete();

        return redirect()->route('clinic.expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
