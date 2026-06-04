@extends('layouts.app')
@section('title', 'Tambah Lapangan')
@section('page_title', 'Tambah Lapangan')
@section('page_subtitle', 'Buat data lapangan baru')
@section('topbar_actions')
    <a href="{{ route('admin.lapangan.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
@endsection

@section('content')
<div class="p-0">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="table-card p-4">
                        <form action="{{ route('admin.lapangan.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-600 small">Nama Lapangan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lapangan" class="form-control @error('nama_lapangan') is-invalid @enderror"
                                    placeholder="cth: Lapangan A" value="{{ old('nama_lapangan') }}" required>
                                @error('nama_lapangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600 small">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3"
                                    placeholder="Deskripsi singkat lapangan">{{ old('deskripsi') }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-600 small">Harga Senin-Jumat (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="harga_weekday" class="form-control @error('harga_weekday') is-invalid @enderror"
                                            placeholder="55000" value="{{ old('harga_weekday') }}" min="1000" required>
                                    </div>
                                    @error('harga_weekday')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-600 small">Harga Sabtu & Minggu (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="harga_weekend" class="form-control @error('harga_weekend') is-invalid @enderror"
                                            placeholder="60000" value="{{ old('harga_weekend') }}" min="1000" required>
                                    </div>
                                    @error('harga_weekend')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-600 small">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-save me-1"></i>Simpan Lapangan
                                </button>
                                <a href="{{ route('admin.lapangan.index') }}" class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
