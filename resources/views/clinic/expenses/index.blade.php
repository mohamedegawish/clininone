@extends('layouts.app')

@section('title', __('clinic.expenses.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.expenses.title') }}</h1>
        <p class="page-subtitle">{{ __('clinic.expenses.subtitle') }}</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary" onclick="document.getElementById('addExpenseModal').classList.add('active')">
            <i class="ph-bold ph-plus"></i>
            <span>{{ __('clinic.expenses.add_new') }}</span>
        </button>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('clinic.expenses.date') }}</th>
                    <th>{{ __('clinic.expenses.category') }}</th>
                    <th>{{ __('clinic.expenses.amount') }}</th>
                    <th>{{ __('clinic.expenses.description') }}</th>
                    <th>{{ __('clinic.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td dir="ltr">{{ $expense->date->format('Y-m-d') }}</td>
                    <td><span class="badge badge-primary">{{ $expense->category }}</span></td>
                    <td class="fw-600 text-danger" dir="ltr">{{ number_format($expense->amount, 2) }} EGP</td>
                    <td class="text-muted">{{ $expense->description ?? '-' }}</td>
                    <td>
                        <div class="action-menu">
                            <form action="{{ route('clinic.expenses.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('clinic.common.confirm_delete') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action delete" title="{{ __('clinic.common.delete') }}">
                                    <i class="ph-bold ph-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-24">{{ __('clinic.common.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
    <div class="card-footer">{{ $expenses->links() }}</div>
    @endif
</div>

<!-- Add Expense Modal -->
<div id="addExpenseModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2 class="modal-title">{{ __('clinic.expenses.add_new') }}</h2>
            <button class="modal-close" onclick="document.getElementById('addExpenseModal').classList.remove('active')">
                <i class="ph-bold ph-x"></i>
            </button>
        </div>
        <form action="{{ route('clinic.expenses.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="date">{{ __('clinic.expenses.date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="category">{{ __('clinic.expenses.category') }} <span class="text-danger">*</span></label>
                    <select name="category" id="category" class="form-control" required onchange="toggleCustomCategory()">
                        <option value="Rent">{{ __('clinic.expenses.rent') }}</option>
                        <option value="Salaries">{{ __('clinic.expenses.salaries') }}</option>
                        <option value="Medical Supplies">{{ __('clinic.expenses.supplies') }}</option>
                        <option value="Maintenance">{{ __('clinic.expenses.maintenance') }}</option>
                        <option value="Utilities">{{ __('clinic.expenses.utilities') }}</option>
                        <option value="Marketing">{{ __('clinic.expenses.marketing') }}</option>
                        <option value="other">{{ __('clinic.expenses.other') }}</option>
                    </select>
                </div>
                <div class="form-group" id="customCategoryGroup" style="display: none;">
                    <label class="form-label" for="custom_category">{{ __('clinic.expenses.custom_category') }}</label>
                    <input type="text" name="custom_category" id="custom_category" class="form-control" placeholder="e.g. Lab fees, Equipment...">
                </div>
                <div class="form-group">
                    <label class="form-label" for="amount">{{ __('clinic.expenses.amount') }} (EGP) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="description">{{ __('clinic.expenses.description') }}</label>
                    <textarea name="description" id="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer d-flex justify-end gap-12">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('addExpenseModal').classList.remove('active')">{{ __('clinic.common.cancel') }}</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ph-bold ph-floppy-disk"></i>
                    {{ __('clinic.common.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleCustomCategory() {
        const v = document.getElementById('category').value;
        const g = document.getElementById('customCategoryGroup');
        const i = document.getElementById('custom_category');
        g.style.display = v === 'other' ? 'block' : 'none';
        i.required = v === 'other';
    }
</script>
@endsection
