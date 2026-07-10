<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Anbiyaa Sport</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3%22 fill=%22%230ea5e9%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22/><path d=%22M8 14.5h8%22 stroke=%22%232563eb%22 stroke-width=%223%22/><path d=%22M7.5 13.5L5 5%22 stroke-width=%222.8%22/><path d=%22M12 13.5V4%22 stroke-width=%222.8%22/><path d=%22M16.5 13.5L19 5%22 stroke-width=%222.8%22/><path d=%22M6 9.5h12%22 stroke-width=%221.8%22 opacity=%220.75%22/></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #030712;
            min-height: 100vh;
            display: flex;
            margin: 0;
            overflow: hidden;
        }
        
        /* ── Kiri: Bagian Visual (Midnight dark theme with glows) ── */
        .login-visual {
            flex: 1.2;
            background: linear-gradient(135deg, #030712 0%, #080c15 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            padding: 3.5rem;
            color: white;
            overflow: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.04);
        }
        
        /* Neon Blur Orbs */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.12;
            z-index: 1;
            pointer-events: none;
            animation: pulse-glow 15s infinite alternate ease-in-out;
        }
        .orb-1 {
            width: 350px;
            height: 350px;
            background: #0ea5e9;
            top: 15%;
            left: 10%;
        }
        .orb-2 {
            width: 450px;
            height: 450px;
            background: #7c3aed;
            bottom: 15%;
            right: 5%;
            animation-delay: -7s;
        }
        @keyframes pulse-glow {
            0% { transform: scale(1) translate(0, 0); }
            50% { transform: scale(1.15) translate(30px, -20px); }
            100% { transform: scale(1) translate(0, 0); }
        }

        .login-visual::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(56,189,248,0.06) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.06) 0%, transparent 50%);
            z-index: 1;
        }
        
        .visual-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
            letter-spacing: -0.5px;
            margin-bottom: auto;
        }
        
        .brand-logo svg {
            color: #0ea5e9;
            filter: drop-shadow(0 0 10px rgba(14, 165, 233, 0.6));
        }
        
        .visual-text {
            max-width: 500px;
            margin-bottom: auto;
        }
        
        .visual-text h1 {
            font-weight: 800;
            font-size: 2.75rem;
            margin-bottom: 1.25rem;
            line-height: 1.25;
            letter-spacing: -1.2px;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .visual-text p {
            color: #94a3b8;
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 2.25rem;
        }

        /* Premium Glass Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.02) !important;
            backdrop-filter: blur(24px) !important;
            -webkit-backdrop-filter: blur(24px) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 18px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            max-width: 440px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        
        .glass-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; bottom: 0;
            width: 3px;
            background: var(--glow-color);
            box-shadow: 0 0 12px var(--glow-color);
        }
        
        .glass-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; flex-shrink: 0;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--glow-color) !important;
            box-shadow: 0 0 15px rgba(var(--glow-rgb), 0.2);
            transition: all 0.3s ease;
        }

        .glass-icon i {
            color: var(--glow-color) !important;
            filter: drop-shadow(0 0 5px var(--glow-color));
        }

        .glass-card:hover {
            border-color: rgba(var(--glow-rgb), 0.3) !important;
            box-shadow: 0 15px 35px rgba(var(--glow-rgb), 0.1) !important;
            transform: translateY(-2px);
        }

        .badges-container {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.75rem;
        }

        .feature-badge {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #cbd5e1;
        }

        /* ── Kanan: Form Login (Elegant Dark Obsidian Container) ── */
        .login-form-container {
            flex: 0 0 520px;
            background: rgba(9, 13, 22, 0.98);
            display: flex;
            flex-direction: column;
            padding: 2.5rem 3rem;
            position: relative;
            box-shadow: -15px 0 45px rgba(0, 0, 0, 0.4);
            overflow-y: auto;
            border-left: 1px solid rgba(255, 255, 255, 0.04);
        }
        
        .form-wrapper {
            width: 100%;
            max-width: 380px;
            margin: auto;
        }
        
        .form-title {
            font-weight: 800;
            color: #ffffff;
            font-size: 1.9rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.8px;
        }
        
        .form-subtitle {
            color: #94a3b8;
            font-size: 0.92rem;
            margin-bottom: 2.25rem;
            line-height: 1.5;
        }
        
        .form-label {
            font-weight: 700;
            font-size: 0.78rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 0.5rem;
        }
        
        /* Premium Input Groups */
        .input-group {
            background: rgba(255, 255, 255, 0.02);
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .input-group:focus-within {
            border-color: #0ea5e9;
            background: rgba(255, 255, 255, 0.04);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
        }
        
        .input-group-text {
            background: transparent;
            border: none;
            color: #64748b;
            padding: 0.75rem 1rem;
        }
        
        .form-control {
            border: none;
            padding: 0.75rem 1rem 0.75rem 0;
            font-size: 0.92rem;
            background: transparent;
            font-weight: 600;
            color: #ffffff;
        }
        
        .form-control:focus {
            box-shadow: none;
            background: transparent;
            color: #ffffff;
        }
        
        .form-control::placeholder {
            color: #4b5563;
            font-weight: 500;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #0ea5e9 0%, #7c3aed 100%);
            color: white;
            font-weight: 700;
            padding: 0.95rem;
            border-radius: 12px;
            width: 100%;
            border: none;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            margin-top: 0.75rem;
            box-shadow: 0 4px 18px rgba(14, 165, 233, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(14, 165, 233, 0.45);
            background: linear-gradient(135deg, #0284c7 0%, #6d28d9 100%);
        }

        .btn-login:active {
            transform: translateY(0);
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.75rem 0;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 600;
        }
        
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1.5px dashed rgba(255, 255, 255, 0.08);
        }
        
        .divider::before { margin-right: .75em; }
        .divider::after { margin-left: .75em; }
        
        .register-link {
            color: #0ea5e9;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .register-link:hover {
            color: #38bdf8;
            text-decoration: underline;
        }
        
        .custom-checkbox .form-check-input {
            width: 1.15em;
            height: 1.15em;
            border-color: rgba(255, 255, 255, 0.2);
            background-color: transparent;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .custom-checkbox .form-check-input:checked {
            background-color: #0ea5e9;
            border-color: #0ea5e9;
        }
        
        .custom-checkbox .form-check-label {
            font-size: 0.88rem;
            color: #94a3b8;
            font-weight: 600;
            cursor: pointer;
        }
 
        .alert {
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.88rem;
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #34d399;
        }
 
        /* ── Promo Banner Slider Logic ── */
        .promo-slider {
            position: relative;
            min-height: 140px;
            overflow: hidden;
            width: 100%;
        }
        
        .promo-slide {
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            position: absolute;
            inset: 0;
            opacity: 0;
            transform: translateY(20px) scale(0.95);
            pointer-events: none;
            display: flex;
            align-items: center;
        }
        
        .promo-slide.active {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
        
        .slider-dots {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 1.5rem;
            padding-left: 0.5rem;
        }
        
        .slider-dots .dot {
            width: 8px;
            height: 8px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.15);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .slider-dots .dot.active {
            width: 24px;
            background: #0ea5e9;
        }

        /* Password Toggle */
        .btn-toggle-password {
            border: none;
            background: transparent;
            color: #64748b;
            padding: 0.5rem 1rem;
            transition: color 0.2s;
        }
        .btn-toggle-password:hover {
            color: #94a3b8;
        }
        /* Hide browser default password reveal and clear icons */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }

        /* Sequential loading animations */
        .form-field-animate {
            opacity: 0;
            transform: translateY(15px);
            animation: fadeUpIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeUpIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .field-delay-1 { animation-delay: 0.05s; }
        .field-delay-2 { animation-delay: 0.1s; }
        .field-delay-3 { animation-delay: 0.15s; }
        .field-delay-4 { animation-delay: 0.2s; }
        .field-delay-5 { animation-delay: 0.25s; }
        .field-delay-6 { animation-delay: 0.3s; }
        .field-delay-7 { animation-delay: 0.35s; }

        /* Floating elements */
        @keyframes float-effect {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }
        .floating-element {
            animation: float-effect 6s ease-in-out infinite;
        }

        /* Responsive */
        @media (max-width: 991px) {
            body { flex-direction: column; overflow: auto; background-color: #030712; }
            .login-visual { display: none; }
            .login-form-container { flex: 1; width: 100%; padding: 2.5rem 1.5rem; box-shadow: none; background-color: #030712; border-left: none; }
            .form-wrapper { max-width: 100%; }
            .brand-logo-mobile { display: flex !important; justify-content: center; margin-bottom: 2rem; }
        }
        .brand-logo-mobile { display: none; }
    </style>
</head>
<body>

    {{-- KIRI: Visual/Banner --}}
    <div class="login-visual">
        <div class="glow-orb orb-1"></div>
        <div class="glow-orb orb-2"></div>
        <div class="visual-content">
            <a href="{{ route('home') }}" class="brand-logo animate-fade-up">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="brand-icon-svg" style="width: 2rem; height: 2rem; display: inline-block; vertical-align: middle; filter: drop-shadow(0 0 8px rgba(14, 165, 233, 0.7));">
                    <path d="M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3" fill="#0ea5e9" stroke="#0ea5e9" stroke-width="2.8"/>
                    <path d="M8 14.5h8" stroke="#2563eb" stroke-width="3"/>
                    <path d="M7.5 13.5L5 5" stroke-width="2.8"/>
                    <path d="M12 13.5V4" stroke-width="2.8"/>
                    <path d="M16.5 13.5L19 5" stroke-width="2.8"/>
                    <path d="M6 9.5h12" stroke-width="1.8" opacity="0.75"/>
                </svg>
                AnbiyaaSport
            </a>

            <div class="visual-text animate-fade-up delay-1">
                <h1>Sistem Sewa GOR <br><span style="color:#0ea5e9">Anbiyaa Sport</span></h1>
                <p>Cek ketersediaan lapangan secara real-time, lakukan reservasi, kumpulkan poin loyalitas, dan klaim diskon sewa dengan mudah.</p>
                
                <!-- Promo Slider Container -->
                <div class="promo-slider">
                    <!-- Slide 1 -->
                    <div class="promo-slide active">
                        <div class="glass-card card-theme-green m-0 w-100" style="--glow-color: #10b981; --glow-rgb: 16, 185, 129;">
                            <div class="glass-icon"><i class="bi bi-gift-fill"></i></div>
                            <div>
                                <h6 class="mb-1 fw-bold text-white" style="font-size:0.95rem;">Program Loyalitas Pelanggan 🏸</h6>
                                <p class="mb-0 text-white-50" style="font-size:0.8rem;">Setiap Rp 10.000 transaksi booking bernilai <strong>1 Poin</strong>. Kumpulkan &amp; tukar poin dengan Voucher Diskon!</p>
                            </div>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="promo-slide">
                        <div class="glass-card card-theme-blue m-0 w-100" style="--glow-color: #3b82f6; --glow-rgb: 59, 130, 246;">
                            <div class="glass-icon"><i class="bi bi-ticket-perforated-fill"></i></div>
                            <div>
                                <h6 class="mb-1 fw-bold text-white" style="font-size:0.95rem;">Voucher Diskon Otomatis 🎫</h6>
                                <p class="mb-0 text-white-50" style="font-size:0.8rem;">Gunakan voucher reward loyalty atau keanggotaan Anda langsung saat booking lapangan secara online, tanpa perlu ke kasir!</p>
                            </div>
                        </div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="promo-slide">
                        <div class="glass-card card-theme-amber m-0 w-100" style="--glow-color: #f59e0b; --glow-rgb: 245, 158, 11;">
                            <div class="glass-icon"><i class="bi bi-shield-check-fill"></i></div>
                            <div>
                                <h6 class="mb-1 fw-bold text-white" style="font-size:0.95rem;">Proteksi Jadwal Aman 🔒</h6>
                                <p class="mb-0 text-white-50" style="font-size:0.8rem;">Data penyewaan Anda dijamin aman. Sistem mencegah double-booking secara otomatis demi kenyamanan bermain Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider Dots -->
                <div class="slider-dots">
                    <span class="dot active" data-slide="0"></span>
                    <span class="dot" data-slide="1"></span>
                    <span class="dot" data-slide="2"></span>
                </div>

                <div class="badges-container floating-element delay-3">
                    <div class="feature-badge"><i class="bi bi-gem text-warning"></i> Loyalty Points</div>
                    <div class="feature-badge"><i class="bi bi-calendar2-check text-info"></i> Real-time Jadwal</div>
                </div>
            </div>
        </div>
    </div>

    {{-- KANAN: Form Login --}}
    <div class="login-form-container">
        <div class="form-wrapper">
            
            {{-- Logo khusus mobile (muncul jika layar kecil) --}}
            <div class="brand-logo-mobile">
                <a href="{{ route('home') }}" class="brand-logo" style="color: #ffffff;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="brand-icon-svg" style="width: 1.8rem; height: 1.8rem; display: inline-block; vertical-align: middle; filter: drop-shadow(0 0 8px rgba(14, 165, 233, 0.5));">
                        <path d="M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3" fill="#0ea5e9" stroke="#0ea5e9" stroke-width="2.8"/>
                        <path d="M8 14.5h8" stroke="#2563eb" stroke-width="3"/>
                        <path d="M7.5 13.5L5 5" stroke-width="2.8"/>
                        <path d="M12 13.5V4" stroke-width="2.8"/>
                        <path d="M16.5 13.5L19 5" stroke-width="2.8"/>
                        <path d="M6 9.5h12" stroke-width="1.8" opacity="0.75"/>
                    </svg>
                    AnbiyaaSport
                </a>
            </div>

            <h2 class="form-title form-field-animate field-delay-1">Selamat Datang Kembali!</h2>
            <p class="form-subtitle form-field-animate field-delay-2">Silakan masukkan kredensial Anda untuk mengakses akun Anda.</p>

            {{-- Alert Messages --}}
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4 form-field-animate field-delay-2">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 form-field-animate field-delay-2">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                
                {{-- Hidden Fields for Booking Redirection --}}
                @if(request()->filled('lapangan_id') && request()->filled('tanggal') && request()->filled('jam_mulai'))
                    <input type="hidden" name="lapangan_id" value="{{ request('lapangan_id') }}">
                    <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
                    <input type="hidden" name="jam_mulai" value="{{ request('jam_mulai') }}">
                    <input type="hidden" name="jam_selesai" value="{{ request('jam_selesai') }}">
                @endif
                
                <div class="mb-4 form-field-animate field-delay-3">
                    <label class="form-label">Username / No. WhatsApp</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username atau no. HP" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-4 form-field-animate field-delay-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required>
                        <button class="btn-toggle-password" type="button" id="togglePasswordBtn" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 form-field-animate field-delay-5">
                    <div class="form-check custom-checkbox">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat Saya</label>
                    </div>
                </div>

                <button type="submit" class="btn-login form-field-animate field-delay-6">
                    Masuk ke Akun <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="divider form-field-animate field-delay-6">Pengguna Baru?</div>

            <div class="text-center form-field-animate field-delay-7">
                <a href="{{ route('register', request()->only(['lapangan_id', 'tanggal', 'jam_mulai', 'jam_selesai'])) }}" class="register-link">
                    Buat akun sekarang
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ── Password Visibility Toggle ──
            const passwordInput = document.getElementById('passwordInput');
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            
            if (togglePasswordBtn && passwordInput) {
                togglePasswordBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    const icon = this.querySelector('i');
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                });
            }

            // ── Promo Banner Slider Logic ──
            const slides = document.querySelectorAll('.promo-slide');
            const dots = document.querySelectorAll('.slider-dots .dot');
            let currentSlide = 0;
            let slideInterval;

            function showSlide(index) {
                if (slides.length === 0) return;
                slides.forEach((slide, i) => {
                    if (i === index) {
                        slide.classList.add('active');
                    } else {
                        slide.classList.remove('active');
                    }
                });

                dots.forEach((dot, i) => {
                    if (i === index) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
                currentSlide = index;
            }

            function nextSlide() {
                showSlide((currentSlide + 1) % slides.length);
            }

            function startSlideShow() {
                if (slides.length > 0) {
                    slideInterval = setInterval(nextSlide, 5000);
                }
            }

            if (slides.length > 0 && dots.length > 0) {
                startSlideShow();
                dots.forEach((dot, i) => {
                    dot.addEventListener('click', () => {
                        clearInterval(slideInterval);
                        showSlide(i);
                        startSlideShow();
                    });
                });
            }

            // ── Loading State on Submit ──
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('.btn-login');
            
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    // Cek kelayakan form HTML5 validation
                    if (form.checkValidity()) {
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memproses...';
                        setTimeout(() => {
                            submitBtn.disabled = true;
                        }, 1);
                    }
                });
            }
        });
    </script>
</body>
</html>
