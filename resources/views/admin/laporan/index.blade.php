@extends('layouts.app')

@section('title', 'Laporan Keuangan & Booking')

@section('page_title', 'Laporan Keuangan')
@section('page_subtitle', 'Data transaksi bulan ' . $filter->translatedFormat('F Y'))
@section('topbar_actions')
    <a href="{{ route('admin.laporan.export.pdf', ['bulan' => $bulan]) }}" class="btn btn-sm btn-outline-danger">
        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
    </a>
    <a href="{{ route('admin.laporan.export.excel', ['bulan' => $bulan]) }}" class="btn btn-sm btn-outline-success">
        <i class="bi bi-file-earmark-excel me-1"></i>Export CSV
    </a>
@endsection

@section('content')
<div class="p-0">
            <div class="table-card mb-4">
                <div class="p-3">
                    <form method="GET" action="{{ route('admin.laporan.index') }}" class="d-flex align-items-end gap-3">
                        <div>
                            <label class="form-label small fw-600">Pilih Bulan</label>
                            <input type="month" name="bulan" class="form-control form-control-sm" value="{{ $bulan }}">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Filter Laporan</button>
                    </form>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card p-3 text-center" style="border-top: 4px solid #0ea5e9">
                        <div class="text-muted small">Total Booking</div>
                        <div class="fs-4 fw-bold text-dark">{{ $totalBookingBulan }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-3 text-center" style="border-top: 4px solid #10b981">
                        <div class="text-muted small">Total Pendapatan</div>
                        <div class="fs-4 fw-bold text-dark">Rp {{ number_format($totalPendapatanBulan,0,',','.') }}</div>
                        <div class="small mt-1 d-flex justify-content-center gap-2">
                            <span class="text-muted" title="Pendapatan Lapangan"><i class="bi bi-geo-alt me-1"></i>Rp{{ number_format($totalPendapatanLapangan,0,',','.') }}</span>
                            <span class="text-muted">|</span>
                            <span class="text-muted" title="Pendapatan Fasilitas"><i class="bi bi-box-seam me-1"></i>Rp{{ number_format($totalPendapatanFasilitas,0,',','.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-3 text-center" style="border-top: 4px solid #3b82f6">
                        <div class="text-muted small">Terbayar (Dipesan/Selesai)</div>
                        <div class="fs-4 fw-bold text-dark">{{ $bookingDikonfirmasi }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-3 text-center" style="border-top: 4px solid #ef4444">
                        <div class="text-muted small">Dibatalkan</div>
                        <div class="fs-4 fw-bold text-dark">{{ $bookingDibatalkan }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="table-card h-100">
                        <div class="table-card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">Grafik & Detail Pendapatan Harian</h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill"><i class="bi bi-graph-up me-1"></i>Bulan Ini</span>
                        </div>
                        <div class="p-4 border-bottom bg-light bg-opacity-50">
                            <div style="height: 280px; width: 100%;">
                                <canvas id="pendapatanChart"></canvas>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jml Transaksi</th>
                                        <th class="text-end">Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bookingPerHari as $tanggal => $bookings)
                                        @php
                                            $bookingsSukses = $bookings->whereIn('status', ['dipesan', 'selesai']);
                                            $totalHarian = $bookingsSukses->sum('total_harga');
                                        @endphp
                                        @if($totalHarian > 0)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}</td>
                                            <td>{{ $bookingsSukses->count() }} Transaksi Sukses</td>
                                            <td class="text-end fw-500">Rp {{ number_format($totalHarian,0,',','.') }}</td>
                                        </tr>
                                        @endif
                                    @empty
                                        <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada transaksi di bulan ini</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="table-card mb-4">
                        <div class="table-card-header">
                            <h6 class="mb-0 fw-bold">Lapangan Terpopuler</h6>
                        </div>
                        <div class="p-3">
                            @forelse($lapanganPopuler as $lp)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <div class="fw-500">{{ $lp->lapangan->nama_lapangan }}</div>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $lp->total }} booking</span>
                                </div>
                            @empty
                                <div class="text-center text-muted">Belum ada data</div>
                            @endforelse
                        </div>
                    </div>
                    
                    <div class="table-card h-100">
                        <div class="table-card-header">
                            <h6 class="mb-0 fw-bold">Penjualan Fasilitas Tambahan</h6>
                        </div>
                        <div class="p-3">
                            @forelse($fasilitasPopuler as $fp)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <div class="fw-500">{{ $fp->fasilitas->nama }}</div>
                                        <div class="text-muted small">Rp {{ number_format($fp->total_pendapatan,0,',','.') }}</div>
                                    </div>
                                    <span class="badge bg-success rounded-pill">{{ $fp->total_terjual }} terjual</span>
                                </div>
                            @empty
                                <div class="text-center text-muted">Belum ada penjualan fasilitas</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('pendapatanChart');
    if (!ctx) return;
    
    // Create soft gradient
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.25)'); // var(--primary) with opacity
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($pendapatanPerHari->map(function($item) { return \Carbon\Carbon::parse($item->tanggal_booking)->format('d M'); })) !!},
            datasets: [{
                label: 'Pendapatan Harian',
                data: {!! json_encode($pendapatanPerHari->pluck('total')) !!},
                borderColor: '#2563eb',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Make the line smooth/curved
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" },
                    bodyFont: { size: 14, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    border: { display: false },
                    ticks: {
                        color: '#64748b',
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                            if (value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    border: { display: false },
                    ticks: { color: '#64748b', font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 } }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
});
</script>
@endpush

