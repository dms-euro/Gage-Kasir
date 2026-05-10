    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <title>Nota #{{ $produksi->id_produksi }}</title>

        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                padding: 1px;
                margin: 0;
            }

            .kop {
                text-align: center;
                border-bottom: 2px solid #000;
                margin: 0;
                padding: 2px;
            }

            .logo {
                margin: 0;
                padding: 0;
                line-height: 0;
                padding: 5px;
            }

            .logo img {
                max-height: 90px;
                display: block;
                margin: 0 auto;
            }

            .logo-placeholder {
                width: auto;
                height: 90px;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .nota-header {
                background: #f0f0f0;
                padding: 6px;
                margin-bottom: 10px;
                border: 1px solid #999;
                font-size: 11px;
            }

            .item-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 5px;
            }

            .item-table th {
                background: #ddd;
                border: 1px solid #999;
                padding: 5px;
                font-size: 11px;
            }

            .item-table td {
                border: 1px solid #ccc;
                padding: 5px;
                font-size: 11px;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            .footer-note {
                text-align: center;
                justify-content: space-between;
                margin-top: 15px;
                font-size: 10px;
                border-top: 1px dashed #ccc;
                padding-top: 5px;
            }

            .nota-inline {
                display: flex;
                width: 100%;
                font-size: 12px;
            }
        </style>
    </head>

    <body>

        <div class="nota-container">

            {{-- LOGO --}}
            <div class="kop">
                @if ($Profilperusahaan->logo && file_exists(public_path('storage/' . $Profilperusahaan->logo)))
                    <div class="logo">
                        <img src="{{ public_path('storage/' . $Profilperusahaan->logo) }}">
                    </div>
                @else
                    <div class="logo-placeholder">
                        {{ strtoupper(substr($Profilperusahaan->nama_perusahaan ?? 'P', 0, 2)) }}
                    </div>
                @endif
            </div>

            <table width="100%" style="margin-bottom:5px; font-size:11px;">
                <tr>
                    <!-- DARI -->
                    <td width="50%" valign="top" style="padding-right:10px;">
                        <table>
                            <tr>
                                <td width="70"><strong>Nama</strong></td>
                                <td>: <strong>{{ $produksi->pelanggan->nama }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Telepon</strong></td>
                                <td>: {{ $produksi->pelanggan->no_hp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Alamat</strong></td>
                                <td>: {{ $produksi->pelanggan->alamat ?? '-' }}</td>
                            </tr>
                        </table>
                    </td>

                    <!-- KEPADA -->
                    <td width="50%" valign="top" style="padding-left:10px;">
                        <table>
                            <tr>
                                <td width="70"><strong>No. Fraktur</strong></td>
                                <td>: <strong>{{ $produksi->id_produksi }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>PIC</strong></td>
                                <td>: {{ $produksi->pic ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal</strong></td>
                                <td>: {{ $produksi->tanggal ?? '-' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- TABLE --}}
            <table class="item-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Deskripsi</th>
                        <th>P x L</th>
                        <th>Bahan</th>
                        <th>Harga</th>
                        <th>Jml</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @php $subtotal = 0; @endphp

                    @foreach ($produksi->detailProduksi as $i => $item)
                        @php
                            $total = $item->panjang * $item->lebar * $item->harga * $item->jumlah;
                            $subtotal += $total;
                        @endphp

                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td class="text-center">{{ $item->panjang }} x {{ $item->lebar }}</td>
                            <td class="text-center">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                            <td class="text-right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->jumlah }}</td>
                            <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="6" class="text-right"><strong>Subtotal</strong></td>
                        <td class="text-right"><strong>{{ number_format($subtotal, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>

            {{-- BOTTOM FIX --}}
            <table width="100%" style="margin-top:20px;">
                <tr>
                    <!-- TTD -->
                    <td width="60%" valign="bottom">
                        <table width="100%">
                            <tr>
                                <td align="center">
                                    <div style="margin-bottom:50px;">Hormat Kami</div>
                                    <div style="border-top:1px solid #000;">
                                        {{ $produksi->user->name ?? 'Admin' }}
                                    </div>
                                </td>

                                <td align="center">
                                    <div style="margin-bottom:50px;">Penerima</div>
                                    <div style="border-top:1px solid #000;">
                                        {{ $produksi->pelanggan->nama }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td width="40%" valign="top">

                        <div>
                            <!-- Biaya tambahan -->
                            @if (($produksi->biaya_design ?? 0) > 0)
                                <div style="margin-bottom:5px;">
                                    <span>Biaya Design</span>
                                    <span style="float:right;">
                                        {{ number_format($produksi->biaya_design, 0, ',', '.') }}
                                    </span>
                                    <div style="clear:both;"></div>
                                </div>
                            @endif

                            <!-- Diskon -->
                            @if (($produksi->diskon ?? 0) > 0)
                                <div style="margin-bottom:5px;">
                                    <span>Diskon</span>
                                    <span style="float:right;">
                                        -{{ number_format($produksi->diskon, 0, ',', '.') }}
                                    </span>
                                    <div style="clear:both;"></div>
                                </div>
                            @endif

                            <!-- TOTAL -->
                            <div style="font-weight:bold;">
                                <span>Total</span>
                                <span style="float:right;">
                                    {{ number_format($produksi->total_tagihan, 0, ',', '.') }}
                                </span>
                                <div style="clear:both;"></div>
                            </div>
                            <div style="border-top:1px dashed #ccc; margin:6px 0;"></div>
                            <!-- Dibayar -->
                            <div style="font-weight:bold">
                                <span>Dibayar</span>
                                <span style="float:right;">
                                    {{ number_format($produksi->total_tagihan - $produksi->sisa_tagihan, 0, ',', '.') }}
                                </span>
                                <div style="clear:both;"></div>
                            </div>

                            <!-- GARIS TIPIS -->
                            <div style="border-top:1px dashed #ccc; margin:6px 0;"></div>

                            <!-- SISA -->
                            <div style="font-weight:bold;">
                                <span>Sisa</span>
                                <span style="float:right;">
                                    @if ($produksi->sisa_tagihan == 0)
                                        LUNAS
                                    @else
                                        {{ number_format($produksi->sisa_tagihan, 0, ',', '.') }}
                                    @endif
                                </span>
                                <div style="clear:both;"></div>
                            </div>
                        </div>

                    </td>
                </tr>
            </table>
            <div class="footer-note">
                <div class="nota-inline">
                    <strong>
                        "Harap cek kembali detail pesanan Anda. Kami tidak menerima perubahan pesanan setelah
                        pembayaran."
                    </strong>
                </div>
            </div>

        </div>

    </body>

    </html>
