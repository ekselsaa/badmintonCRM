<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Anbiyaa Sport</title>
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

        /* ── Kanan: Form Register (Elegant Dark Obsidian Container - Fit Selayar) ── */
        .login-form-container {
            flex: 0 0 520px;
            background: rgba(9, 13, 22, 0.98);
            display: flex;
            flex-direction: column;
            padding: 1.5rem 2.25rem; /* Reduced padding to fit screen height */
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
            font-size: 1.65rem; /* Reduced font size */
            margin-bottom: 0.25rem;
            letter-spacing: -0.8px;
        }
        
        .form-subtitle {
            color: #94a3b8;
            font-size: 0.85rem;
            margin-bottom: 1rem; /* Reduced margin */
            line-height: 1.4;
        }
        
        .form-label {
            font-weight: 700;
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 0.35rem;
        }
        
        /* Compact Input Margins to Fit Desktop Viewport */
        .login-form-container .mb-3 {
            margin-bottom: 0.65rem !important;
        }
        .login-form-container .mb-2 {
            margin-bottom: 0.5rem !important;
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
            padding: 0.65rem 1rem; /* Reduced padding */
        }
        
        .form-control {
            border: none;
            padding: 0.65rem 1rem 0.65rem 0; /* Reduced padding */
            font-size: 0.9rem;
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
        
        .form-text.text-muted {
            color: #64748b !important;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #0ea5e9 0%, #7c3aed 100%);
            color: white;
            font-weight: 700;
            padding: 0.8rem; /* Reduced padding */
            border-radius: 12px;
            width: 100%;
            border: none;
            font-size: 0.92rem;
            transition: all 0.25s ease;
            margin-top: 0.5rem;
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
            margin: 1rem 0; /* Reduced margin */
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
        
        /* Password Strength Wrapper styling */
        .progress {
            background-color: rgba(255, 255, 255, 0.1) !important;
            height: 4px !important;
            border-radius: 4px;
        }
        .password-strength-wrapper {
            margin-top: 0.4rem;
        }
        #strengthText.text-danger, #confirmMatchText.text-danger { color: #f87171 !important; }
        #strengthText.text-warning, #confirmMatchText.text-warning { color: #fbbf24 !important; }
        #strengthText.text-info, #confirmMatchText.text-info { color: #38bdf8 !important; }
        #strengthText.text-success, #confirmMatchText.text-success { color: #34d399 !important; }

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
                <p>Gabung bersama ribuan pemain bulutangkis lainnya. Booking lapangan jadi lebih praktis, transparan, dan bertabur keuntungan!</p>
                
                <!-- Promo Slider Container -->
                <div class="promo-slider">
                    <!-- Slide 1 -->
                    <div class="promo-slide active">
                        <div class="glass-card card-theme-green m-0 w-100" style="--glow-color: #10b981; --glow-rgb: 16, 185, 129;">
                            <div class="glass-icon"><i class="bi bi-gift-fill"></i></div>
                            <div>
                                <h6 class="mb-1 fw-bold text-white" style="font-size:0.95rem;">Program Loyalitas Pelanggan 🏸</h6>
                                <p class="mb-0 text-white-50" style="font-size:0.8rem;">Setiap kelipatan <strong>Rp 5.000</strong> transaksi booking bernilai <strong>1 Poin</strong>. Off-Peak Weekdays mendapat <strong>Double Points (2×)</strong>! Kumpulkan &amp; tukar poin dengan Voucher menarik.</p>
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

    {{-- KANAN: Form Registrasi --}}
    <div class="login-form-container">
        <div class="form-wrapper">
            
            {{-- Logo khusus mobile --}}
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

            <h2 class="form-title form-field-animate field-delay-1">Buat Akun Baru</h2>
            <p class="form-subtitle form-field-animate field-delay-2">Lengkapi data diri di bawah ini untuk menjadi member Anbiyaa Sport.</p>

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 form-field-animate field-delay-2">
                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf
                
                {{-- Hidden Fields for Booking Redirection --}}
                @if(request()->filled('lapangan_id') && request()->filled('tanggal') && request()->filled('jam_mulai'))
                    <input type="hidden" name="lapangan_id" value="{{ request('lapangan_id') }}">
                    <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
                    <input type="hidden" name="jam_mulai" value="{{ request('jam_mulai') }}">
                    <input type="hidden" name="jam_selesai" value="{{ request('jam_selesai') }}">
                @endif
                
                <div class="row g-2 mb-2">
                    <div class="col-12 form-field-animate field-delay-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama sesuai identitas" value="{{ old('name') }}" required autofocus>
                        </div>
                    </div>

                    <div class="col-md-6 form-field-animate field-delay-4">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" value="{{ old('email') }}" required>
                        </div>
                        <small class="form-text text-muted mt-1 d-block" style="font-size: 0.68rem; line-height: 1.2;">Untuk bukti kwitansi sewa resmi.</small>
                    </div>

                    <div class="col-md-6 form-field-animate field-delay-4">
                        <label class="form-label">Nomor WhatsApp</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                            <input type="text" name="nomor_hp" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('nomor_hp') }}">
                        </div>
                        <small class="form-text text-muted mt-1 d-block" style="font-size: 0.68rem; line-height: 1.2;">Untuk notifikasi booking instan.</small>
                    </div>
                </div>

                <div class="mb-2 form-field-animate field-delay-5">
                    <label class="form-label">Alamat Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" name="alamat" class="form-control" placeholder="Masukkan alamat lengkap (opsional)" value="{{ old('alamat') }}">
                    </div>
                </div>

                <div class="row g-2 mb-3 form-field-animate field-delay-6">
                    <div class="col-md-6">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="passwordInput" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                            <button class="btn-toggle-password" type="button" id="togglePasswordBtn" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <!-- Strength Meter Container -->
                        <div class="password-strength-wrapper" style="display: none;">
                            <div class="progress" style="height: 4px; border-radius: 4px; background-color: #e2e8f0;">
                                <div id="strengthMeter" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span id="strengthText" class="d-block mt-1" style="font-size: 0.68rem; font-weight: 700;"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Konfirmasi <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password_confirmation" id="passwordConfirmInput" class="form-control" placeholder="Ulangi password" required>
                            <button class="btn-toggle-password" type="button" id="togglePasswordConfirmBtn" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <span id="confirmMatchText" class="d-block mt-1" style="font-size: 0.68rem; font-weight: 700; display: none;"></span>
                    </div>
                </div>

                <button type="submit" class="btn-login form-field-animate field-delay-7">
                    <i class="bi bi-person-plus-fill me-1"></i> Daftar Sekarang
                </button>
            </form>

            <div class="divider form-field-animate field-delay-7">Atau sudah punya akun?</div>

            <div class="text-center form-field-animate field-delay-7">
                <a href="{{ route('login', request()->only(['lapangan_id', 'tanggal', 'jam_mulai', 'jam_selesai'])) }}" class="register-link">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk di sini
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
            const passwordConfirmInput = document.getElementById('passwordConfirmInput');
            const togglePasswordConfirmBtn = document.getElementById('togglePasswordConfirmBtn');
            
            if (togglePasswordBtn && passwordInput) {
                togglePasswordBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    const icon = this.querySelector('i');
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                });
            }

            if (togglePasswordConfirmBtn && passwordConfirmInput) {
                togglePasswordConfirmBtn.addEventListener('click', function() {
                    const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordConfirmInput.setAttribute('type', type);
                    const icon = this.querySelector('i');
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                });
            }

            // ── Password Strength Indicator ──
            const strengthWrapper = document.querySelector('.password-strength-wrapper');
            const strengthMeter = document.getElementById('strengthMeter');
            const strengthText = document.getElementById('strengthText');

            if (passwordInput && strengthMeter && strengthText) {
                passwordInput.addEventListener('input', function() {
                    const val = passwordInput.value;
                    if (val.length === 0) {
                        strengthWrapper.style.display = 'none';
                        return;
                    }
                    strengthWrapper.style.display = 'block';

                    let score = 0;
                    if (val.length >= 6) score += 25;
                    if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score += 25;
                    if (/\d/.test(val)) score += 25;
                    if (/[^a-zA-Z0-9]/.test(val)) score += 25;

                    strengthMeter.style.width = score + '%';
                    
                    if (score <= 25) {
                        strengthMeter.className = 'progress-bar bg-danger';
                        strengthText.innerText = 'Sangat Lemah (Gunakan kombinasi huruf & angka)';
                        strengthText.className = 'd-block mt-1 text-danger';
                    } else if (score <= 50) {
                        strengthMeter.className = 'progress-bar bg-warning';
                        strengthText.innerText = 'Cukup Kuat (Gunakan huruf besar & simbol)';
                        strengthText.className = 'd-block mt-1 text-warning';
                    } else if (score <= 75) {
                        strengthMeter.className = 'progress-bar bg-info';
                        strengthText.innerText = 'Kuat';
                        strengthText.className = 'd-block mt-1 text-info';
                    } else {
                        strengthMeter.className = 'progress-bar bg-success';
                        strengthText.innerText = 'Sangat Kuat!';
                        strengthText.className = 'd-block mt-1 text-success';
                    }
                });
            }

            // ── Password Confirmation Matcher ──
            const confirmMatchText = document.getElementById('confirmMatchText');
            if (passwordInput && passwordConfirmInput && confirmMatchText) {
                function checkMatch() {
                    if (passwordConfirmInput.value.length === 0) {
                        confirmMatchText.style.display = 'none';
                        return;
                    }
                    confirmMatchText.style.display = 'block';
                    if (passwordInput.value === passwordConfirmInput.value) {
                        confirmMatchText.innerText = '✓ Password cocok';
                        confirmMatchText.className = 'd-block mt-1 text-success';
                    } else {
                        confirmMatchText.innerText = '✗ Password tidak cocok';
                        confirmMatchText.className = 'd-block mt-1 text-danger';
                    }
                }
                passwordInput.addEventListener('input', checkMatch);
                passwordConfirmInput.addEventListener('input', checkMatch);
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
                    if (form.checkValidity()) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mendaftarkan...';
                    }
                });
            }
        });
    </script>
</body>
</html>
