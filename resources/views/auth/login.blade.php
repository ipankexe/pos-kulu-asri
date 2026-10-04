<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Login POS — Rumah Makan Kulu Asri</title>
    
    <!-- Design Tokens & Fonts -->
    <link rel="stylesheet" href="{{ asset('css/design-tokens.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --brand-green: var(--color-primary);
            --brand-green-dark: var(--color-primary-dark);
            --brand-amber: var(--color-accent);
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: var(--font-family-sans);
            background-color: var(--color-background);
            color: var(--color-text-main);
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
        }

        /* Left Hero Banner */
        .login-left {
            flex: 1.15;
            background: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1974&auto=format&fit=crop') center/cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
            padding: 3.5rem;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(145deg, rgba(6, 78, 59, 0.94) 0%, rgba(2, 44, 34, 0.96) 65%, rgba(180, 83, 9, 0.45) 100%);
            z-index: 1;
        }

        .login-left-content {
            position: relative;
            z-index: 2;
            max-width: 520px;
        }

        .brand-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            backdrop-filter: blur(12px);
            padding: 7px 18px;
            border-radius: var(--radius-full);
            color: #fef3c7;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .login-left h1 {
            font-size: 2.85rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin-bottom: 0.85rem;
        }

        .brand-tagline-hero {
            font-family: var(--font-family-serif);
            font-style: italic;
            font-weight: 700;
            font-size: 1.45rem;
            color: var(--ka-amber-300);
            display: block;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.25);
        }

        .login-left p {
            font-size: 1rem;
            line-height: 1.65;
            color: rgba(241, 245, 249, 0.9);
            margin-bottom: 2.5rem;
        }

        /* Feature Pills */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .feature-pill {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 600;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feature-pill i {
            font-size: 1.25rem;
            color: var(--ka-amber-300);
        }

        .brand-footer-left {
            position: relative;
            z-index: 2;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.65);
        }

        /* Right Form Side */
        .login-right {
            flex: 0.85;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: #ffffff;
            padding: 3.5rem 3rem;
            position: relative;
        }

        .login-box {
            width: 100%;
            max-width: 430px;
        }

        .login-header {
            margin-bottom: 2.25rem;
        }

        .login-header .brand-icon-sq {
            width: 48px;
            height: 48px;
            background: var(--ka-emerald-50);
            color: var(--ka-emerald-700);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
            border: 1px solid var(--ka-emerald-200);
        }

        .login-header h2 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--color-text-main);
            letter-spacing: -0.02em;
            margin-bottom: 0.4rem;
        }

        .login-header p {
            color: var(--color-text-secondary);
            font-size: 0.92rem;
            margin: 0;
        }

        /* Form Inputs */
        .form-label {
            font-weight: 600;
            color: var(--ka-slate-700);
            margin-bottom: 0.45rem;
            font-size: 0.88rem;
        }

        .custom-input-group {
            border: 1.5px solid var(--ka-slate-200);
            border-radius: var(--radius-md);
            background: #ffffff;
            transition: all var(--transition-fast);
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .custom-input-group:focus-within {
            border-color: var(--ka-emerald-600);
            box-shadow: 0 0 0 4px rgba(4, 120, 87, 0.12);
        }

        .custom-input-group .input-icon {
            padding-left: 14px;
            padding-right: 10px;
            color: var(--ka-emerald-700);
            font-size: 1.15rem;
            display: flex;
            align-items: center;
        }

        .custom-input-group .form-control {
            border: none;
            background: transparent;
            padding: 13px 14px 13px 4px;
            font-size: 0.95rem;
            color: var(--ka-slate-900);
            box-shadow: none !important;
            font-family: var(--font-family-sans);
        }

        .custom-input-group .form-control::placeholder {
            color: var(--ka-slate-400);
            font-size: 0.9rem;
        }

        .btn-toggle-eye {
            background: transparent;
            border: none;
            color: var(--ka-slate-400);
            padding-right: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: color var(--transition-fast);
        }

        .btn-toggle-eye:hover {
            color: var(--ka-slate-700);
        }

        /* Submit Button */
        .btn-login {
            background: linear-gradient(135deg, var(--ka-emerald-700) 0%, var(--ka-emerald-800) 100%);
            border: none;
            color: white;
            padding: 13px 24px;
            border-radius: var(--radius-md);
            font-size: 1rem;
            font-weight: 700;
            width: 100%;
            margin-top: 0.75rem;
            transition: all var(--transition-normal);
            box-shadow: 0 4px 14px rgba(4, 120, 87, 0.28);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, var(--ka-emerald-600) 0%, var(--ka-emerald-700) 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-emerald-glow);
            color: white;
        }

        .btn-login:active {
            transform: translateY(0) scale(0.98);
        }

        .form-check-input:checked {
            background-color: var(--ka-emerald-700);
            border-color: var(--ka-emerald-700);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(4, 120, 87, 0.2);
        }

        .invalid-feedback {
            font-size: 0.82rem;
            margin-top: 0.4rem;
        }

        /* Mobile View */
        @media (max-width: 991px) {
            .login-left {
                display: none;
            }
            .login-right {
                background: var(--color-background);
                padding: 2.5rem 1.5rem;
            }
            .login-box {
                background: #ffffff;
                padding: 2.5rem 2rem;
                border-radius: var(--radius-xl);
                box-shadow: var(--shadow-lg);
                border: 1px solid var(--color-border);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side: Brand Narrative & Visual Artistry -->
        <div class="login-left">
            <div class="login-left-content">
                <div class="brand-pill-badge">
                    <i class="bi bi-fire text-warning"></i>
                    <span>Cita Rasa Nusantara • Sejak 2012</span>
                </div>
                
                <h1>Rumah Makan<br>Kulu Asri</h1>
                <span class="brand-tagline-hero">"Jagonya Ikan Bakar!"</span>
                
                <p>
                    Platform Point of Sale (POS) dan Pemesanan Mandiri (QR Self-Order) modern terintegrasi, dirancang khusus untuk kecepatan kasir dan kenyamanan tamu kuliner.
                </p>

                <!-- Feature Highlights -->
                <div class="feature-grid">
                    <div class="feature-pill">
                        <i class="bi bi-lightning-charge-fill"></i>
                        <span>Kasir Cepat & Cetak Struk</span>
                    </div>
                    <div class="feature-pill">
                        <i class="bi bi-qr-code-scan"></i>
                        <span>Self-Order QR Meja</span>
                    </div>
                    <div class="feature-pill">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>Real-time Sales Analytics</span>
                    </div>
                    <div class="feature-pill">
                        <i class="bi bi-shield-check"></i>
                        <span>Void Audit & Role Security</span>
                    </div>
                </div>
            </div>

            <!-- Left Footer Signoff -->
            <div class="brand-footer-left">
                <span>&copy; {{ date('Y') }} RM Kulu Asri POS System</span>
                <span>v2.4 Enterprise Edition</span>
            </div>
        </div>

        <!-- Right Side: Clean Modern Form -->
        <div class="login-right">
            <div class="login-box">
                <div class="login-header">
                    <div class="brand-icon-sq">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h2>Selamat Datang</h2>
                    <p>Masukkan kredensial akun kasir atau administrator Anda.</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <div class="custom-input-group @error('email') border-danger @enderror">
                            <span class="input-icon"><i class="bi bi-envelope"></i></span>
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
                            <label for="password" class="form-label mb-0">Password Akun</label>
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none small fw-semibold" href="{{ route('password.request') }}" style="color: var(--ka-emerald-700);">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                        <div class="custom-input-group @error('password') border-danger @enderror">
                            <span class="input-icon"><i class="bi bi-shield-lock"></i></span>
                            <input id="password" 
                                   type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="••••••••">
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
                                Simpan sesi login ini
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-login">
                        <span>Masuk ke Dashboard / POS</span>
                        <i class="bi bi-arrow-right fs-5"></i>
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top">
                    <small style="font-family: var(--font-family-serif); font-style: italic; color: var(--ka-emerald-800); font-size: 13px; font-weight: 700;">
                        Kulu Asri <span style="color: var(--ka-amber-600);">—</span> Jagonya Ikan Bakar!
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & Toggle Script -->
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
