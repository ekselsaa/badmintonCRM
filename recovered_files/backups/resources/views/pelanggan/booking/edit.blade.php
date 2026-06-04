@extends('layouts.app')
@section('title', 'Edit Booking')
@section('page_title', 'Edit Booking')
@section('page_subtitle', 'Sesuaikan jadwal atau fasilitas pesanan Anda')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

body, select, input, textarea, button, h6, label {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

.table-card {
    border-radius: 16px !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05) !important;
}

.form-select, .form-control {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 10px !important;
    font-size: .9rem !important;
    padding: 10px 14px !important;
    background: #f8fafc !important;
    transition: all 0.2s ease;
}

.form-select:focus, .form-control:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59,130,246,.12) !important;
    background:#fff !important;
}

/* Reward discount line in total box */
.discount-row {
    display: flex; justify-content: space-between; align-items:center;
    background: linear-gradient(135deg, rgba(16,185,129,.1), rgba(52,211,153,.05));
    border: 1px solid rgba(16,185,129,.25);
    border-radius: 10px;
    padding: 6px 12px;
    margin-top: 6px;
    font-size: .72rem; font-weight: 700;
    color: #059669;
}
.reward-indicator-badge {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: .8rem;
    font-weight: 700;
    margin-bottom: 15px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}
</style>
@endpush

@section('content')
<div class="p-0">
            <div class="container mt-4">
                <div class="row g-3">
                    {{-- Info --}}
                    <div class="col-12 mb-3">
                        <div class="table-card p-4" style="background:#fffbeb;border-color:#fde68a">
                            <h6 class="fw-bold mb-2" style="color:#92400e"><i class="bi bi-info-circle me-2"></i>Info Booking</h6>
                            <div class="d-flex flex-wrap gap-4 small" style="color:#92400e">
                                <span><i class="bi bi-1-circle me-1"></i>Pilih lapangan & tanggal</span>
                                <span><i class="bi bi-2-circle me-1"></i>Pilih jam booking</span>
                                <span><i class="bi bi-3-circle me-1"></i>Upload bukti bayar</span>
                                <span><i class="bi bi-4-circle me-1"></i>Tunggu konfirmasi</span>
                            </div>
                        </div>
                    </div>

                <div class="col-lg-7">
                    @if($booking->reward_applied)
                        <div class="reward-indicator-badge">
                            <i class="bi bi-gift-fill text-warning"></i>
                            <span>Booking ini menggunakan promo gratis 1 jam dari program Loyalty Reward!</span>
                        </div>
                    @endif

                    <div class="table-card p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Edit Waktu Booking</h6>
                        <form id="formBookingCreate" action="{{ route('booking.update', $booking->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="mb-3">
                                <label class="form-label small fw-600">Pilih Lapangan</label>
                                <select name="lapangan_id" id="selectLapanganEdit" class="form-select" required onchange="updateHargaBadgeEdit()">
                                    <option value="">-- Pilih Lapangan --</option>
                                    @foreach($lapangans as $l)
                                    <option value="{{ $l->id }}" 
                                            data-weekday="{{ $l->harga_weekday }}" 
                                            data-weekend="{{ $l->harga_weekend }}"
                                            {{ old('lapangan_id', $booking->lapangan_id) == $l->id ? 'selected' : '' }}>
                                        {{ $l->nama_lapangan }}
                                    </option>
                                    @endforeach
                                </select>
                                {{-- Badge harga aktif --}}
                                <div id="hargaBadgeEdit" class="mt-2 d-none">
                                    <span id="hargaBadgeLabelEdit" class="badge rounded-pill px-3 py-2" style="font-size:0.82rem;"></span>
                                    <span class="text-muted ms-2" style="font-size:0.78rem;" id="hargaBadgeSubEdit"></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-600">Pilih Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $booking->tanggal_booking->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required onchange="updateHargaBadgeEdit(); disablePastHours(); hitungTotal();">
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label small fw-600">Jam Mulai</label>
                                    <select name="jam_mulai" class="form-select" required>
                                        <option value="">-- Jam Mulai --</option>
                                        @php $times = ['07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00']; @endphp
                                        @foreach($times as $t)
                                        <option value="{{ $t }}" {{ old('jam_mulai', \Carbon\Carbon::parse($booking->jadwal->jam_mulai)->format('H:i')) == $t ? 'selected' : '' }}>{{ $t }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-600">Jam Selesai</label>
                                    <select name="jam_selesai" class="form-select" required>
                                        <option value="">-- Jam Selesai --</option>
                                        @php $timesEnd = ['07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00','23:59']; @endphp
                                        @foreach($timesEnd as $t)
                                        <option value="{{ $t }}" {{ old('jam_selesai', $booking->jadwal->jam_selesai == '23:59:00' ? '23:59' : \Carbon\Carbon::parse($booking->jadwal->jam_selesai)->format('H:i')) == $t ? 'selected' : '' }}>{{ $t }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-600">Pilih Metode Pembayaran</label>
                                <select name="metode_pembayaran" class="form-select" required>
                                    <option value="qris" {{ old('metode_pembayaran', $booking->pembayaran->metode_pembayaran ?? 'qris') == 'qris' ? 'selected' : '' }}>QRIS</option>
                                    <option value="tunai" {{ old('metode_pembayaran', $booking->pembayaran->metode_pembayaran ?? 'qris') == 'tunai' ? 'selected' : '' }}>Bayar di Tempat (Tunai)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-600">Catatan Tambahan (Opsional)</label>
                                <textarea name="catatan" class="form-control" rows="2" placeholder="Cth: Pinjam raket, dll">{{ old('catatan', $booking->catatan) }}</textarea>
                            </div>

                            {{-- Fasilitas Tambahan - Collapsible --}}
                            <div class="mb-4">
                                <button class="btn btn-outline-secondary btn-sm w-100 rounded-3 mb-2 d-flex justify-content-between align-items-center" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseFasilitas">
                                    <span class="fw-bold"><i class="bi bi-plus-circle me-1"></i>Tambah Raket / Kok (Opsional)</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>

                                <div class="collapse" id="collapseFasilitas">
                                    <div class="d-flex flex-column gap-2 mt-2">
                                        @foreach($fasilitas_list as $f)
                                        @php
                                            $val = 0;
                                            if(isset($booking)) {
                                                $arr = explode(', ', $booking->fasilitas);
                                                foreach($arr as $item) {
                                                    if(str_contains($item, $f->nama)) {
                                                        $val = (int) filter_var($item, FILTER_SANITIZE_NUMBER_INT);
                                                    }
                                                }
                                            }
                                        @endphp
                                        <div class="d-flex flex-column p-2 rounded-3 border bg-white shadow-sm mb-2" style="font-size: 0.85rem;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi {{ $f->icon }} text-primary"></i>
                                                    <span>{{ $f->nama }} (+Rp {{ number_format($f->harga, 0, ',', '.') }})
                                                        <span id="badge-stok-{{ $f->id }}">
                                                            @if($f->stok > 0 || $val > 0)
                                                                <span class="badge bg-light text-primary ms-1" style="font-size:0.6rem; border:1px solid #bfdbfe;">Sisa: {{ $f->stok }}</span>
                                                            @else
                                                                <span class="badge bg-light text-danger ms-1" style="font-size:0.6rem; border:1px solid #fecaca;">Habis</span>
                                                            @endif
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="input-group input-group-sm" style="width: 100px;">
                                                    <button class="btn btn-outline-secondary btn-minus" type="button" id="btn-minus-{{ $f->id }}" {{ $val == 0 ? 'disabled' : '' }}>-</button>
                                                    <input type="text" name="fasilitas[{{ $f->id }}]" class="form-control text-center qty-input" 
                                                           id="qty-input-{{ $f->id }}"
                                                           data-id="{{ $f->id }}" 
                                                           data-harga="{{ $f->harga }}" 
                                                           data-nama="{{ $f->nama }}"
                                                           data-max="{{ $f->stok + $val }}"
                                                           value="{{ $val }}" readonly>
                                                    <button class="btn btn-outline-secondary btn-plus" type="button" id="btn-plus-{{ $f->id }}">+</button>
                                                </div>
                                            </div>
                                            <div id="info-stok-{{ $f->id }}" class="small mt-1 text-muted d-none" style="font-size:0.75rem; margin-left: 24px;"></div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Display Total Harga --}}
                            <div class="p-3 rounded-3 mb-4 d-none" id="containerTotal" style="background:#f0f7ff; border: 1px dashed #0ea5e9;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-600 text-muted">Estimasi Total Bayar:</span>
                                    <span class="h5 fw-bold text-primary mb-0" id="displayTotal">Rp 0</span>
                                </div>
                                <div class="text-muted" style="font-size: .7rem;" id="detailHitung"></div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle me-2"></i>Konfirmasi & Lanjutkan Pembayaran
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Kolom Info Jadwal Terisi --}}
                <div class="col-lg-5" id="occupiedSchedulesCard">
                    <div class="table-card p-4">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-info-circle me-2 text-danger"></i>
                            Jadwal Terisi pada {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                        </h6>
                        <p class="text-muted small mb-4">Harap hindari memilih jam yang bertabrakan dengan jadwal di bawah ini.</p>

                        <div id="occupiedSchedulesList">
                            @if($jadwals->count() > 0)
                            <div class="row g-3">
                                @foreach($jadwals as $j)
                                <div class="col-sm-6 col-lg-12 col-xl-6">
                                    <div class="stat-card" style="opacity: 0.85; background-color: #f8fafc; border: 1px solid {{ $j->status === 'pending' ? '#fde68a' : '#fecaca' }};">
                                        <div class="d-flex align-items-start justify-content-between mb-2">
                                            <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">{{ $j->lapangan->nama_lapangan }}</h6>
                                            @if($j->status === 'pending')
                                                <span class="badge" style="background:#fef3c7;color:#92400e; font-size: 0.65rem;">Pending</span>
                                            @else
                                                <span class="badge bg-danger" style="font-size: 0.65rem;">Dipesan</span>
                                            @endif
                                        </div>
                                        <div class="fw-bold {{ $j->status === 'pending' ? 'text-warning' : 'text-danger' }} mt-2" style="font-size:1rem">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="p-5 text-center" style="border: 2px dashed #cbd5e1; border-radius: 16px;">
                                <i class="bi bi-calendar2-check fs-1 text-success d-block mb-3"></i>
                                <h6 class="text-success fw-bold">Semua Jadwal Kosong!</h6>
                                <p class="text-muted small mb-0">Belum ada lapangan yang dibooking pada tanggal ini.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const isRewardApplied = {{ $booking->reward_applied ? 'true' : 'false' }};

// Fungsi Kalkulasi Harga
function hitungTotal() {
    const lapangan = document.querySelector('select[name="lapangan_id"]');
    const tanggal = document.querySelector('input[name="tanggal"]');
    const jamMulai = document.querySelector('select[name="jam_mulai"]');
    const jamSelesai = document.querySelector('select[name="jam_selesai"]');
    const container = document.getElementById('containerTotal');
    const display = document.getElementById('displayTotal');
    const detail = document.getElementById('detailHitung');

    if (lapangan.value && tanggal.value && jamMulai.value && jamSelesai.value) {
        const option = lapangan.options[lapangan.selectedIndex];
        const date = new Date(tanggal.value);
        const isWeekend = date.getDay() === 0 || date.getDay() === 6;
        const hargaPerJam = isWeekend ? parseInt(option.dataset.weekend) : parseInt(option.dataset.weekday);
        
        let mulai = parseInt(jamMulai.value.split(':')[0]);
        let selesai = parseInt(jamSelesai.value.split(':')[0]);
        if (jamSelesai.value.endsWith('59')) {
            selesai += 1;
        }
        
        if (selesai > mulai) {
            const durasi = selesai - mulai;
            let total = durasi * hargaPerJam;
            let parts = [`${durasi} Jam × Rp ${hargaPerJam.toLocaleString('id-ID')}`];

            // Hitung Fasilitas
            const inputs = document.querySelectorAll('.qty-input');
            inputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                if(qty > 0) {
                    const harga = parseInt(input.dataset.harga);
                    const nama = input.dataset.nama;
                    total += (qty * harga);
                    parts.push(`${nama}×${qty}`);
                }
            });

            // ── Loyalty Reward Discount ──
            let discountHtml = '';
            if (isRewardApplied) {
                const diskon = hargaPerJam;
                total = Math.max(0, total - diskon);
                discountHtml = `
                    <div class="discount-row mt-2">
                        <span><i class="bi bi-gift-fill me-1"></i>Reward: Gratis 1 Jam Lapangan</span>
                        <span>- Rp ${diskon.toLocaleString('id-ID')}</span>
                    </div>`;
            }
            
            container.classList.remove('d-none');
            display.innerText = 'Rp ' + total.toLocaleString('id-ID');
            detail.innerHTML = `<span style="opacity:.8">${parts.join(' + ')}</span>` + discountHtml;
        } else {
            container.classList.add('d-none');
        }
    } else {
        container.classList.add('d-none');
    }
}

// Data Awal Booking (Untuk disable jam lampau)
const initialTanggal = "{{ $booking->tanggal_booking->format('Y-m-d') }}";
const initialJamMulai = "{{ \Carbon\Carbon::parse($booking->jadwal->jam_mulai)->format('H:i') }}";
const initialJamSelesai = "{{ $booking->jadwal->jam_selesai == '23:59:00' ? '23:59' : \Carbon\Carbon::parse($booking->jadwal->jam_selesai)->format('H:i') }}";

// Fungsi Nonaktifkan Jam Lampau
function disablePastHours() {
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    if (!tanggalInput) return;

    const dateVal = tanggalInput.value;
    const today = new Date();
    // YYYY-MM-DD format (local time)
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayStr = `${yyyy}-${mm}-${dd}`;
    
    const isToday = (dateVal === todayStr);
    const isPast = (dateVal < todayStr);
    const currentHour = today.getHours();

    document.querySelectorAll('select[name="jam_mulai"] option, select[name="jam_selesai"] option').forEach(opt => {
        if (!opt.value) return;
        const optHour = parseInt(opt.value.split(':')[0]);
        
        // Cek apakah ini opsi awal yang dibooking
        const isInitialOption = (dateVal === initialTanggal && 
            ((opt.parentElement.name === 'jam_mulai' && opt.value === initialJamMulai) || 
             (opt.parentElement.name === 'jam_selesai' && opt.value === initialJamSelesai)));

        if ((isPast && !isInitialOption) || (isToday && optHour <= currentHour && !isInitialOption)) {
            opt.disabled = true;
            // Jika option yang terpilih ter-disable, reset selectnya
            if (opt.selected) {
                opt.parentElement.value = '';
            }
        } else {
            opt.disabled = false;
        }
    });
}

// Badge harga aktif berdasarkan tanggal (edit)
function updateHargaBadgeEdit() {
    const sel = document.getElementById('selectLapanganEdit');
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    const badgeWrap = document.getElementById('hargaBadgeEdit');
    const badgeLabel = document.getElementById('hargaBadgeLabelEdit');
    const badgeSub = document.getElementById('hargaBadgeSubEdit');

    if (!sel || !sel.value || !tanggalInput || !tanggalInput.value) {
        if (badgeWrap) badgeWrap.classList.add('d-none');
        return;
    }

    const opt = sel.options[sel.selectedIndex];
    const hargaWeekday = parseInt(opt.dataset.weekday);
    const hargaWeekend = parseInt(opt.dataset.weekend);
    const date = new Date(tanggalInput.value);
    const day = date.getDay();
    const isWeekend = (day === 0 || day === 6);

    const harga = isWeekend ? hargaWeekend : hargaWeekday;
    const tipe = isWeekend ? 'Weekend' : 'Weekday';
    const warna = isWeekend ? '#7c3aed' : '#0ea5e9';
    const bgWarna = isWeekend ? '#ede9fe' : '#e0f2fe';

    badgeLabel.textContent = 'Rp ' + harga.toLocaleString('id-ID') + ' / jam';
    badgeLabel.style.background = bgWarna;
    badgeLabel.style.color = warna;
    badgeSub.textContent = '(' + tipe + ' · ' + opt.text.trim() + ')';
    badgeWrap.classList.remove('d-none');
}

// Event Listeners
document.querySelectorAll('select, input').forEach(el => {
    el.addEventListener('change', function() {
        if (this.name === 'tanggal') {
            disablePastHours();
            updateHargaBadgeEdit();
            fetchOccupiedSchedules();
        }
        if (this.name === 'lapangan_id') {
            updateHargaBadgeEdit();
            fetchOccupiedSchedules();
        }
        if (this.name === 'tanggal' || this.name === 'jam_mulai' || this.name === 'jam_selesai') {
            updateFasilitasAvailability();
        }
        hitungTotal();
    });
    el.addEventListener('keyup', hitungTotal);
});

// Plus Minus Buttons logic
document.querySelectorAll('.btn-plus').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.closest('.input-group').querySelector('.qty-input');
        const max = parseInt(input.dataset.max) || 0;
        const currentVal = parseInt(input.value) || 0;
        
        if (currentVal < max) {
            input.value = currentVal + 1;
            hitungTotal();
            const btnMinus = this.closest('.input-group').querySelector('.btn-minus');
            if (btnMinus) btnMinus.disabled = false;
        } else {
            const nama = input.dataset.nama;
            const infoEl = document.getElementById(`info-stok-${input.dataset.id}`);
            let infoMsg = '';
            if (infoEl && !infoEl.classList.contains('d-none')) {
                infoMsg = `<br><span class="text-warning fw-bold">${infoEl.innerHTML}</span>`;
            }
            
            Swal.fire({
                title: `Stok ${nama} Terbatas`,
                html: `Maaf, jumlah ${nama} yang tersedia saat ini adalah ${max} unit untuk slot waktu yang Anda pilih.${infoMsg}`,
                icon: 'warning',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Baik, Saya Mengerti',
                customClass: {
                    popup: 'rounded-4'
                }
            });
        }
    });
});

document.querySelectorAll('.btn-minus').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.closest('.input-group').querySelector('.qty-input');
        const currentVal = parseInt(input.value) || 0;
        if (currentVal > 0) {
            input.value = currentVal - 1;
            hitungTotal();
            if (parseInt(input.value) === 0) {
                this.disabled = true;
            }
        }
    });
});

// Fungsi AJAX Ketersediaan Fasilitas
function updateFasilitasAvailability() {
    const tanggal = document.querySelector('input[name="tanggal"]').value;
    const jamMulai = document.querySelector('select[name="jam_mulai"]').value;
    const jamSelesai = document.querySelector('select[name="jam_selesai"]').value;
    const bookingId = "{{ $booking->id }}";

    if (!tanggal || !jamMulai || !jamSelesai) {
        return;
    }

    const url = `{{ route('booking.cek-fasilitas') }}?tanggal=${tanggal}&jam_mulai=${jamMulai}&jam_selesai=${jamSelesai}&booking_id=${bookingId}`;

    fetch(url)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                Object.values(res.data).forEach(f => {
                    const badge = document.getElementById(`badge-stok-${f.id}`);
                    const btnMinus = document.getElementById(`btn-minus-${f.id}`);
                    const btnPlus = document.getElementById(`btn-plus-${f.id}`);
                    const input = document.getElementById(`qty-input-${f.id}`);
                    const info = document.getElementById(`info-stok-${f.id}`);

                    if (badge) {
                        if (f.sisa_stok > 0) {
                            badge.innerHTML = `<span class="badge bg-light text-primary ms-1" style="font-size:0.6rem; border:1px solid #bfdbfe;">Sisa: ${f.sisa_stok}</span>`;
                        } else {
                            badge.innerHTML = `<span class="badge bg-light text-danger ms-1" style="font-size:0.6rem; border:1px solid #fecaca;">Habis</span>`;
                        }
                    }

                    if (input) {
                        input.dataset.max = f.sisa_stok;
                        if (parseInt(input.value) > f.sisa_stok) {
                            input.value = f.sisa_stok;
                        }
                    }

                    if (btnMinus && btnPlus) {
                        btnMinus.disabled = (parseInt(input.value) === 0);
                        btnPlus.disabled = false;
                    }

                    if (info) {
                        if (f.tersedia_pada) {
                            info.innerHTML = `<i class="bi bi-info-circle me-1 text-warning"></i>Akan tersedia pada jam ${f.tersedia_pada}`;
                            info.classList.remove('d-none');
                        } else {
                            info.innerHTML = '';
                            info.classList.add('d-none');
                        }
                    }
                });

                hitungTotal();
            }
        })
        .catch(err => console.error("Gagal mengambil status ketersediaan fasilitas:", err));
}

let pollIntervalId = null;

function fetchOccupiedSchedules(isSilent = false) {
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    const lapanganSelect = document.querySelector('select[name="lapangan_id"]');
    
    if (!tanggalInput || !lapanganSelect) return;
    
    const tgl = tanggalInput.value;
    const lap = lapanganSelect.value;

    const url = `{{ route('booking.edit', $booking->id) }}?tanggal=${tgl}&lapangan_id=${lap}`;

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            rebuildOccupiedSchedulesList(res.jadwals);
            
            const header = document.querySelector('#occupiedSchedulesCard h6');
            if (header) {
                header.innerHTML = `<i class="bi bi-info-circle me-2 text-danger"></i>Jadwal Terisi pada ${res.formatted_tanggal}`;
            }
        }
    })
    .catch(err => console.error("Gagal mengambil jadwal terisi:", err));
}

function rebuildOccupiedSchedulesList(jadwals) {
    const container = document.getElementById('occupiedSchedulesList');
    if (!container) return;

    if (jadwals.length > 0) {
        let html = '<div class="row g-3">';
        jadwals.forEach(j => {
            const isPending = j.status === 'pending';
            const borderWarna = isPending ? '#fde68a' : '#fecaca';
            const textWarna = isPending ? 'text-warning' : 'text-danger';
            const labelText = isPending ? 'Pending' : (j.keterangan ? j.keterangan : 'Dipesan');
            const badgeHtml = isPending 
                ? `<span class="badge" style="background:#fef3c7;color:#92400e; font-size: 0.65rem;">${labelText}</span>`
                : `<span class="badge bg-danger" style="font-size: 0.65rem;">${labelText}</span>`;
            
            const formatTime = (timeStr) => {
                if (!timeStr) return '';
                const parts = timeStr.split(':');
                return parts.length >= 2 ? `${parts[0]}:${parts[1]}` : timeStr;
            };

            const jamMulai = formatTime(j.jam_mulai);
            const jamSelesai = formatTime(j.jam_selesai);

            html += `
                <div class="col-sm-6 col-lg-12 col-xl-6">
                    <div class="stat-card" style="opacity: 0.85; background-color: #f8fafc; border: 1px solid ${borderWarna};">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">${j.lapangan ? j.lapangan.nama_lapangan : 'Lapangan'}</h6>
                            ${badgeHtml}
                        </div>
                        <div class="fw-bold ${textWarna} mt-2" style="font-size:1rem">
                            <i class="bi bi-clock me-1"></i>
                            ${jamMulai} - ${jamSelesai}
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    } else {
        container.innerHTML = `
            <div class="p-5 text-center" style="border: 2px dashed #cbd5e1; border-radius: 16px;">
                <i class="bi bi-calendar2-check fs-1 text-success d-block mb-3"></i>
                <h6 class="text-success fw-bold">Semua Jadwal Kosong!</h6>
                <p class="text-muted small mb-0">Belum ada lapangan yang dibooking pada tanggal ini.</p>
            </div>
        `;
    }
}

function startPollingEdit() {
    if (pollIntervalId) {
        clearInterval(pollIntervalId);
    }
    pollIntervalId = setInterval(() => {
        fetchOccupiedSchedules(true);
    }, 10000);
}

// Jalankan saat load
window.addEventListener('DOMContentLoaded', function() {
    disablePastHours();
    updateHargaBadgeEdit();
    updateFasilitasAvailability();
    hitungTotal();
    startPollingEdit();
});

// Logic khusus untuk paksa submit filter
const btnFilter = document.querySelector('#filterFormCreate button[type="submit"]');
if(btnFilter) {
    btnFilter.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('filterFormCreate').submit();
    });
}

document.getElementById('formBookingCreate').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Konfirmasi Booking',
        text: "Apakah Anda yakin jadwal, lapangan, dan jam sudah sesuai?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1a56db',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Cek Lagi',
        background: '#fff',
        customClass: {
            popup: 'rounded-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formBookingCreate').submit();
        }
    });
});

// Agar tombol logout bisa langsung diklik tanpa dialog "Leave page?"
document.querySelectorAll('.btn-logout, .btn-logout-icon').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        // Reset form booking agar browser tidak menganggap ada perubahan
        var bookingForm = document.getElementById('formBookingCreate');
        if (bookingForm) { bookingForm.reset(); }
        // Nonaktifkan peringatan "Leave page?" jika ada
        window.onbeforeunload = null;
    });
});
</script>
@endpush
