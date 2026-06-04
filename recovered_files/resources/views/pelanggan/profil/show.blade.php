@extends('layouts.app')

@section('title', $activeTab === 'loyalty' ? 'Loyalty Points Saya' : 'Profil Saya')

@section('page_title', $activeTab === 'loyalty' ? 'Loyalty Points' : 'Profil Saya')
@section('page_subtitle', $activeTab === 'loyalty' ? 'Kumpulkan poin sewa dan tukarkan dengan hadiah menarik' : 'Kelola informasi akun dan pantau aktivitas Anda')
@section('topbar_actions')
    <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm">
        <i class="bi bi-person-badge-fill me-1"></i>{{ strtoupper($user->kategori_member ?? 'non-member') }}
    </span>
@endsection

@section('content')
@php
    // Hitung persentase kelengkapan profil
    $completeness = 30; // default (nama terisi)
    $completeness += 30; // email terisi
    if ($user->nomor_hp) $completeness += 15;
    if ($user->alamat) $completeness += 15;
    if ($user->foto_profil) $completeness += 10;
@endphp

<style>
    /* ── Combined Premium Styling ── */
    .profile-sidebar-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .profile-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 2.5rem 1.5rem;
        position: relative;
        text-align: center;
        color: #fff;
    }

    .profile-hero::after {
        content: '';
      