<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi Tagihan - {{ $pelanggan->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body class="bg-slate-100 p-4 sm:p-6">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">

        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-info to-blue-600 text-white p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg sm:text-xl font-bold">
                        {{ $Profilperusahaan->nama_perusahaan ?? ($Profilperusahaan->nama ?? 'Percetakan') }}
                    </h1>
                    <p class="text-white/70 text-xs sm:text-sm mt-1">Tagihan Gabungan</p>
                </div>
                <div class="text-right">
                    <div class="text-xl font-bold">{{ $kodeTagihan }}</div>
                    <div class="text-white/70 text-xs mt-1">
                        {{ \Carbon\Carbon::parse(request('start_date'))->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse(request('end_date'))->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- PELANGGAN --}}
        <div class="p-5 sm:p-6 border-b">
            <div class="flex items-center gap-3">
                <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                <div>
                    <p class="font-semibold">{{ $pelanggan->nama }}</p>
                    <p class="text-xs text-slate-500">{{ $pelanggan->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- LIST ORDER --}}
        <div class="p-5 sm:p-6">
            <h3 class="font-medium text-slate-700 mb-3">Daftar Order ({{ $produksis->count() }} invoice)</h3>

            <div class="space-y-3">
                @foreach ($produksis as $p)
                    <div class="border border-slate-200 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-primary">#{{ $p->id_produksi }}</span>
                                <span
                                    class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</span>
                            </div>
                            <span class="font-semibold">Rp {{ number_format($p->total_tagihan, 0, ',', '.') }}</span>
                        </div>

                        {{-- Items --}}
                        <div class="text-sm text-slate-600">
                            @foreach ($p->detailProduksi as $item)
                                <div class="flex justify-between py-0.5">
                                    <span>{{ $item->deskripsi }} ({{ $item->jumlah }}x)</span>
                                    <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-between text-xs mt-2 pt-2 border-t border-slate-100">
                            <span>Dibayar: <span class="text-green-600">Rp
                                    {{ number_format($p->bayar, 0, ',', '.') }}</span></span>
                            <span>Sisa: <span class="text-warning">Rp
                                    {{ number_format($p->sisa_tagihan, 0, ',', '.') }}</span></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TOTAL --}}
        <div class="bg-slate-50 p-5 sm:p-6">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Tagihan ({{ $produksis->count() }} order)</span>
                    <span class="font-bold">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Sudah Dibayar</span>
                    <span class="text-green-600">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                </div>
                <div class="border-t my-2"></div>
                <div class="flex justify-between text-base font-bold">
                    <span>Total Sisa Tagihan</span>
                    <span class="text-amber-500">Rp {{ number_format($totalSisa, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="p-5 sm:p-6 border-t text-center no-print">
            <button onclick="window.print()"
                class="bg-blue-600 text-white px-5 py-2 rounded-xl text-sm font-medium mr-2">
                <i data-lucide="printer" class="w-4 h-4 inline mr-1"></i> Cetak
            </button>
            <button onclick="window.close()" class="bg-slate-500 text-white px-5 py-2 rounded-xl text-sm font-medium">
                <i data-lucide="x" class="w-4 h-4 inline mr-1"></i> Tutup
            </button>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
