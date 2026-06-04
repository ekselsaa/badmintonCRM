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