@extends('layouts.app')

@section('title', 'Kelola Akun Admin')
@section('page_title', 'Kelola Akun Admin')

@section('content')
<style>
    /* ── Premium Profile View Style ── */
    .profile-container {
        animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Glassmorphism Card style matching admin theme */
    .premium-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 20px;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }
    .premium-card:hover {
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.08);
        border-color: rgba(37, 99, 235, 0.15);
    }

    /* Interactive Avatar Edit */
    .avatar-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto;
        border-radius: 50%;
        padding: 6px;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.25);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .avatar-wrapper:hover {
        transform: scale(1.03) rotate(2deg);
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.35);
    }
    .avatar-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        background: #fff;
        border: 4px solid #fff;
    }
    .avatar-initial {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.8rem;
        font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        border: 4px solid #fff;
    }
    .avatar-upload-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .avatar-upload-btn:hover {
        background: #3b82f6;
        color: #fff;
        transform: scale(1.1);
    }

    /* Premium Input Elements */
    .form-group-premium {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .form-label-premium {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #64748b;
        margin-bottom: 0.5rem;
        display: block;
        transition: color 0.2s;
    }
    .form-control-premium {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        font-weight: 500;
        color: #1e293b;
        background: #fff;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .form-control-premium:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        background: #fff;
        outline: none;
    }
    .form-group-premium:focus-within .form-label-premium {
        color: #3b82f6;
    }

    .form-icon-premium {
        position: absolute;
        right: 1rem;
        top: 2.5rem;
        color: #94a3b8;
        font-size: 1.1rem;
        pointer-events: none;
        transition: color 0.2s;
    }
    .form-group-premium:focus-within .form-icon-premium {
        color: #3b82f6;
    }

    /* Section Separator */
    .section-title-premium {
        font-size: 0.95rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .section-title-premium i {
        color: #3b82f6;
    }

    /* Submit Button */
    .btn-save-premium {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border: none;
        color: #fff;
        font-weight: 700;
        letter-spacing: 0.5px;
        border-radius: 12px;
        padding: 0.8rem 2rem;
        box-shadow: 0 4px 15px rgba(29, 78, 216, 0.3);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
    }
    .btn-save-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(29, 78, 216, 0.45);
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    }
    .btn-save-premium:active {
        transform: translateY(0);
    }

    /* Badges */
    .badge-admin-premium {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
        border: 1px solid rgba(59, 130, 246, 0.2);
        color: #2563eb;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        display: inline-block;
    }
</style>

<div class="container-fluid profile-container py-2">
    <div class="row g-4">
        
        {{-- Kolom Kiri: Profil Card Preview --}}
        <div class="col-12 col-lg-4">
            <div class="premium-card p-4 text-center h-100 d-flex flex-column justify-content-between">
                <div>
                    {{-- Badge Status --}}
                    <div class="mb-4">
                        <span class="badge-admin-premium">
                            <i class="bi bi-shield-lock-fill me-1"></i> Administrator
                        </span>
                    </div>

                    {{-- Foto Profil Preview --}}
                    <div class="avatar-wrapper mb-4">
                        @if($user->foto_profil)
                            <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="{{ $user->name }}" class="avatar-preview" id="profileImagePreview">
                        @else
                            @php
                                $initials = strtoupper(substr($user->name, 0, 2));
                            @endphp
                            <div class="avatar-initial" id="profileInitialsPreview">{{ $initials }}</div>
                            <img src="" alt="Preview" class="avatar-preview d-none" id="profileImagePreview">
                        @endif

                        {{-- Input trigger file hidden --}}
                        <label for="fotoProfilInput" class="avatar-upload-btn" title="Unggah Foto Baru">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>

                    {{-- Data Ringkas --}}
                    <h5 class="fw-extrabold text-dark mb-1" style="letter-spacing: -0.5px;">{{ $user->name }}</h5>
                    <p class="text-muted small mb-4"><i class="bi bi-envelope-at me-1"></i> {{ $user->email }}</p>

                    <div style="border-top: 1px dashed #e2e8f0;" class="pt-3 mt-2 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                            <span>Kategori Akun</span>
                            <span class="fw-bold text-dark">Super Admin</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 small text-muted">
                            <span>Status Akun</span>
                            <span class="badge bg-success rounded-pill px-2 py-1" style="font-size: 0.65rem;">AKTIF</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span>Bergabung Sejak</span>
                            <span class="fw-bold text-dark">{{ $user->created_at->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 text-muted small" style="border-top: 1px solid #f1f5f9;">
                    <i class="bi bi-info-circle-fill text-primary"></i> Foto profil mendukung format JPG, JPEG, atau PNG dengan ukuran maksimal 2MB.
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Kelola Akun --}}
        <div class="col-12 col-lg-8">
            <div class="premium-card p-4 h-100">
                <form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data" id="adminProfileForm">
                    @csrf
                    @method('PUT')

                    {{-- Input file tersembunyi yang ditrigger dari Avatar --}}
                    <input type="file" name="foto_profil" id="fotoProfilInput" class="d-none" accept="image/png, image/jpeg, image/jpg">

                    {{-- Bagian 1: Data Profil --}}
                    <div class="section-title-premium">
                        <i class="bi bi-person-fill-gear"></i> Informasi Akun Admin
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-premium">
                                <label for="name" class="form-label-premium">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control form-control-premium w-100" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap admin">
                                <i class="bi bi-person-badge form-icon-premium"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-premium">
                                <label for="email" class="form-label-premium">Alamat Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control form-control-premium w-100" value="{{ old('email', $user->email) }}" required placeholder="admin@badminton.com">
                                <i class="bi bi-envelope form-icon-premium"></i>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-premium">
                                <label for="nomor_hp" class="form-label-premium">Nomor Telepon / WhatsApp</label>
                                <input type="text" name="nomor_hp" id="nomor_hp" class="form-control form-control-premium w-100" value="{{ old('nomor_hp', $user->nomor_hp) }}" placeholder="Contoh: 08123456789">
                                <i class="bi bi-whatsapp form-icon-premium"></i>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-premium">
                                <label for="alamat" class="form-label-premium">Alamat Domisili</label>
                                <input type="text" name="alamat" id="alamat" class="form-control form-control-premium w-100" value="{{ old('alamat', $user->alamat) }}" placeholder="Masukkan alamat lengkap">
                                <i class="bi bi-geo-alt form-icon-premium"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Bagian 2: Keamanan --}}
                    <div class="section-title-premium mt-4">
                        <i class="bi bi-shield-lock-fill"></i> Keamanan Akun
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-premium">
                                <label for="password" class="form-label-premium">Password Baru</label>
                                <input type="password" name="password" id="password" class="form-control form-control-premium w-100" placeholder="Kosongkan jika tidak ingin diubah">
                                <i class="bi bi-key form-icon-premium"></i>
                                <small class="text-muted" style="font-size: 0.72rem;">Minimal 6 karakter</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group-premium">
                                <label for="password_confirmation" class="form-label-premium">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-premium w-100" placeholder="Masukkan ulang password baru">
                                <i class="bi bi-key-fill form-icon-premium"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Button Submit --}}
                    <div class="text-end mt-4 pt-2">
                        <button type="submit" class="btn-save-premium">
                            <i class="bi bi-check-circle-fill"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fotoInput = document.getElementById('fotoProfilInput');
        const imgPreview = document.getElementById('profileImagePreview');
        const initialsPreview = document.getElementById('profileInitialsPreview');

        // Handler untuk live preview foto yang baru dipilih
        if (fotoInput) {
            fotoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Validasi ukuran gambar (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ukuran File Terlalu Besar',
                            text: 'Foto profil maksimal berukuran 2MB.',
                            customClass: {
                                popup: 'swal-premium',
                                confirmButton: 'btn btn-primary swal-btn-premium'
                            }
                        });
                        this.value = ''; // Reset input
                        return;
                    }

                    // Render preview gambar
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (imgPreview) {
                            imgPreview.src = e.target.result;
                            imgPreview.classList.remove('d-none');
                        }
                        if (initialsPreview) {
                            initialsPreview.classList.add('d-none');
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush
@endsection
