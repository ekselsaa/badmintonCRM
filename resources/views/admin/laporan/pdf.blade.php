<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan GOR Anbiyaa</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.5;
            color: #1e293b;
            background-color: #ffffff;
        }

        /* Header / Letterhead */
        .letterhead-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 3px double #cbd5e1;
            padding-bottom: 12px;
        }
        .letterhead-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .brand-title {
            font-size: 18px;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: -0.5px;
            margin: 0 0 4px 0;
        }
        .brand-subtitle {
            font-size: 9px;
            color: #64748b;
            margin: 0;
            line-height: 1.4;
        }
        .doc-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            text-align: right;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .doc-meta {
            font-size: 9px;
            color: #475569;
            text-align: right;
            line-height: 1.4;
        }

        /* Summary Cards Table */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table td {
            border: none;
            padding: 0 5px;
        }
        .summary-table td:first-child {
            padding-left: 0;
        }
        .summary-table td:last-child {
            padding-right: 0;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: left;
        }
        .summary-card.highlight {
            background-color: #eff6ff;
            border-color: #bfdbfe;
        }
        .card-label {
            font-size: 8px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .card-value {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }
        .summary-card.highlight .card-value {
            color: #2563eb;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.5px;
            border: 1px solid #0f172a;
            padding: 6px 8px;
        }
        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        
        /* Utility styles */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #64748b; }
        
        .status-badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 3px;
        }
        .status-dipesan {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        .status-selesai {
            background-color: #dcfce7;
            color: #15803d;
        }

        /* Signature block */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        .footer-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .signature-title {
            font-size: 9px;
            color: #475569;
            margin-bottom: 40px;
        }
        .signature-name {
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            border-bottom: 1px solid #475569;
            display: inline-block;
            padding-bottom: 2px;
        }

        /* Page Number Footer */
        .page-footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 20px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 5px;
        }
        .page-footer .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    @php
        $totalFasilitas = 0;
        foreach($bookings as $b) {
            $totalFasilitas += $b->bookingFasilitas->sum('subtotal');
        }
        $totalLapangan = $totalPendapatan - $totalFasilitas;
    @endphp

    <!-- Page Number Footer -->
    <div class="page-footer">
        Laporan Keuangan GOR Anbiyaa • Halaman <span class="page-number"></span>
    </div>

    <!-- Letterhead -->
    <table class="letterhead-table">
        <tr>
            <td width="60%">
                <h1 class="brand-title">GOR ANBIYAA SPORT</h1>
                <p class="brand-subtitle">
                    Jl. Berua Raya, Daya, Kec. Biringkanaya, Kota Makassar, Sulawesi Selatan<br>
                    Telp: 0821-8748-5422
                </p>
            </td>
            <td width="40%">
                <h2 class="doc-title">Laporan Keuangan</h2>
                <p class="doc-meta">
                    <strong>Periode:</strong> {{ $filter->translatedFormat('F Y') }}<br>
                    <strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y') }}<br>
                    <strong>Status:</strong> Terverifikasi & Selesai
                </p>
            </td>
        </tr>
    </table>

    <!-- Summary Cards -->
    <table class="summary-table">
        <tr>
            <td width="25%">
                <div class="summary-card">
                    <div class="card-label">Total Transaksi</div>
                    <div class="card-value">{{ $bookings->count() }} Booking</div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="card-label">Pendapatan Lapangan</div>
                    <div class="card-value">Rp {{ number_format($totalLapangan, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="card-label">Pendapatan Fasilitas</div>
                    <div class="card-value">Rp {{ number_format($totalFasilitas, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card highlight">
                    <div class="card-label">Total Pendapatan</div>
                    <div class="card-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th width="12%">Tanggal</th>
                <th width="20%">Pelanggan</th>
                <th width="12%">Lapangan</th>
                <th width="12%">Jam</th>
                <th width="20%">Fasilitas Tambahan</th>
                <th width="10%" class="text-center">Status</th>
                <th width="10%" class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $i => $b)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $b->tanggal_booking->format('d/m/Y') }}</td>
                    <td class="fw-bold">{{ $b->nama_pemesan }}</td>
                    <td>{{ $b->lapangan->nama_lapangan ?? '-' }}</td>
                    <td>{{ $b->jadwal ? $b->jadwal->jam_mulai . ' - ' . $b->jadwal->jam_selesai : '-' }}</td>
                    <td class="text-muted">{{ $b->fasilitas ?: 'Tidak ada' }}</td>
                    <td class="text-center">
                        <span class="status-badge status-{{ $b->status }}">
                            {{ $b->status }}
                        </span>
                    </td>
                    <td class="text-right fw-bold">{{ number_format($b->total_harga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding: 15px;">
                        Tidak ada data transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bottom Signature -->
    <table class="footer-table" style="page-break-inside: avoid;">
        <tr>
            <td width="60%">
                <div style="font-size: 8px; color: #64748b; line-height: 1.4; padding-right: 40px;">
                    <strong>Catatan:</strong><br>
                    Laporan ini dibuat otomatis oleh sistem Badminton CRM GOR Anbiyaa. Seluruh transaksi yang tercantum di atas adalah transaksi resmi yang telah terverifikasi oleh admin.
                </div>
            </td>
            <td width="40%" class="text-right">
                <div class="signature-title">
                    Makassar, {{ now()->translatedFormat('d F Y') }}<br>
                    <strong>Admin GOR Anbiyaa</strong>
                </div>
                <div style="height: 40px;"></div>
                <div>
                    <span class="signature-name">{{ auth()->user()->name ?? 'Administrator' }}</span>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
