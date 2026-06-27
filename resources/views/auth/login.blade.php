<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Login - {{ config('app.name', 'POS Kulu Asri') }}</title>
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
        }

        .login-left {
            flex: 1;
            background: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1974&auto=format&fit=crop') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 3rem;
            position: relative;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9) 0%, rgba(5, 150, 105, 0.9) 100%);
            z-index: 1;
        }

        .login-left-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .login-left-content .icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .login-left h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: -1px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .login-left p {
            font-size: 1.15rem;
            opacity: 0.9;
            max-width: 420px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .login-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: white;
            padding: 3rem;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            font-size: 2.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #64748b;
            font-size: 1rem;
        }

        .form-control {
            padding: 0.8rem 1.2rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 1rem;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .form-label {
            font-weight: 500;
            color: #475569;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .btn-login {
            background: #10b981;
            border: none;
            color: white;
            padding: 0.9rem 1.5rem;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }

        .btn-login:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-check-input:checked {
            background-color: #10b981;
            border-color: #10b981;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
        }

        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        @media (max-width: 991px) {
            .login-left {
                display: none;
            }
            .login-right {
                background: #f8fafc;
            }
            .login-box {
                background: white;
                padding: 3rem;
                border-radius: 24px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side: Branding/Image -->
        <div class="login-left">
            <div class="login-left-content">
                <div class="logo-wrapper mb-4">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Kulu Asri Logo" style="width: 320px; max-width: 100%; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); border: 3px solid rgba(255,255,255,0.3);">
                </div>
                <p>Sistem Point of Sale Modern untuk Mengelola Bisnis Kuliner Anda dengan Lebih Cepat dan Efisien.</p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="login-right">
            <div class="login-box">
                <div class="login-header">
                    <div class="d-lg-none mb-4 text-center">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Kulu Asri Logo" style="width: 180px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                    </div>
                    <h2>Selamat Datang! 👋</h2>
                    <p>Silakan login ke akun Anda untuk melanjutkan.</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Masukkan email anda">
                        
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="password" class="form-label mb-0">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none" href="{{ route('password.request') }}" style="color: #10b981; font-size: 0.9rem; font-weight: 500;">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Masukkan password anda">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted" for="remember" style="font-size: 0.95rem;">
                                Ingat Saya
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">
                        Masuk Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
