@extends('layouts.app')
@section('title', 'Informasi Membership')
@section('page_title', 'Informasi Membership')
@section('page_subtitle', 'Bergabung menjadi member Anbiyaa Sport')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, select, input, textarea, button, h2, h5, h6, label {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* Styling Premium untuk Formulir Membership */
    .table-card {
        border-radius: 20px !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05) !important;
    }
    .membership-card-option {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .membership-card-option:hover {
        transform: translateY(-4px);
        border-color: #cbd5e1;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .membership-card-option.active {
        border-color: #2563eb;
        background: #f8fafc;
        box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.15);
    }
    .membership-card-option.active::after {
        content: "\F272"; /* bi-chec