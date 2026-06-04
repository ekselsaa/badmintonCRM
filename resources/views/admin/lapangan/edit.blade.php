@extends('layouts.app')
@section('title', 'Edit Lapangan')
@section('page_title', 'Edit Lapangan')
@section('page_subtitle', 'Perbarui data lapangan')
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
                        <form action="{{ route('admin.lapangan.update', $lapangan->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="mb-3">
                                <label class="form-label fw-600 small">Nama Lapangan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lapangan" class="form-control @error('nama_lapangan') is-invalid @enderror"
                                    value="{{ old('nama_lapangan', $lapangan->nama_lapangan) }}" required>
                                @error('nama_lapangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600 small">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $lapangan->deskripsi) }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-600 small">Harga Senin-Jumat (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="harga_weekday" class="form-control"
                                            value="{{ old('harga_weekday', $lapangan->harga_weekday) }}" min="1000" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-600 small">Harga Sabtu & Minggu (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="harga_weekend" class="form-control"
                                            value="{{ old('harga_weekend', $lapangan->harga_weekend) }}" min="1000" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-600 small">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="aktif" {{ $lapangan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ $lapangan->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-save me-1"></i>Update Lapangan
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
