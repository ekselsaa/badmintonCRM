@extends('layouts.app')
@section('title', 'Kelola Fasilitas')
@section('page_title', 'Stok & Fasilitas Tambahan')
@section('page_subtitle', 'Manajemen inventaris seperti raket, kok, dan minuman')

@section('content')
<div class="row">
    <!-- Tabel Data Fasilitas -->
    <div class="col-md-8">
        <div class="table-card mb-4" style="border:none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-radius: 16px;">
            <div class="table-card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold" style="color: #1e293b;"><i class="bi bi-box-seam text-primary me-2"></i>Daftar Fasilitas</h5>
                <p class="text-muted small mt-1">Kelola stok dan harga fasilitas tambahan lapangan.</p>
            </div>
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 8px;">
                    <thead style="background-color: #f8fafc; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr class="align-middle">
                            <th class="border-0 rounded-start px-4 py-3 text-nowrap">Fasilitas</th>
                            <th class="border-0 py-3 text-nowrap">Harga</th>
                            <th class="border-0 py-3 text-center text-nowrap">Stok Real-Time</th>
                            <th class="border-0 py-3 text-center text-nowrap">Status</th>
                            <th class="border-0 rounded-end px-4 py-3 text-end text-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fasilitas as $f)
                            <tr style="background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s ease;">
                                <td class="border-0 px-4 py-3 rounded-start">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 45px; height: 45px; border-radius: 12px; background: linear-gradient(135deg, rgba(37,99,235,0.1), rgba(37,99,235,0.05)); display:flex; align-items:center; justify-content:center;">
                                            <i class="bi {{ $f->icon }} text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $f->nama }}</div>
                                            <div class="text-muted small">ID: #{{ str_pad($f->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-0 py-3 fw-semibold text-dark text-nowrap">
                                    Rp {{ number_format($f->harga, 0, ',', '.') }}
                                </td>
                                <td class="border-0 py-3 text-center">
                                    @if($f->stok > 0)
                                        <div class="d-inline-flex align-items-center justify-content-center fw-bold" style="background:#ecfdf5; color:#059669; padding: 6px 12px; border-radius: 20px; font-size:0.85rem; border: 1px solid #d1fae5;">
                                            <i class="bi bi-check-circle-fill me-1"></i> {{ $f->stok }} Pcs
                                        </div>
                                    @else
                                        <div class="d-inline-flex align-items-center justify-content-center fw-bold" style="background:#fef2f2; color:#dc2626; padding: 6px 12px; border-radius: 20px; font-size:0.85rem; border: 1px solid #fee2e2;">
                                            <i class="bi bi-x-circle-fill me-1"></i> Habis
                                        </div>
                                    @endif
                                </td>
                                <td class="border-0 py-3 text-center">
                                    @if($f->is_active)
                                        <span class="badge" style="background: linear-gradient(135deg, #3b82f6, #2563eb); font-weight: 500; padding: 6px 12px;">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary" style="font-weight: 500; padding: 6px 12px;">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="border-0 px-4 py-3 rounded-end">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0;" data-bs-toggle="modal" data-bs-target="#editModal{{ $f->id }}" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <form action="{{ route('admin.fasilitas.destroy', $f->id) }}" method="POST" class="d-inline mb-0">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle btn-delete" style="width: 32px; height: 32px; padding: 0;" title="Hapus" data-nama="{{ $f->nama }}">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>


                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-box-seam fs-1 d-block mb-3 text-secondary" style="opacity:0.5"></i>
                                        <span class="fw-semibold">Belum ada fasilitas.</span><br>
                                        <small>Tambahkan fasilitas baru pada form di samping.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modals Loop (Outside Table) -->
    @foreach($fasilitas as $f)
    <div class="modal fade" id="editModal{{ $f->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $f->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('admin.fasilitas.update', $f->id) }}" method="POST" class="modal-content shadow-lg" style="border:none; border-radius:16px; overflow:hidden;">
                @csrf @method('PUT')
                <div class="modal-header border-0 bg-light px-4 py-3" style="background: linear-gradient(to right, #f8fafc, #f1f5f9) !important;">
                    <h5 class="modal-title fw-bold" id="editModalLabel{{ $f->id }}" style="color: #1e293b;"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Fasilitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Nama Fasilitas</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-tag text-muted"></i></span>
                            <input type="text" name="nama" class="form-control border-start-0 ps-0" value="{{ $f->nama }}" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Harga Satuan (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">Rp</span>
                                <input type="number" name="harga" class="form-control border-start-0 ps-0 fw-bold" value="{{ $f->harga }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Stok Fisik</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-box text-muted"></i></span>
                                <input type="number" name="stok" class="form-control border-start-0 ps-0 fw-bold text-primary" value="{{ $f->stok }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 d-none">
                        <input type="hidden" name="icon" value="{{ $f->icon }}">
                    </div>
                    <div class="form-check form-switch p-3 rounded-3" style="background:#f8fafc; border: 1px solid #e2e8f0;">
                        <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" id="active{{ $f->id }}" {{ $f->is_active ? 'checked' : '' }} style="width: 2.5em; height: 1.25em; cursor:pointer;">
                        <label class="form-check-label fw-bold d-block" for="active{{ $f->id }}" style="cursor:pointer; padding-top:2px;">Tampilkan di Form Pelanggan</label>
                        <small class="text-muted">Jika dinonaktifkan, fasilitas ini tidak akan muncul di form pelanggan.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    <!-- Form Tambah Fasilitas -->
    <div class="col-md-4">
        <div class="stat-card" style="border:none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border-radius: 16px; background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); position: sticky; top: 20px;">
            <div class="d-flex align-items-center mb-4">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #e0e7ff; display:flex; align-items:center; justify-content:center; margin-right: 12px;">
                    <i class="bi bi-plus-lg text-primary fs-5"></i>
                </div>
                <h5 class="fw-bold mb-0" style="color: #1e293b;">Tambah Baru</h5>
            </div>
            
            <form action="{{ route('admin.fasilitas.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Nama Fasilitas</label>
                    <input type="text" name="nama" class="form-control bg-white" placeholder="Contoh: Sewa Raket" required style="border-radius: 8px;">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control bg-white fw-bold" placeholder="Contoh: 25000" required style="border-radius: 8px;">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Stok Awal</label>
                    <input type="number" name="stok" class="form-control bg-white fw-bold text-primary" value="0" required style="border-radius: 8px;">
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold mb-2">Pilih Ikon</label>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $icons = ['bi-bag', 'bi-record-circle', 'bi-box-seam', 'bi-droplet', 'bi-cup-straw', 'bi-bag-check', 'bi-tag', 'bi-gear'];
                        @endphp
                        @foreach($icons as $index => $icon)
                        <input type="radio" class="btn-check" name="icon" id="icon-{{ $icon }}" value="{{ $icon }}" {{ $index == 0 ? 'checked' : '' }} required>
                        <label class="btn btn-outline-primary d-flex align-items-center justify-content-center p-0" for="icon-{{ $icon }}" style="width: 42px; height: 42px; border-radius: 10px;">
                            <i class="bi {{ $icon }} fs-5"></i>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-check form-switch mb-4 p-3 rounded-3 bg-white" style="border: 1px solid #e2e8f0;">
                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_active" id="newActive" checked style="width: 2.5em; height: 1.25em; cursor:pointer;">
                    <label class="form-check-label fw-bold" for="newActive" style="cursor:pointer; padding-top:2px;">Aktifkan Langsung</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); border:none;">
                    <i class="bi bi-save me-2"></i>Simpan Fasilitas
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const namaFasilitas = this.getAttribute('data-nama');
            
            Swal.fire({
                title: 'Hapus Fasilitas?',
                text: `Apakah Anda yakin ingin menghapus fasilitas "${namaFasilitas}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Danger Red
                cancelButtonColor: '#6b7280', // Gray
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#fff',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
