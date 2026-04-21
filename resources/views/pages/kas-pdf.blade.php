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
            background: white;
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
            width: 100%;
            table-layout: fixed;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8fafc;
            border-radius: 5px;
        }

        .summary-item {
            text-align: center;
            padding: 8px;
            vertical-align: middle;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
        }

        .summary-value {
            font-size: 14px;
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
            border-bottom: 1px solid #ddd;
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

        .badge-masuk {
            color: #10b981;
            font-weight: bold;
        }

        .badge-keluar {
            color: #ef4444;
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
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $profil->nama ?? 'PERCETAKAN APP' }}</h1>
        <p>{{ $profil->alamat ?? '-' }} | Telp: {{ $profil->telepon ?? '-' }} | Email: {{ $profil->email ?? '-' }}</p>
    </div>

    <div class="title">{{ $title }}</div>

    <table class="summary">
        <tr>
            <td class="summary-item">
                <div class="summary-label">Saldo Akhir</div>
                <div class="summary-value">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
            </td>

            <td class="summary-item">
                <div class="summary-label">Total Pemasukan</div>
                <div class="summary-value" style="color: #10b981;">
                    Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </div>
            </td>

            <td class="summary-item">
                <div class="summary-label">Total Pengeluaran</div>
                <div class="summary-value" style="color: #ef4444;">
                    Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </div>
            </td>

            <td class="summary-item">
                <div class="summary-label">Total Transaksi</div>
                <div class="summary-value">{{ $kas->count() }}</div>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="10%">Tipe</th>
                <th width="15%">Kategori</th>
                <th width="28%">Keterangan</th>
                <th width="15%">Nominal</th>
                <th width="15%">User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kas as $index => $k)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $k->tanggal->format('d-m-Y') }}</td>
                    <td>
                        @if ($k->tipe == 'masuk')
                            <span class="badge-masuk">MASUK</span>
                        @else
                            <span class="badge-keluar">KELUAR</span>
                        @endif
                    </td>
                    <td>{{ $k->kategori }}</td>
                    <td>{{ $k->keterangan ?? '-' }}</td>
                    <td class="text-right">
                        {{ $k->tipe == 'masuk' ? '+' : '-' }} Rp {{ number_format($k->nominal, 0, ',', '.') }}
                    </td>
                    <td>{{ $k->user->nama ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px;">Tidak ada data transaksi</td>
                </tr>
            @endforelse

            @if ($kas->count() > 0)
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right">
                        @php
                            $totalMasukSum = $kas->where('tipe', 'masuk')->sum('nominal');
                            $totalKeluarSum = $kas->where('tipe', 'keluar')->sum('nominal');
                        @endphp
                        <strong>+ Rp {{ number_format($totalMasukSum, 0, ',', '.') }}</strong><br>
                        <strong>- Rp {{ number_format($totalKeluarSum, 0, ',', '.') }}</strong>
                    </td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->nama }} | {{ date('d F Y H:i:s') }}</p>
    </div>
</body>

</html>
