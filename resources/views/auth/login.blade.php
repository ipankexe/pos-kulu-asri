<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Login - POS Rumah Makan Kulu Asri</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --brand-green: #1b5e20;
            --brand-green-light: #2e7d32;
            --brand-orange: #d97706;
            --brand-orange-light: #f59e0b;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f5;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
        }

        /* Left Side (Banner) */
        .login-left {
            flex: 1.1;
            background: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1974&auto=format&fit=crop') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 3.5rem;
            position: relative;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(27, 94, 32, 0.94) 0%, rgba(46, 125, 50, 0.90) 100%);
            z-index: 1;
        }

        .login-left-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 480px;
        }

        .logo-wrapper {
            margin-bottom: 2rem;
        }

        .logo-wrapper img {
            width: 170px;
            height: auto;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
            border: 3px solid rgba(255, 255, 255, 0.4);
            background: white;
            padding: 8px;
        }

        .login-left h1 {
            font-size: 2.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .brand-tagline-badge {
            display: inline-block;
            background: rgba(254, 243, 199, 0.2);
            border: 1px solid rgba(253, 230, 138, 0.4);
            color: #fef3c7;
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 6px 20px;
            border-radius: 50px;
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
            backdrop-filter: blur(5px);
        }

        .login-left p {
            font-size: 0.95rem;
            opacity: 0.9;
            line-height: 1.6;
            color: #e2ece5;
        }

        /* Right Side (Form) */
        .login-right {
            flex: 0.9;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: #ffffff;
            padding: 3rem;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            margin-bottom: 2rem;
        }

        .login-header h2 {
            font-size: 1.85rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.35rem;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: #64748b;
            font-size: 0.92rem;
            margin: 0;
        }

        /* Neat Form Controls */
        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.45rem;
            font-size: 0.88rem;
        }

        .custom-input-group {
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            transition: all 0.25s ease;
            overflow: hidden;
        }

        .custom-input-group:focus-within {
            background: #ffffff;
            border-color: var(--brand-green-light);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.12);
        }

        .custom-input-group .input-group-text {
            background: transparent;
            border: none;
            color: #64748b;
            padding-left: 14px;
            padding-right: 10px;
            font-size: 1.1rem;
        }

        .custom-input-group .form-control {
            border: none;
            background: transparent;
            padding: 12px 14px 12px 4px;
            font-size: 0.95rem;
            color: #1e293b;
            box-shadow: none !important;
        }

        .custom-input-group .form-control::placeholder {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .btn-toggle-eye {
            background: transparent;
            border: none;
            color: #64748b;
            padding-right: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .btn-toggle-eye:hover {
            color: #1e293b;
        }

        /* Button Login */
        .btn-login {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            margin-top: 0.5rem;
            transition: all 0.25s ease;
            box-shadow: 0 6px 20px rgba(27, 94, 32, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(27, 94, 32, 0.35);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-check-input:checked {
            background-color: var(--brand-green);
            border-color: var(--brand-green);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.2);
        }

        .invalid-feedback {
            font-size: 0.82rem;
            margin-top: 0.4rem;
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .login-left {
                display: none;
            }
            .login-right {
                background: #f4f7f5;
                padding: 2rem 1.25rem;
            }
            .login-box {
                background: white;
                padding: 2.25rem 1.75rem;
                border-radius: 24px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.06);
                border: 1px solid #e2ece5;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side: Branding/Image -->
        <div class="login-left">
            <div class="login-left-content">
                <div class="logo-wrapper">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Kulu Asri Logo" onerror="this.style.display='none'">
                </div>
                <h1>Rumah Makan Kulu Asri</h1>
                <div class="brand-tagline-badge">
                    Kulu Asri - Jagonya Ikan Bakar!
                </div>
                <p>
                    Sistem Point of Sale (POS) & Self-Order QR Meja terintegrasi untuk pelayanan kasir cepat dan pemesanan mandiri yang nyaman bagi pelanggan.
                </p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="login-right">
            <div class="login-box">
                <div class="login-header">
                    <div class="d-lg-none mb-3 text-center">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Kulu Asri Logo" style="width: 110px; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 4px; background: white;" onerror="this.style.display='none'">
                    </div>
                    <h2>Selamat Datang! 👋</h2>
                    <p>Silakan masuk ke akun POS Kasir / Admin Kulu Asri.</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <div class="input-group custom-input-group @error('email') border-danger @enderror">
                            <span class="input-group-text"><i class="bi bi-envelope text-success"></i></span>
                            <input id="email" 
                                   type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus 
                                   placeholder="nama@kuluasri.com">
                        </div>
                        
                        @error('email')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none small fw-semibold" href="{{ route('password.request') }}" style="color: var(--brand-green);">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                        <div class="input-group custom-input-group @error('password') border-danger @enderror">
                            <span class="input-group-text"><i class="bi bi-shield-lock text-success"></i></span>
                            <input id="password" 
                                   type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="Masukkan password akun">
                            <button type="button" class="btn-toggle-eye" onclick="togglePasswordVisibility()" title="Lihat/Sembunyikan Password">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>

                        @error('password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted small" for="remember">
                                Ingat sesi saya
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-login">
                        <span>Masuk ke Sistem POS</span>
                        <i class="bi bi-arrow-right-short fs-5"></i>
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <small style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; color: #1b5e20; font-size: 13px; font-weight: 700;">
                        Kulu Asri <span style="color: #d97706;">-</span> Jagonya Ikan Bakar!
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & Toggle Password Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                pwdInput.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
