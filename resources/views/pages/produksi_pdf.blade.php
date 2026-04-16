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
            border: 1px solid #1a56db;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $profil->nama ?? 'PERCETAKAN APP' }}</h1>
        <p>{{ $profil->alamat ?? '-' }} | Telp: {{ $profil->telepon ?? '-' }} | Email: {{ $profil->email ?? '-' }}</p>
    </div>

    <div class="title">{{ $title }}</div>

    <table>
        <thead>
            <tr>
                <th width="3%">#</th>
                <th width="12%">No. Nota</th>
                <th width="8%">Tanggal</th>
                <th width="15%">Pelanggan</th>
                <th width="25%">Item</th>
                <th width="12%">Total</th>
                <th width="10%">Dibayar</th>
                <th width="10%">Sisa</th>
                <th width="5%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produksis as $index => $p)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>#{{ $p->id_produksi }}</td>
                <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d-m-Y') }}</td>
                <td>
                    <strong>{{ $p->pelanggan->nama ?? '-' }}</strong><br>
                    <small>{{ $p->pelanggan->cv ?? '' }}</small>
                </td>
                <td>
                    @php
                        $items = $p->detailProduksi->take(2);
                        $count = $p->detailProduksi->count();
                    @endphp
                    @foreach($items as $item)
                        • {{ $item->deskripsi }} ({{ $item->jumlah }}x)<br>
                    @endforeach
                    @if($count > 2)
                        <small>+{{ $count - 2 }} item lainnya</small>
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($p->total_tagihan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($p->total_dibayar, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($p->sisa_tagihan, 0, ',', '.') }}</td>
                <td class="text-center">
                    @if($p->keterangan == 'LUNAS')
                        <span class="status-lunas">LUNAS</span>
                    @else
                        <span class="status-utang">UTANG</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data produksi</td>
            </tr>
            @endforelse

            @if($produksis->count() > 0)
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalOmset, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($produksis->sum('total_dibayar'), 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($produksis->sum('sisa_tagihan'), 0, ',', '.') }}</strong></td>
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
