<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'en' ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('auth.login.title') }} | {{ App\Models\Setting::get('system_name', 'ClinicOne') }}</title>
    <link rel="stylesheet" href="{{ asset('css/saas.css') }}">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--clr-primary-900), var(--clr-primary-800));
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--surface);
            border-radius: var(--r-xl);
            box-shadow: var(--sh-xl);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 5px;
            background: linear-gradient(90deg, var(--clr-accent-400), var(--clr-primary-400));
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, var(--clr-accent-400), var(--clr-primary-400));
            border-radius: var(--r-lg);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; color: #fff;
            margin: 0 auto 16px;
            box-shadow: 0 8px 20px rgba(30,191,171,0.3);
        }
        .login-title {
            font-size: 24px; font-weight: 700; color: var(--clr-n-900);
            margin-bottom: 8px;
        }
        .login-subtitle { font-size: 14px; color: var(--clr-n-400); }
        
        .lang-switch-login {
            position: absolute;
            top: 20px;
            inset-inline-end: 20px;
        }
        @media (max-width: 480px) {
            .login-card { padding: 30px 20px; }
        }
    </style>
</head>
<body>
    <div class="lang-switch-login">
        <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="btn btn-ghost btn-sm" style="background: rgba(255,255,255,0.1); color: #fff;">
            {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
        </a>
    </div>

    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="ph-fill ph-first-aid-kit"></i>
            </div>
            <h1 class="login-title">{{ App\Models\Setting::get('system_name', __('auth.login.system_name')) }}</h1>
            <p class="login-subtitle">{{ __('auth.login.subtitle') }}</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger mb-24">
                <i class="ph-bold ph-warning-circle"></i>
                <div>{{ __('auth.login.error') }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group mb-16">
                <label class="form-label">{{ __('auth.login.email') }}</label>
                <div class="input-wrap">
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required autofocus>
                    <i class="ph-bold ph-envelope input-icon"></i>
                </div>
            </div>

            <div class="form-group mb-24">
                <div class="d-flex justify-content-between align-center mb-8">
                    <label class="form-label mb-0">{{ __('auth.login.password') }}</label>
                    <a href="{{ route('password.request') }}" class="text-sm fw-600" style="color: var(--clr-primary-600);">{{ __('auth.login.forgot_password') }}</a>
                </div>
                <div class="input-wrap">
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    <i class="ph-bold ph-lock-simple input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <span>{{ __('auth.login.submit') }}</span>
                <i class="ph-bold ph-arrow-left" style="{{ app()->getLocale() === 'en' ? 'transform: rotate(180deg);' : '' }}"></i>
            </button>
        </form>
    </div>
</body>
</html>
