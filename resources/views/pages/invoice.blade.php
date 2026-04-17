@extends('layouts.app')

@section('title', 'Invoice #' . $produksi->id_produksi)

@section('content')
    <div>
        <div class="intro-y flex flex-col sm:flex-row items-center mt-8 no-print">
            <h2 class="text-lg font-medium mr-auto">
                Invoice #{{ $produksi->id_produksi }}
            </h2>
            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                <a href="{{ route('produksi.cetak', $produksi->id_produksi) }}" class="btn btn-primary shadow-md mr-2"
                    target="_blank">
                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak Nota
                </a>
                <a href="{{ route('produksi.index') }}" class="btn btn-primary shadow-md mr-2 no-print">
                    <i data-lucide="home" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
        </div>

        {{-- INVOICE CONTENT --}}
        <div class="invoice-print-area intro-y box overflow-hidden mt-5" id="invoice-content">
            <div
                class="flex flex-col lg:flex-row items-center lg:items-start pt-10 px-5 sm:px-20 sm:pt-20 lg:pb-10 text-center sm:text-left">
                {{-- Logo & Nama Perusahaan --}}
                <div class="flex items-center gap-4">
                    @if ($Profilperusahaan->logo)
                        <div class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 flex items-center justify-center">
                            <img src="{{ asset('storage/' . $Profilperusahaan->logo) }}" alt="Logo"
                                class="w-full h-full object-contain">
                        </div>
                    @endif
                    <div>
                        <div class="font-bold text-primary text-2xl sm:text-3xl">
                            {{ $Profilperusahaan->nama ?? 'Nama Perusahaan' }}
                        </div>
                        <div class="text-slate-500 text-sm mt-1">Invoice Resmi</div>
                    </div>
                </div>

                {{-- Info Perusahaan (Kanan) --}}
                <div class="mt-8 lg:mt-0 lg:ml-auto lg:text-right">
                    <div class="inline-block px-4 py-1 bg-primary/10 rounded-full text-primary font-medium text-sm mb-3">
                        {{ $Profilperusahaan->nama ?? 'Nama Perusahaan' }}
                    </div>
                    <div class="space-y-1 text-sm">
                        <div class="flex lg:justify-end items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                            <span>{{ $Profilperusahaan->email ?? '-' }}</span>
                        </div>
                        <div class="flex lg:justify-end items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i>
                            <span>{{ $Profilperusahaan->alamat ?? '-' }}</span>
                        </div>
                        <div class="flex lg:justify-end items-center gap-2">
                            <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                            <span>{{ $Profilperusahaan->telepon ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="flex flex-col lg:flex-row border-y border-slate-200 dark:border-darkmode-400 bg-slate-50/50 dark:bg-darkmode-700/30 px-5 sm:px-20 py-8 sm:py-12">
                <div>
                    <div class="flex items-center gap-2 text-slate-500 text-sm mb-3">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        <span class="font-medium uppercase tracking-wide">Data Pelanggan</span>
                    </div>
                    <div class="space-y-2">
                        <div class="text-xl font-semibold text-primary">
                            {{ $produksi->pelanggan->nama_lengkap ?? $produksi->pelanggan->nama }}
                        </div>
                        <div class="grid grid-cols-1 gap-1 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 w-20">ID</span>
                                <span class="font-medium">{{ $produksi->pelanggan->id_pelanggan ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 w-20">Contact</span>
                                <span>{{ $produksi->pelanggan->cp ?? ($produksi->pelanggan->no_hp ?? '-') }}</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-slate-400 w-20">Alamat</span>
                                <span>{{ $produksi->pelanggan->alamat ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400 w-20">Broker</span>
                                <span>
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-darkmode-600 rounded text-xs">
                                        {{ $produksi->pelanggan->broker ?? 'Non Broker' }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 lg:mt-0 lg:ml-auto lg:text-right">
                    <div class="flex lg:justify-end items-center gap-2 text-slate-500 text-sm mb-3">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span class="font-medium uppercase tracking-wide">Detail Invoice</span>
                    </div>
                    <div class="space-y-2">
                        <div>
                            <div class="text-2xl font-bold text-primary">#{{ $produksi->id_produksi }}</div>
                            <div class="text-slate-500 text-sm mt-1">
                                {{ \Carbon\Carbon::parse($produksi->tanggal)->translatedFormat('d F Y') }}
                            </div>
                        </div>
                        <div class="mt-3">
                            @if ($produksi->keterangan == 'LUNAS')
                                <span
                                    class="inline-flex items-center gap-1 px-4 py-1.5 bg-success/10 text-success rounded-full text-sm font-medium">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    LUNAS
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 px-4 py-1.5 bg-warning/10 text-warning rounded-full text-sm font-medium">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    UTANG
                                </span>
                            @endif
                        </div>
                        <div class="text-sm text-slate-500 mt-2">
                            <span class="text-slate-400">PIC:</span> {{ $produksi->pic ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-5 sm:px-16 py-8 sm:py-12">
                <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    <span class="font-medium uppercase tracking-wide">Rincian Item</span>
                </div>

                <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-darkmode-400">
                    <table class="table">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-darkmode-800">
                                <th class="whitespace-nowrap px-4 py-3 text-left">DESKRIPSI</th>
                                <th class="whitespace-nowrap px-4 py-3 text-right">UKURAN (m)</th>
                                <th class="whitespace-nowrap px-4 py-3 text-right">QTY</th>
                                <th class="whitespace-nowrap px-4 py-3 text-right">HARGA/m²</th>
                                <th class="whitespace-nowrap px-4 py-3 text-right">SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produksi->detailProduksi as $index => $item)
                                <tr class="{{ $index % 2 == 0 ? '' : 'bg-slate-50/50 dark:bg-darkmode-700/30' }}">
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $item->deskripsi }}</div>
                                        <div class="text-slate-500 text-xs mt-0.5 flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-darkmode-600 rounded">
                                                {{ $item->kategori->nama_kategori ?? '-' }}
                                            </span>
                                            @if ($item->bahan)
                                                <span class="text-slate-400">•</span>
                                                <span>{{ $item->bahan }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        {{ number_format($item->panjang, 2, ',', '.') }} ×
                                        {{ number_format($item->lebar, 2, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ $item->jumlah }}</td>
                                    <td class="px-4 py-3 text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-medium">Rp
                                        {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-slate-500">
                                        <i data-lucide="package" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                                        Tidak ada item dalam invoice ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-5 sm:px-16 pb-10 sm:pb-16 flex flex-col lg:flex-row gap-8">
                <div class="lg:w-1/2">
                    <div class="bg-slate-50 dark:bg-darkmode-700/50 rounded-lg p-5">
                        <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            <span class="font-medium uppercase tracking-wide">Informasi Pembayaran</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 mr-2">Metode :</span>
                                <span class="font-medium">{{ $produksi->pembayaran }}</span>
                            </div>
                            @if ($Profilperusahaan->no_rekening && $produksi->pembayaran == 'Bank')
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 mr-2">No. Rekening :</span>
                                    <span class="font-medium font-mono">{{ $Profilperusahaan->no_rekening }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500 mr-2">Operator :</span>
                                <span>{{ $produksi->user->nama ?? '-' }}</span>
                            </div>
                            <div class="border-t border-slate-200 dark:border-darkmode-400 pt-3 mt-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 mr-2">Status Pembayaran</span>
                                    <span
                                        class="{{ $produksi->keterangan == 'LUNAS' ? 'text-success' : 'text-warning' }} font-medium">
                                        {{ $produksi->keterangan == 'LUNAS' ? '✓ Sudah Lunas' : '⏳ Belum Lunas' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2 lg:ml-auto">
                    <div class="bg-slate-50 dark:bg-darkmode-700/50 rounded-lg p-6">

                        <div class="text-sm text-slate-500 mb-5 font-semibold uppercase">
                            Ringkasan
                        </div>

                        <div class="space-y-3 text-sm tabular-nums">

                            <!-- Subtotal -->
                            <div class="grid grid-cols-2">
                                <span class="text-slate-500">Subtotal</span>
                                <span class="text-right font-medium">
                                    Rp {{ number_format($produksi->subtotal_item, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Design -->
                            <div class="grid grid-cols-2">
                                <span class="text-slate-500">Biaya Design</span>
                                <span class="text-right">
                                    Rp {{ number_format($produksi->biaya_design, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Diskon -->
                            @if ($produksi->diskon > 0)
                                <div class="grid grid-cols-2 text-red-500">
                                    <span>Diskon</span>
                                    <span class="text-right">
                                        - Rp {{ number_format($produksi->diskon, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                            <div class="border-t my-2"></div>

                            <!-- TOTAL -->
                            <div class="grid grid-cols-2 text-base font-semibold">
                                <span>Total Tagihan</span>
                                <span class="text-right text-primary">
                                    Rp {{ number_format($produksi->total_tagihan, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Dibayar -->
                            <div class="grid grid-cols-2">
                                <span class="text-slate-500">Sudah Dibayar</span>
                                <span class="text-right">
                                    Rp {{ number_format($produksi->total_dibayar, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="border-t my-2"></div>

                            <!-- Sisa -->
                            <div class="grid grid-cols-2 items-center">
                                <span class="font-semibolt">Sisa Tagihan</span>

                                @if ($produksi->sisa_tagihan == 0)
                                    <span class="text-right text-green-600 font-semibold">
                                        LUNAS
                                    </span>
                                @else
                                    <span class="text-right text-orange-500 font-semibold">
                                        Rp {{ number_format($produksi->sisa_tagihan, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            @if ($produksi->detailPiutang->count() > 1)
                <div class="px-5 sm:px-16 pb-10 sm:pb-16 border-t border-slate-200 dark:border-darkmode-400">
                    <div class="pt-8">
                        <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
                            <i data-lucide="history" class="w-4 h-4"></i>
                            <span class="font-medium uppercase tracking-wide">Riwayat Pembayaran</span>
                        </div>
                        <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-darkmode-400">
                            <table class="table table-sm">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-darkmode-800">
                                        <th class="whitespace-nowrap px-4 py-2 text-left">Tanggal</th>
                                        <th class="whitespace-nowrap px-4 py-2 text-center">Cicilan Ke</th>
                                        <th class="whitespace-nowrap px-4 py-2 text-right">Nominal</th>
                                        <th class="whitespace-nowrap px-4 py-2 text-left">Metode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produksi->detailPiutang as $index => $pembayaran)
                                        <tr class="{{ $index % 2 == 0 ? '' : 'bg-slate-50/50 dark:bg-darkmode-700/30' }}">
                                            <td class="px-4 py-2">
                                                {{ \Carbon\Carbon::parse($pembayaran->tanggal)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <span
                                                    class="px-2 py-0.5 bg-slate-100 dark:bg-darkmode-600 rounded text-xs">
                                                    {{ $pembayaran->cicilan_ke }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-right font-medium">
                                                Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2">
                                                <span
                                                    class="px-2 py-0.5 bg-slate-100 dark:bg-darkmode-600 rounded text-xs">
                                                    {{ $pembayaran->pembayaran }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            <div class="px-5 sm:px-16 pb-10 sm:pb-12 text-center border-t border-slate-200 dark:border-darkmode-400">
                <div class="pt-6 text-slate-500 text-sm">
                    <p>Terima kasih telah menggunakan jasa kami.</p>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function printInvoice() {
                const originalTitle = document.title;

                document.title = 'Invoice-{{ $produksi->id_produksi }}';

                window.print();

                document.title = originalTitle;
            }
        </script>
    @endpush

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #invoice-content,
            #invoice-content * {
                visibility: visible;
            }

            #invoice-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

        }
    </style>
@endsection
