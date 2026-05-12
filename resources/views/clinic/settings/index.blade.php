@extends('layouts.app')

@section('title', __('clinic.settings.title'))

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ __('clinic.settings.title') }}</h1>
        <p class="page-subtitle">{{ __('clinic.settings.subtitle') }}</p>
    </div>
</div>

<div class="dashboard-grid-50">

    {{-- Language Preference Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ph-bold ph-globe" style="margin-inline-end: 8px;"></i>
                {{ __('clinic.settings.preferences') }} — {{ __('clinic.settings.language') }}
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted text-sm mb-16">{{ __('clinic.settings.language_subtitle') }}</p>
            <form action="{{ route('clinic.settings.locale') }}" method="POST">
                @csrf
                <div class="d-flex gap-12 mb-24" style="align-items: stretch; flex-wrap: wrap;">
                    <label class="d-flex align-center gap-8 cursor-pointer p-16 card mb-0" style="flex: 1; min-width: 140px; margin: 0; border: 2px solid {{ app()->getLocale() === 'en' ? 'var(--clr-primary-400)' : 'var(--clr-n-200)' }}; background: {{ app()->getLocale() === 'en' ? 'var(--clr-primary-50)' : 'transparent' }};">
                        <input type="radio" name="locale" value="en" {{ app()->getLocale() === 'en' ? 'checked' : '' }} onchange="this.form.submit()">
                        <div>
                            <span class="fw-700 d-block" style="line-height: 1.2;">🇬🇧 {{ __('clinic.settings.english') }}</span>
                            <span class="text-sm text-muted">English (LTR)</span>
                        </div>
                    </label>
                    <label class="d-flex align-center gap-8 cursor-pointer p-16 card mb-0" style="flex: 1; min-width: 140px; margin: 0; border: 2px solid {{ app()->getLocale() === 'ar' ? 'var(--clr-primary-400)' : 'var(--clr-n-200)' }}; background: {{ app()->getLocale() === 'ar' ? 'var(--clr-primary-50)' : 'transparent' }};">
                        <input type="radio" name="locale" value="ar" {{ app()->getLocale() === 'ar' ? 'checked' : '' }} onchange="this.form.submit()">
                        <div>
                            <span class="fw-700 d-block" style="line-height: 1.2;">🇪🇬 {{ __('clinic.settings.arabic') }}</span>
                            <span class="text-sm text-muted">العربية (RTL)</span>
                        </div>
                    </label>
                </div>
            </form>
        </div>
    </div>

    {{-- Account Info Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ph-bold ph-user" style="margin-inline-end: 8px;"></i>
                {{ __('clinic.settings.profile') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="form-group mb-16">
                <label class="form-label">{{ __('clinic.settings.name') }}</label>
                <div class="form-control" style="background: var(--clr-n-50); cursor: not-allowed;">{{ $user->name }}</div>
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('clinic.settings.email') }}</label>
                <div class="form-control" dir="ltr" style="background: var(--clr-n-50); cursor: not-allowed;">{{ $user->email }}</div>
            </div>
        </div>
    </div>

    {{-- Doctor Prices Card --}}
    @if(auth()->user()->doctor)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ph-bold ph-currency-circle-dollar" style="margin-inline-end: 8px;"></i>
                {{ __('clinic.settings.prices_title') }}
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('clinic.settings.prices') }}" method="POST">
                @csrf
                <div class="form-group mb-16">
                    <label class="form-label">{{ __('clinic.settings.price_consultation') }}</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', auth()->user()->doctor->price) }}">
                </div>
                <div class="form-group mb-16">
                    <label class="form-label">{{ __('clinic.settings.price_followup') }}</label>
                    <input type="number" step="0.01" name="followup_price" class="form-control" value="{{ old('followup_price', auth()->user()->doctor->followup_price) }}">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="ph-bold ph-floppy-disk"></i>
                    {{ __('clinic.settings.save_prices') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Doctor Services Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ph-bold ph-list-plus" style="margin-inline-end: 8px;"></i>
                {{ __('clinic.settings.services_title') }}
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('clinic.settings.services.store') }}" method="POST" class="mb-24">
                @csrf
                <div class="d-flex gap-12 align-end">
                    <div class="form-group mb-0" style="flex: 2;">
                        <label class="form-label">{{ __('clinic.settings.service_name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group mb-0" style="flex: 1;">
                        <label class="form-label">{{ __('clinic.settings.price') }}</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 40px; margin-bottom: 2px;">
                        <i class="ph-bold ph-plus"></i>
                    </button>
                </div>
            </form>

            @if(auth()->user()->doctor->services->count() > 0)
                <div class="table-responsive">
                    <table class="table" style="font-size: 13px;">
                        <thead>
                            <tr>
                                <th>{{ __('clinic.settings.service') }}</th>
                                <th>{{ __('clinic.settings.price') }}</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(auth()->user()->doctor->services as $service)
                            <tr>
                                <td>{{ $service->name }}</td>
                                <td>{{ $service->price }}</td>
                                <td>
                                    <form action="{{ route('clinic.settings.services.destroy', $service) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost text-danger" onclick="return confirm('{{ __('clinic.settings.confirm_delete') }}')">
                                            <i class="ph-bold ph-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Prescription Branding Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ph-bold ph-paint-bucket" style="margin-inline-end: 8px;"></i>
                {{ __('clinic.settings.branding_title') }}
            </h3>
        </div>
        <div class="card-body">
            {{-- Live preview strip --}}
            <div id="branding-preview" style="height:8px;border-radius:6px;margin-bottom:20px;background:linear-gradient(90deg,{{ $clinic->primaryColor() }},{{ $clinic->primaryColor() }});" ></div>

            <form action="{{ route('clinic.settings.branding') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="d-flex gap-16 flex-wrap">
                    <div class="form-group" style="flex:1;min-width:180px;">
                        <label class="form-label">{{ __('clinic.settings.rx_color') }}</label>
                        <div class="d-flex gap-8 align-center">
                            <input type="color" name="primary_color" id="colorPicker" class="form-control" style="width:60px;height:40px;padding:2px 4px;cursor:pointer;" value="{{ $clinic->primaryColor() }}">
                            <span id="colorHex" class="text-sm text-muted fw-600">{{ $clinic->primaryColor() }}</span>
                        </div>
                    </div>
                    <div class="form-group" style="flex:2;min-width:200px;">
                        <label class="form-label">{{ __('clinic.settings.clinic_phone') }}</label>
                        <input type="text" name="clinic_phone" class="form-control" dir="ltr" value="{{ $clinic->phone ?? '01007056015' }}" placeholder="01007056015">
                    </div>
                    <div class="form-group" style="flex:3;min-width:250px;">
                        <label class="form-label">{{ __('clinic.settings.clinic_address') }}</label>
                        <input type="text" name="clinic_address" class="form-control" value="{{ $clinic->address ?? '' }}" placeholder="{{ __('clinic.settings.clinic_address_placeholder') }}">
                    </div>
                </div>
                <div class="form-group mt-8">
                    <label class="form-label">{{ __('clinic.settings.clinic_logo') }}</label>
                    @if($clinic->logoUrl())
                    <div class="mb-8 d-flex align-center gap-12">
                        <img src="{{ $clinic->logoUrl() }}" style="height:56px;border-radius:10px;border:1px solid var(--clr-n-200);padding:4px;background:#fff;" alt="Logo">
                        <span class="text-sm text-muted">{{ __('clinic.settings.logo_hint') }}</span>
                    </div>
                    @endif
                    <input type="file" name="clinic_logo" class="form-control" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                    @if(!$clinic->logoUrl())
                    <div class="text-sm text-muted mt-4">{{ __('clinic.settings.logo_hint') }}</div>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary mt-8">
                    <i class="ph-bold ph-floppy-disk"></i>
                    {{ __('clinic.settings.save_branding') }}
                </button>
            </form>
        </div>
    </div>
    <script>
    (function() {
        const picker  = document.getElementById('colorPicker');
        const hex     = document.getElementById('colorHex');
        const preview = document.getElementById('branding-preview');
        if (!picker) return;
        picker.addEventListener('input', function () {
            hex.textContent = this.value;
            preview.style.background = 'linear-gradient(90deg,' + this.value + ',' + this.value + 'cc)';
        });
    })();
    </script>

    {{-- Change Email Card --}}
    <div class="card" style="display: flex; flex-direction: column;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ph-bold ph-envelope" style="margin-inline-end: 8px;"></i>
                {{ __('clinic.settings.change_email_title') }}
            </h3>
        </div>
        <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
            <form id="emailChangeForm" onsubmit="requestOtp(event, 'email_change')" style="flex: 1; display: flex; flex-direction: column;">
                <div class="form-group mb-16">
                    <label class="form-label" for="new_email">{{ __('clinic.settings.new_email') }}</label>
                    <input type="email" id="new_email" name="new_email" class="form-control" required dir="ltr" placeholder="new@email.com">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: auto; padding: 12px; height: 48px;">
                    <i class="ph-bold ph-paper-plane-tilt"></i>
                    {{ __('clinic.settings.request_otp') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Change Password Card --}}
    <div class="card" style="display: flex; flex-direction: column;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ph-bold ph-lock" style="margin-inline-end: 8px;"></i>
                {{ __('clinic.settings.security') }}
            </h3>
        </div>
        <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
            <form id="passwordChangeForm" onsubmit="requestOtp(event, 'password_change')" style="flex: 1; display: flex; flex-direction: column;">
                <div class="form-group mb-16">
                    <label class="form-label" for="new_password">{{ __('clinic.settings.new_password') }}</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8" dir="ltr">
                </div>
                <div class="form-group mb-16">
                    <label class="form-label" for="new_password_confirmation">{{ __('clinic.settings.confirm_password') }}</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required minlength="8" dir="ltr">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: auto; padding: 12px; height: 48px;">
                    <i class="ph-bold ph-paper-plane-tilt"></i>
                    {{ __('clinic.settings.request_otp') }}
                </button>
            </form>
        </div>
    </div>
</div>

{{-- OTP Modal --}}
<div id="otpModal" class="modal">
    <div class="modal-content" style="max-width: 420px; text-align: center;">
        <div class="modal-header d-flex justify-between align-center mb-24">
            <h2 class="modal-title">{{ __('clinic.settings.verify_otp') }}</h2>
            <button class="modal-close" onclick="closeOtpModal()"><i class="ph-bold ph-x"></i></button>
        </div>
        <div class="modal-body">
            <i class="ph-fill ph-envelope-simple-open" style="font-size: 64px; color: var(--clr-primary-400); margin-bottom: 16px;"></i>
            <p class="mb-24 text-muted">{{ __('clinic.settings.otp_sent') }}</p>
            <form id="verifyOtpForm" onsubmit="verifyOtp(event)">
                <input type="hidden" id="otp_type" name="type">
                <div class="form-group">
                    <input type="text" id="otp_code" class="form-control text-center" placeholder="------" maxlength="6" required dir="ltr" style="letter-spacing: 10px; font-size: 22px; font-weight: 700;">
                </div>
                <button type="submit" class="btn btn-primary btn-lg mt-16" style="width: 100%;">{{ __('clinic.settings.verify_otp') }}</button>
            </form>
            <div id="otpError" class="text-danger mt-12 text-sm" style="display: none;"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const _i18n = {
        passwordsNoMatch: '{{ __('clinic.settings.passwords_no_match') }}',
        errorOccurred: '{{ __('clinic.settings.error_occurred') }}',
        serverError: '{{ __('clinic.settings.server_error') }}',
        connectionError: '{{ __('clinic.settings.connection_error') }}',
    };

    let currentActionType = '';

    function requestOtp(e, type) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        formData.append('type', type);
        formData.append('_token', '{{ csrf_token() }}');

        if (type === 'password_change') {
            if (formData.get('new_password') !== formData.get('new_password_confirmation')) {
                alert(_i18n.passwordsNoMatch);
                return;
            }
        }

        fetch('{{ route('clinic.settings.request-otp') }}', {
            method: 'POST', body: formData, headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                currentActionType = type;
                document.getElementById('otp_type').value = type;
                document.getElementById('otpModal').classList.add('active');
            } else {
                alert(data.message || _i18n.errorOccurred);
            }
        })
        .catch(() => alert(_i18n.serverError));
    }

    function verifyOtp(e) {
        e.preventDefault();
        const code = document.getElementById('otp_code').value;
        const errorDiv = document.getElementById('otpError');
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('type', currentActionType);
        formData.append('code', code);

        fetch('{{ route('clinic.settings.verify-otp') }}', {
            method: 'POST', body: formData, headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') { alert(data.message); location.reload(); }
            else { errorDiv.textContent = data.message; errorDiv.style.display = 'block'; }
        })
        .catch(() => { errorDiv.textContent = _i18n.connectionError; errorDiv.style.display = 'block'; });
    }

    function closeOtpModal() {
        document.getElementById('otpModal').classList.remove('active');
        document.getElementById('otpError').style.display = 'none';
        document.getElementById('otp_code').value = '';
    }
</script>
@endpush
