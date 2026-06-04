@extends('layouts.app')
@section('title', 'Detail Booking #' . $booking->id)
@section('page_title', 'Detail Booking #' . $booking->id)
@section('page_subtitle', 'Status: ' . ucfirst($booking->status))
@section('topbar_actions')
    <a href="{{ route('admin.booking.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
<div class="p-0">
    <div class="row g-3">
        {{-- Kolom Kiri: Informasi Booking --}}
        <div class="col-lg-6">
            <div class="table-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Informasi Booking</h6>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid #f1f5f9">
                    <span class="text-muted small">Status Booking</span>
                    <span class="badge badge-{{ $booking->status }} px-3 py-2 rounded-pill">{{ ucfirst($booking->status) }}</span>
                </div>
                <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                    <span class="text-muted small">Lapangan</span>
                    <span class="fw-600">{{ $booking->lapangan->nama_lapangan ?? '-' }}</span>
                <