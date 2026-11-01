<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengaduan Kos</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563EB;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            color: #1E293B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 0;
            color: #64748B;
            font-size: 11px;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .meta-info td {
            font-size: 10px;
            padding: 3px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #CBD5E1;
            padding: 7px 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #F1F5F9;
            color: #1E293B;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.data-table tr:nth-child(even) {
            background-color: #F8FAFC;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-pending { background-color: #FEF3C7; color: #92400E; }
        .badge-in_progress { background-color: #E0F2FE; color: #0369A1; }
        .badge-completed { background-color: #D1FAE5; color: #065F46; }
        .badge-cancelled { background-color: #FEE2E2; color: #991B1B; }
        .badge-high { background-color: #FEE2E2; color: #991B1B; }
        .badge-medium { background-color: #FEF3C7; color: #92400E; }
        .badge-low { background-color: #F1F5F9; color: #475569; }

        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .signature {
            float: right;
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rekapitulasi Laporan Pengaduan Penghuni Kos</h1>
        <p>Sistem Informasi Pelaporan dan Pengelolaan Keluhan Kos</p>
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 50%;"><strong>Dicetak Pada:</strong> {{ now()->translatedFormat('d F Y, H:i') }}</td>
            <td style="width: 50%; text-align: right;"><strong>Total Data:</strong> {{ $reports->count() }} Laporan</td>
        </tr>
        @if(!empty($filters['status']) || !empty($filters['category']) || !empty($filters['priority']) || !empty($filters['date_from']))
        <tr>
            <td colspan="2" style="color: #64748B;">
                <strong>Filter Diterapkan:</strong> 
                @if(!empty($filters['status'])) [Status: {{ strtoupper($filters['status']) }}] @endif
                @if(!empty($filters['category'])) [Kategori: {{ $filters['category'] }}] @endif
                @if(!empty($filters['priority'])) [Prioritas: {{ strtoupper($filters['priority']) }}] @endif
                @if(!empty($filters['date_from'])) [Periode: {{ $filters['date_from'] }} s/d {{ $filters['date_to'] ?? 'Sekarang' }}] @endif
            </td>
        </tr>
        @endif
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 14%;">No. Laporan</th>
                <th style="width: 15%;">Pelapor & Kamar</th>
                <th style="width: 13%;">Kategori</th>
                <th style="width: 25%;">Judul & Deskripsi</th>
                <th style="width: 9%;">Prioritas</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 9%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $rep)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td><strong>{{ $rep->report_number }}</strong></td>
                <td>
                    {{ $rep->user->name }}<br>
                    <small style="color: #64748B;">{{ $rep->room ? 'Kamar ' . $rep->room->room_number : '-' }}</small>
                </td>
                <td>{{ $rep->category->name }}</td>
                <td>
                    <strong>{{ $rep->title }}</strong>
                    <div style="color: #475569; font-size: 9.5px; margin-top: 2px;">
                        {{ Str::limit($rep->description, 90) }}
                    </div>
                </td>
                <td>
                    <span class="badge badge-{{ $rep->priority }}">{{ $rep->priority }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ $rep->status }}">
                        {{ $rep->status === 'in_progress' ? 'Diproses' : ($rep->status === 'completed' ? 'Selesai' : ($rep->status === 'cancelled' ? 'Batal' : 'Pending')) }}
                    </span>
                </td>
                <td>{{ $rep->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">Tidak ada data laporan ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Dicetak oleh,</p>
            <div class="signature-line">{{ auth()->user()->name ?? 'Pengelola Kos' }}</div>
            <p style="margin: 0; font-size: 10px; color: #64748B;">Pemilik / Pengelola Kos</p>
        </div>
    </div>

</body>
</html>
