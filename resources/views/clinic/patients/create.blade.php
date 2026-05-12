@extends('layouts.app')

@section('title', __('clinic.patients.add_new'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.patients.add_new') }}</h1>
        <p class="page-subtitle">{{ __('clinic.patients.add_subtitle') }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('clinic.patients.index') }}" class="btn btn-secondary">
            <i class="ph-bold ph-arrow-left"></i>
            <span>{{ __('clinic.common.back') }}</span>
        </a>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-body">
        <form action="{{ route('clinic.patients.store') }}" method="POST">
            @csrf

            <h3 class="fw-700 mb-16" style="color: var(--clr-primary-600); border-bottom: 1px solid var(--clr-n-200); padding-bottom: 10px;">
                <i class="ph-bold ph-user"></i> {{ __('clinic.patients.basic_info') }}
            </h3>
            <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="full_name">{{ __('clinic.patients.full_name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name') }}" required>
                    @error('full_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="english_name">{{ __('clinic.patients.english_name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="english_name" id="english_name" class="form-control" value="{{ old('english_name') }}" dir="ltr" required>
                    @error('english_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">{{ __('clinic.patients.phone') }} <span class="text-danger">*</span></label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" dir="ltr" required>
                    @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="ssn">{{ __('clinic.patients.ssn') }}</label>
                    <input type="text" name="ssn" id="ssn" class="form-control" value="{{ old('ssn') }}" dir="ltr">
                    @error('ssn') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="birth_date">{{ __('clinic.patients.dob') }}</label>
                    <input type="date" name="birth_date" id="birth_date" class="form-control" value="{{ old('birth_date') }}">
                    @error('birth_date') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="age">{{ __('clinic.patients.age') }}</label>
                    <input type="number" name="age" id="age" class="form-control" value="{{ old('age') }}" min="0" placeholder="{{ __('clinic.patients.years') }}">
                    @error('age') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('clinic.patients.gender') }} <span class="text-danger">*</span></label>
                    <div class="d-flex gap-16 mt-8">
                        <label class="d-flex align-center gap-8 cursor-pointer">
                            <input type="radio" name="gender" value="male" {{ old('gender') == 'male' ? 'checked' : '' }} required>
                            <span>{{ __('clinic.patients.male') }}</span>
                        </label>
                        <label class="d-flex align-center gap-8 cursor-pointer">
                            <input type="radio" name="gender" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
                            <span>{{ __('clinic.patients.female') }}</span>
                        </label>
                    </div>
                    @error('gender') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="nationality">{{ __('clinic.patients.nationality') }}</label>
                    <input type="text" name="nationality" id="nationality" class="form-control" value="{{ old('nationality', 'Egypt') }}">
                    @error('nationality') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">{{ __('clinic.patients.email') }}</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" dir="ltr">
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="form-group mt-16 mb-24">
                <label class="form-label" for="address">{{ __('clinic.patients.address') }}</label>
                <textarea name="address" id="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>

            <h3 class="fw-700 mb-16" style="color: var(--clr-primary-600); border-bottom: 1px solid var(--clr-n-200); padding-bottom: 10px;">
                <i class="ph-bold ph-shield-check"></i> {{ __('clinic.patients.insurance_info') }}
            </h3>
            <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label class="form-label" for="company">{{ __('clinic.patients.insurance_company') }}</label>
                    <input type="text" name="company" id="company" class="form-control" value="{{ old('company') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="policy_name">{{ __('clinic.patients.policy_name') }}</label>
                    <input type="text" name="policy_name" id="policy_name" class="form-control" value="{{ old('policy_name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="class">{{ __('clinic.patients.class') }}</label>
                    <input type="text" name="class" id="class" class="form-control" value="{{ old('class') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="card_no">{{ __('clinic.patients.card_no') }}</label>
                    <input type="text" name="card_no" id="card_no" class="form-control" value="{{ old('card_no') }}">
                </div>
            </div>

            <div class="d-flex justify-end gap-12 mt-32" style="border-top: 1px solid var(--clr-n-200); padding-top: 16px;">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="ph-bold ph-floppy-disk"></i>
                    <span>{{ __('clinic.patients.save_btn') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
