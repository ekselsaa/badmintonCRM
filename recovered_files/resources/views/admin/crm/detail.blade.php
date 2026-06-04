@extends('layouts.app')
@section('title', 'Detail Pelanggan - ' . $pelanggan->name)
@section('page_title', 'Detail Pelanggan')
@section('page_subtitle', 'Riwayat lengkap: ' . $pelanggan->name)
@section('topbar_actions')
    <a href="{{ route('admin.crm.pelanggan') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
@endsection

@section('content')
<div class="p-0">
    <div class="row g-4 mb-4">
        {{-- Profil Pelanggan --}}
        <div class="col-lg-4">
            <div class="table-card p-4 p-md-5 text-center position-relative h-100">
                @if($pelanggan->kategori_member === 'member')
                    <span class="position-absolute top-0 end-0 m-3 badge" style="background:#fef3c7; color:#d97706; border:1px solid #fde68a;">
                        <i class="bi bi-star-fill me-1"></i>Member
                    </span>
                @else
                    <span class="position-absolute top-0 end-0 m-3 badge bg-light text-secondary border">
                        Non-Member
                    </span>
                @endif

                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 mt-2"
                     style="width:72px;height:72px;background:linear-gradient(135deg,#1a56db,#0ea5e9);color:#fff;font-size:1.8rem;font-weight:700">
                    {{ strtoupper(substr($pelanggan->name, 0, 1)) }}
        