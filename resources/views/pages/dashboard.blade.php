@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-12 gap-6">

        {{-- WELCOME CARD --}}
        <div class="col-span-12 mt-8">
            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">
                    Selamat Datang, {{ auth()->user()->nama }}!
                </h2>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="intro-y box p-5 mt-5 bg-primary/5 border border-primary/20">
                    <div class="flex items-center">
                        <div>
                            <div class="text-primary font-medium text-lg">
                                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</div>
                            <div class="text-slate-500 mt-1">Pantau performa bisnis percetakan Anda hari ini</div>
                        </div>
                    </div>
                </div>
                <div class="intro-y box p-5 mt-5 bg-primary/5 border border-primary/20">
                    <div class="grid grid-cols-3 gap-3">
                        <a href="{{ route('produksi.index') }}" class="btn btn-outline-primary py-3 justify-start">
                            <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i> Order Baru
                        </a>
                        <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-success py-3 justify-start">
                            <i data-lucide="user-plus" class="w-5 h-5 mr-2"></i> Tambah Pelanggan
                        </a>
                        <a href="{{ route('piutang.index') }}" class="btn btn-outline-warning py-3 justify-start">
                            <i data-lucide="credit-card" class="w-5 h-5 mr-2"></i> Bayar Piutang
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATS CARDS --}}
        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <div class="intro-y box p-5 zoom-in">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                        <i data-lucide="dollar-sign" class="w-6 h-6 text-primary"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-slate-500 text-sm">Omset Bulan ini</div>
                        <div class="text-2xl font-bold">Rp {{ number_format($omsetBulanIni, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <div class="intro-y box p-5 zoom-in">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-success/10 rounded-full flex items-center justify-center">
                        <i data-lucide="dollar-sign" class="w-6 h-6 text-success"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-slate-500 text-sm">Omset Hari Ini</div>
                        <div class="text-2xl font-bold text-success">Rp {{ number_format($omsetHariIni, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <div class="intro-y box p-5 zoom-in">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-warning/10 rounded-full flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-6 h-6 text-warning"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-slate-500 text-sm">Total Piutang</div>
                        <div class="text-2xl font-bold text-warning">Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 sm:col-span-6 xl:col-span-3">
            <div class="intro-y box p-5 zoom-in">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-info/10 rounded-full flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6 text-info"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-slate-500 text-sm">Total Pelanggan</div>
                        <div class="text-2xl font-bold text-info">{{ $totalPelanggan }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-8 mt-5">
            <div class="intro-y box p-5">
                <div class="flex items-center border-b border-slate-200/60 pb-3 mb-5">
                    <h3 class="font-medium text-base mr-auto">
                        <i data-lucide="bar-chart" class="w-5 h-5 mr-2 inline"></i>
                        Omset Bulan Ini
                    </h3>
                    <span class="text-success font-medium">Rp {{ number_format($omsetBulanIni, 0, ',', '.') }}</span>
                </div>
                <div class="h-[250px]">
                    <canvas id="omset-chart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-span-12 lg:col-span-4 mt-5">
            <div class="intro-y box p-5">
                <div class="flex items-center border-b border-slate-200/60 pb-3 mb-3">
                    <h3 class="font-medium text-base">
                        <i data-lucide="clock" class="w-5 h-5 mr-2 inline"></i>
                        Order Terbaru
                    </h3>
                    <a href="{{ route('produksi.index') }}" class="ml-auto text-primary text-sm">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    @forelse($orderTerbaru as $order)
                        <a href="{{ route('produksi.invoice', $order->id_produksi) }}"
                            class="flex items-center p-2 hover:bg-slate-50 rounded-md transition">
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                <i data-lucide="file-text" class="w-5 h-5 text-primary"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="font-medium">#{{ $order->id_produksi }}</div>
                                <div class="text-slate-500 text-xs">{{ $order->pelanggan->nama ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium">Rp {{ number_format($order->total_tagihan, 0, ',', '.') }}</div>
                                <div>
                                    @if ($order->keterangan == 'LUNAS')
                                        <span class="text-success text-xs">LUNAS</span>
                                    @else
                                        <span class="text-warning text-xs">UTANG</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5 text-slate-500">Belum ada order hari ini</div>
                    @endforelse
                </div>
            </div>
        </div>
        {{-- ================= KIRI: PELANGGAN PIUTANG ================= --}}
        <div class="col-span-12 lg:col-span-4 mt-5">
            <div class="intro-y box p-5 h-full">
                <div class="flex items-center border-b border-slate-200/60 pb-3 mb-3">
                    <h3 class="font-medium text-base">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 inline"></i>
                        Pelanggan dengan Piutang
                    </h3>
                    <a href="{{ route('piutang.index') }}" class="ml-auto text-primary text-sm">Lihat Semua</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr class="bg-slate-50">
                                <th>Pelanggan</th>
                                <th class="text-right">Total Piutang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelangganPiutang as $p)
                                <tr>
                                    <td>
                                        <a href="{{ route('pelanggan.produksi', $p->id) }}" class="font-medium">
                                            {{ $p->nama }}
                                        </a>
                                        <div class="text-slate-500 text-xs">{{ $p->cv ?? '-' }}</div>
                                    </td>
                                    <td class="text-right font-medium text-warning">
                                        Rp {{ number_format($p->total_piutang, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-3 text-slate-500">
                                        Tidak ada piutang
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= KANAN: GENERAL REPORT ================= --}}
        <div class="col-span-12 lg:col-span-8 mt-5">
            <div class="intro-y box p-5 h-full  ">
                <div class="flex items-center border-b border-slate-200/60 pb-3 mb-3">
                    <h3 class="font-medium text-base">
                        <i data-lucide="alert-circle" class="w-5 h-5 mr-2 inline"></i>
                        Laporan Produksi
                    </h3>
                    <a href="{{ route('produksi.index') }}" class="ml-auto text-primary text-sm">Lihat Semua</a>
                </div>
                <div class="box grid grid-cols-12">

                    {{-- LEFT PANEL --}}
                    <div class="col-span-12 lg:col-span-4 px-8 py-12 flex flex-col justify-center">
                        <i data-lucide="trending-up" class="w-10 h-10 text-primary"></i>

                        <div class="mt-12 text-slate-600">
                            Total Omset Bulan Ini
                        </div>

                        <div class="mt-4 text-2xl font-medium">
                            Rp {{ number_format($omsetBulanIni, 0, ',', '.') }}
                        </div>

                        <div class="mt-4 text-slate-500 text-xs">
                            {{ now()->translatedFormat('F Y') }}
                        </div>

                        <a href="{{ route('produksi.index') }}" class="btn btn-outline-primary rounded-full mt-8">
                            Buat Order Baru
                        </a>
                    </div>

                    {{-- RIGHT PANEL --}}
                    <div class="col-span-12 lg:col-span-8 p-8 border-t lg:border-t-0 lg:border-l border-dashed">

                        {{-- TAB --}}
                        <ul class="nav nav-pills w-60 mx-auto mb-8">
                            <li class="nav-item flex-1">
                                <button class="nav-link w-full active" data-tab-target="#harian">
                                    Harian
                                </button>
                            </li>

                            <li class="nav-item flex-1">
                                <button class="nav-link w-full" data-tab-target="#bulanan">
                                    Bulanan
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">

                            {{-- ================= HARIAN ================= --}}
                            <div class="tab-pane active grid grid-cols-12 gap-6" id="harian">

                                <div class="col-span-6 md:col-span-4">
                                    <div class="text-slate-500">Order Hari Ini</div>
                                    <div class="font-medium">{{ $orderHariIni }}</div>
                                </div>

                                <div class="col-span-6 md:col-span-4">
                                    <div class="text-slate-500">Omset Hari Ini</div>
                                    <div class="font-medium text-success">
                                        Rp {{ number_format($omsetHariIni, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="col-span-6 md:col-span-4">
                                    <div class="text-slate-500">Total Piutang</div>
                                    <div class="font-medium text-warning">
                                        Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                                    </div>
                                </div>

                                @foreach ($jenisPelanggans as $jenis)
                                    <div class="col-span-6 md:col-span-4">
                                        <div class="text-slate-500">{{ $jenis->nama_jenis }}</div>
                                        <div>{{ $orderPerJenisHarian[$jenis->nama_jenis] ?? 0 }} order</div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- ================= BULANAN ================= --}}
                            <div class="tab-pane hidden grid grid-cols-12 gap-6" id="bulanan">

                                <div class="col-span-6 md:col-span-4">
                                    <div class="text-slate-500">Order Bulan Ini</div>
                                    <div class="font-medium">
                                        {{ array_sum($orderPerJenisBulanan) }}
                                    </div>
                                </div>

                                <div class="col-span-6 md:col-span-4">
                                    <div class="text-slate-500">Omset Bulan Ini</div>
                                    <div class="font-medium text-success">
                                        Rp {{ number_format($omsetBulanIni, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="col-span-6 md:col-span-4">
                                    <div class="text-slate-500">Total Piutang</div>
                                    <div class="font-medium text-warning">
                                        Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                                    </div>
                                </div>

                                @foreach ($jenisPelanggans as $jenis)
                                    <div class="col-span-6 md:col-span-4">
                                        <div class="text-slate-500">{{ $jenis->nama_jenis }}</div>
                                        <div>{{ $orderPerJenisBulanan[$jenis->nama_jenis] ?? 0 }} order</div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            (function() {
                const ctx = document.getElementById('omset-chart')?.getContext('2d');
                if (!ctx) {
                    console.error('Canvas #omset-chart not found!');
                    return;
                }

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Omset (Rp)',
                            data: {!! json_encode($chartData) !!},
                            borderColor: '#1a56db',
                            backgroundColor: 'rgba(26, 86, 219, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (val) => 'Rp ' + val.toLocaleString('id-ID')
                                }
                            }
                        }
                    }
                });
            })();
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Ambil semua tab button dalam box laporan produksi
                const tabContainer = document.querySelector('.intro-y.box .box.grid');
                if (!tabContainer) return;

                const buttons = tabContainer.querySelectorAll('[data-tab-target]');
                const contents = tabContainer.querySelectorAll('.tab-pane');

                buttons.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();

                        // Hapus class active dari semua button
                        buttons.forEach(b => b.classList.remove('active'));

                        // Sembunyikan semua tab content
                        contents.forEach(c => {
                            c.classList.add('hidden');
                            c.classList.remove('active');
                        });

                        // Aktifkan button yang diklik
                        this.classList.add('active');

                        // Tampilkan target content
                        const targetId = this.getAttribute('data-tab-target');
                        const targetContent = document.querySelector(targetId);
                        if (targetContent) {
                            targetContent.classList.remove('hidden');
                            targetContent.classList.add('active');
                        }
                    });
                });

                // Pastikan tab Harian aktif saat load
                const activeContent = document.querySelector('#harian');
                const activeButton = document.querySelector('[data-tab-target="#harian"]');
                if (activeContent && activeButton) {
                    activeContent.classList.remove('hidden');
                    activeContent.classList.add('active');
                    activeButton.classList.add('active');
                }
            });
        </script>
    @endpush
@endsection
