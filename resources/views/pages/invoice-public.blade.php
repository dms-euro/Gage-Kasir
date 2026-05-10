<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $produksi->id_produksi }}</title>
    <meta name="description"
        content="Invoice #{{ $produksi->id_produksi }} - {{ $Profilperusahaan->nama ?? 'Percetakan' }}">
    {{-- Open Graph / Social Media Preview --}}
    <meta property="og:title" content="Invoice #{{ $produksi->id_produksi }}">
    <meta property="og:description"
        content="Total Tagihan: Rp {{ number_format($produksi->total_tagihan, 0, ',', '.') }} | Status: {{ $produksi->keterangan }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .invoice-card {
            background: white;
            max-width: 700px;
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .invoice-header {
            background: linear-gradient(135deg, #1a56db, #1e40af);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }

        .invoice-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .invoice-header .invoice-no {
            font-size: 16px;
            opacity: 0.9;
        }

        .invoice-body {
            padding: 25px 30px;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-box h3 {
            font-size: 14px;
            color: #1a56db;
            margin-bottom: 8px;
        }

        .info-box p {
            font-size: 13px;
            color: #4b5563;
            line-height: 1.6;
        }

        .info-box .name {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th {
            background: #f3f4f6;
            padding: 10px 8px;
            text-align: left;
            font-size: 12px;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 10px 8px;
            font-size: 13px;
            border-bottom: 1px solid #f3f4f6;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 14px;
        }

        .total-row.final {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-lunas {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-utang {
            background: #fed7aa;
            color: #92400e;
        }

        .btn-wa {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #25D366;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.2s;
        }

        .btn-wa:hover {
            background: #1ea952;
        }

        .footer-actions {
            text-align: center;
            padding: 20px 30px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        @media (max-width: 600px) {
            .info-section {
                grid-template-columns: 1fr;
            }

            .invoice-body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-card">

        {{-- HEADER --}}
        <div class="invoice-header">
            <h1>{{ $Profilperusahaan->nama ?? 'Percetakan' }}</h1>
            <div class="invoice-no">Invoice #{{ $produksi->id_produksi }}</div>
            <div style="margin-top:10px;">
                <span class="badge {{ $produksi->keterangan == 'LUNAS' ? 'badge-lunas' : 'badge-utang' }}"
                    style="background:rgba(255,255,255,0.2);color:white;">
                    {{ $produksi->keterangan }}
                </span>
            </div>
        </div>

        {{-- BODY --}}
        <div class="invoice-body">

            {{-- INFO --}}
            <div class="info-section">
                <div class="info-box">
                    <h3>📋 Detail Invoice</h3>
                    <p>Tanggal:
                        <strong>{{ \Carbon\Carbon::parse($produksi->tanggal)->translatedFormat('d F Y') }}</strong></p>
                    <p>PIC: {{ $produksi->pic ?? '-' }}</p>
                    <p>Metode: {{ $produksi->pembayaran }}</p>
                </div>
                <div class="info-box">
                    <h3>👤 Pelanggan</h3>
                    <p class="name">{{ $produksi->pelanggan->nama_lengkap ?? $produksi->pelanggan->nama }}</p>
                    <p>{{ $produksi->pelanggan->alamat ?? '-' }}</p>
                    <p>CP: {{ $produksi->pelanggan->cp ?? ($produksi->pelanggan->no_hp ?? '-') }}</p>
                </div>
            </div>

            {{-- TABEL --}}
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="35%">Deskripsi</th>
                        <th width="12%" class="text-center">Ukuran</th>
                        <th width="8%" class="text-center">Jml</th>
                        <th width="15%" class="text-right">Harga</th>
                        <th width="15%" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produksi->detailProduksi as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->deskripsi }}</strong>
                                <br><small style="color:#6b7280;">{{ $item->bahan ?? '' }}</small>
                            </td>
                            <td class="text-center">{{ $item->panjang > 0 ? $item->panjang . '×' . $item->lebar : '-' }}
                            </td>
                            <td class="text-center">{{ $item->jumlah }}</td>
                            <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada item</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- TOTAL --}}
            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($produksi->subtotal_item, 0, ',', '.') }}</span>
                </div>
                @if ($produksi->biaya_design > 0)
                    <div class="total-row">
                        <span>Biaya Design</span>
                        <span>Rp {{ number_format($produksi->biaya_design, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($produksi->diskon > 0)
                    <div class="total-row" style="color:#ef4444;">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($produksi->diskon, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="total-row final" style="color:#1a56db;">
                    <span>Total Tagihan</span>
                    <span>Rp {{ number_format($produksi->total_tagihan, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span>Sudah Dibayar</span>
                    <span style="color:#10b981;">Rp {{ number_format($produksi->total_dibayar, 0, ',', '.') }}</span>
                </div>
                <div class="total-row final" style="color:{{ $produksi->sisa_tagihan > 0 ? '#f59e0b' : '#10b981' }};">
                    <span>Sisa Tagihan</span>
                    <span>
                        @if ($produksi->sisa_tagihan == 0)
                            LUNAS ✅
                        @else
                            Rp {{ number_format($produksi->sisa_tagihan, 0, ',', '.') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer-actions">
            @if ($produksi->pelanggan->no_hp || $produksi->pelanggan->cp)
                <a href="https://wa.me/{{ $produksi->pelanggan->no_wa }}" class="btn-wa" target="_blank">
                    💬 Hubungi via WhatsApp
                </a>
            @endif
            <p style="margin-top:15px;font-size:12px;color:#9ca3af;">
                {{ $Profilperusahaan->nama ?? 'Percetakan' }} • {{ $Profilperusahaan->telepon ?? '-' }}
            </p>
        </div>

    </div>
</body>

</html>
