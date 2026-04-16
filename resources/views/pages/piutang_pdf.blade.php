<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1a56db;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            color: #1a56db;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 11px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 5px;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #1a56db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #1a56db;
            color: white;
            padding: 8px 5px;
            font-size: 10px;
            text-align: left;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .status-lunas {
            color: #10b981;
            font-weight: bold;
        }

        .status-utang {
            color: #f59e0b;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .total-row {
            background: #eef2ff;
            font-weight: bold;
        }

        .filter-info {
            background: #f3f4f6;
            padding: 8px 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $profil->nama ?? 'PERCETAKAN APP' }}</h1>
        <p>{{ $profil->alamat ?? '-' }} | Telp: {{ $profil->telepon ?? '-' }} | Email: {{ $profil->email ?? '-' }}</p>
    </div>

    <div class="title">{{ $title }}</div>

    <div class="filter-info">
        <strong>Status:</strong> {{ $statusText }} |
        <strong>Total Data:</strong> {{ $piutangs->count() }} invoice
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Tagihan</div>
            <div class="summary-value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Dibayar</div>
            <div class="summary-value" style="color: #10b981;">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Sisa</div>
            <div class="summary-value" style="color: #f59e0b;">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Outstanding</div>
            <div class="summary-value">{{ $totalOutstanding }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Lunas</div>
            <div class="summary-value">{{ $totalLunas }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%">#</th>
                <th width="12%">No. Invoice</th>
                <th width="15%">Pelanggan</th>
                <th width="8%">Tanggal</th>
                <th width="12%">Total</th>
                <th width="12%">Dibayar</th>
                <th width="12%">Sisa</th>
                <th width="10%">Cicilan</th>
                <th width="8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($piutangs as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>#{{ $p->id_produksi }}</td>
                    <td>
                        <strong>{{ $p->pelanggan->nama ?? '-' }}</strong>
                        @if ($p->pelanggan->cv)
                            <br><small>{{ $p->pelanggan->cv }}</small>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($p->produksi->tanggal)->format('d/m/Y') }}</td>
                    <td class="text-right">Rp {{ number_format($p->total_tagihan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($p->total_terbayar, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($p->sisa_tagihan, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $p->detailPiutang->count() }}x</td>
                    <td class="text-center">
                        @if ($p->sisa_tagihan == 0)
                            <span class="status-lunas">LUNAS</span>
                        @else
                            <span class="status-utang">UTANG</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse

            @if ($piutangs->count() > 0)
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalDibayar, 0, ',', '.') }}</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalSisa, 0, ',', '.') }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->nama }} | {{ date('d F Y H:i:s') }}</p>
    </div>
</body>

</html>
