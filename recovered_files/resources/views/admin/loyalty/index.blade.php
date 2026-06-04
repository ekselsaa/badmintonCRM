@extends('layouts.app')

@section('title', 'CRM - Loyalty Points')
@section('page_title', 'CRM - Loyalty & Rewards')

@section('content')
<style>
    /* ── Enhanced Stat Cards ── */
    .loyalty-stat {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform .25s ease, box-shadow .25s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .loyalty-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
    }
    .loyalty-stat::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 5px;
        border-radius: 5px 0 0 5px;
    }
    .loyalty-stat.blue::before   { background: #3b82f6; }
    .loyalty-stat.indigo::before { background: #6366f1; }
    .loyalty-stat.emerald::before{ background: #10b981; }
    .loyalty-stat.violet::before { background: #8b5cf6; }

    .loyalty-stat .stat-num {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -1px;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }
    .loyalty-stat .stat-sub {
        font-size: .82rem;
        color: #6