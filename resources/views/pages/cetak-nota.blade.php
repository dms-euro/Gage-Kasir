<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Nota #{{ $produksi->id_produksi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            padding: 20px;
            background: white;
        }
        .nota-container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1a56db;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            overflow: hidden;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .logo-placeholder {
            width: 70px;
            height: 70px;
            background: #1a56db;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
        }
        .company h2 {
            font-size: 20px;
            color: #1a56db;
            margin-bottom: 5px;
        }
        .company p {
            font-size: 11px;
            color: #555;
            line-height: 1.4;
        }
        .invoice-box {
            text-align: right;
        }
        .invoice-box .invoice-number {
            font-size: 18px;
            font-weight: bold;
            color: #1a56db;
        }
        .invoice-box .invoice-date {
            font-size: 11px;
            color: #555;
        }

        /* CUSTOMER INFO */
        .customer-section {
            display: flex;
            margin-bottom: 20px;
        }
        .customer-label {
            width: 80px;
            font-weight: bold;
            color: #555;
        }
        .customer-detail {
            flex: 1;
        }
        .customer-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .customer-address {
            font-size: 11px;
            color: #555;
            line-height: 1.4;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th {
            background: #1a56db;
            color: white;
            padding: 8px 5px;
            font-size: 11px;
            text-align: center;
            border: 1px solid #1a56db;
        }
        td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 11px;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }

        /* SUMMARY */
        .summary {
            display: flex;
            justify-content: flex-end;
            margin: 15px 0;
        }
        .summary-table {
            width: 300px;
        }
        .summary-table td {
            padding: 5px 10px;
            border: none;
        }
        .summary-table .total-row {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 13px;
        }

        /* FOOTER */
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px dashed #999;
        }
        .signature {
            text-align: center;
            width: 180px;
        }
        .signature .name {
            margin-top: 40px;
            font-weight: bold;
        }
        .note {
            margin-top: 15px;
            font-size: 10px;
            color: #777;
            font-style: italic;
        }

        /* PRINT */
        @media print {
            body {
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }

        /* BUTTON */
        .print-btn {
            margin-bottom: 15px;
        }
        .btn {
            padding: 8px 20px;
            background: #1a56db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <div class="nota-container">

        {{-- TOMBOL PRINT --}}
        <div class="print-btn no-print">
            <button class="btn" onclick="window.print()">🖨️ Cetak Nota</button>
            <button class="btn" onclick="window.close()" style="background: #6b7280; margin-left: 10px;">Tutup</button>
        </div>

        {{-- HEADER --}}
        <div class="header">
            <div class="header-left">
                @if($Profilperusahaan->logo && file_exists(public_path('storage/' . $Profilperusahaan->logo)))
                    <div class="logo">
                        <img src="{{ asset('storage/' . $Profilperusahaan->logo) }}" alt="Logo">
                    </div>
                @else
                    <div class="logo-placeholder">
                        {{ strtoupper(substr($Profilperusahaan->nama ?? 'P', 0, 2)) }}
                    </div>
                @endif

                <div class="company">
                    <h2>{{ $Profilperusahaan->nama ?? 'PERCETAKAN APP' }}</h2>
                    <p>{{ $Profilperusahaan->alamat ?? '-' }}</p>
                    <p>Telp: {{ $Profilperusahaan->telepon ?? '-' }} | Email: {{ $Profilperusahaan->email ?? '-' }}</p>
                    @if($Profilperusahaan->no_rekening)
                        <p>Rek: {{ $Profilperusahaan->no_rekening }}</p>
                    @endif
                </div>
            </div>
            <div class="invoice-box">
                <div class="invoice-number">#{{ $produksi->id_produksi }}</div>
                <div class="invoice-date">{{ \Carbon\Carbon::parse($produksi->tanggal)->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 10px;">
                    <span style="background: #1a56db; color: white; padding: 3px 10px; border-radius: 20px; font-size: 11px;">
                        {{ $produksi->keterangan }}
                    </span>
                </div>
            </div>
        </div>

        {{-- CUSTOMER INFO --}}
        <div class="customer-section">
            <div class="customer-label">Pelanggan</div>
            <div class="customer-detail">
                <div class="customer-name">
                    {{ $produksi->pelanggan->nama_lengkap ?? $produksi->pelanggan->nama }}
                    @if($produksi->pelanggan->cv)
                        ({{ $produksi->pelanggan->cv }})
                    @endif
                </div>
                <div class="customer-address">{{ $produksi->pelanggan->alamat ?? '-' }}</div>
                <div class="customer-address">
                    CP: {{ $produksi->pelanggan->cp ?? $produksi->pelanggan->no_hp ?? '-' }} |
                    ID: {{ $produksi->pelanggan->id_pelanggan ?? '-' }}
                </div>
                <div class="customer-address">PIC: {{ $produksi->pic ?? '-' }} | Broker: {{ $produksi->pelanggan->broker ?? 'Non Broker' }}</div>
            </div>
        </div>

        {{-- TABLE ITEMS --}}
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="30%">Deskripsi</th>
                    <th width="12%">Ukuran (m)</th>
                    <th width="12%">Bahan</th>
                    <th width="12%">Harga/m²</th>
                    <th width="8%">Jml</th>
                    <th width="15%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @forelse($produksi->detailProduksi as $index => $item)
                    @php
                        $itemSubtotal = $item->panjang * $item->lebar * $item->harga * $item->jumlah;
                        $subtotal += $itemSubtotal;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->deskripsi }}</strong>
                            <br><small style="color: #777;">{{ $item->kategori->nama_kategori ?? '-' }}</small>
                        </td>
                        <td class="text-center">
                            @if($item->panjang > 0 && $item->lebar > 0)
                                {{ $item->panjang }} × {{ $item->lebar }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">{{ $item->bahan ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td class="text-right">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Tidak ada item</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #f0f0f0; font-weight: bold;">
                    <td colspan="6" class="text-right">Jumlah Total</td>
                    <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- SUMMARY --}}
        <div class="summary">
            <table class="summary-table">
                @if($produksi->biaya_design > 0)
                <tr>
                    <td>Biaya Design</td>
                    <td class="text-right">Rp {{ number_format($produksi->biaya_design, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($produksi->diskon > 0)
                <tr>
                    <td>Diskon</td>
                    <td class="text-right">- Rp {{ number_format($produksi->diskon, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total Tagihan</td>
                    <td class="text-right">Rp {{ number_format($produksi->total_tagihan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Sudah Dibayar</td>
                    <td class="text-right">Rp {{ number_format($produksi->total_dibayar, 0, ',', '.') }}</td>
                </tr>
                <tr style="font-weight: bold; color: {{ $produksi->sisa_tagihan > 0 ? '#f59e0b' : '#10b981' }};">
                    <td>Sisa Tagihan</td>
                    <td class="text-right">
                        @if($produksi->sisa_tagihan == 0)
                            LUNAS
                        @else
                            Rp {{ number_format($produksi->sisa_tagihan, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- PAYMENT INFO --}}
        <div style=" background: #f8fafc; border-radius: 5px;">
            <strong>Metode Pembayaran:</strong> {{ $produksi->pembayaran }} |
            <strong>Operator:</strong> {{ $produksi->user->nama ?? '-' }}
            @if($Profilperusahaan->no_rekening && $produksi->pembayaran == 'Bank')
                <br><strong>No. Rekening:</strong> {{ $Profilperusahaan->no_rekening }}
            @endif
        </div>

        {{-- FOOTER SIGNATURE --}}
        <div class="footer">
            <div class="signature">
                <div>Hormat Kami,</div>
                <div class="name">{{ auth()->user()->nama ?? 'Admin' }}</div>
            </div>
            <div class="signature">
                <div>Penerima,</div>
                <div class="name">{{ $produksi->pelanggan->nama ?? 'Pelanggan' }}</div>
            </div>
        </div>
    </div>

    {{-- AUTO PRINT --}}
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
