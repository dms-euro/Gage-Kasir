@extends('layouts.app')

@section('title', 'Riwayat Produksi - ' . $pelanggan->nama)

@section('content')
    <div>
        <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                Riwayat Produksi - {{ $pelanggan->nama }}
            </h2>
            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                <a href="{{ route('produksi.create', ['pelanggan_id' => $pelanggan->id]) }}"
                    class="btn btn-primary shadow-md mr-2">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Order Baru
                </a>
                <a href="{{ route('pelanggan.index') }}" class="btn btn-outline-secondary shadow-md">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
        </div>

        {{-- INFO PELANGGAN --}}
        <div class="intro-y box p-5 mt-5">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <div class="text-slate-500 text-xs">ID Pelanggan</div>
                    <div class="font-medium">{{ $pelanggan->id_pelanggan }}</div>
                </div>
                <div>
                    <div class="text-slate-500 text-xs">Nama / CV</div>
                    <div class="font-medium">{{ $pelanggan->nama_lengkap }}</div>
                </div>
                <div>
                    <div class="text-slate-500 text-xs">Contact</div>
                    <div class="font-medium">{{ $pelanggan->no_hp ?? ($pelanggan->cp ?? '-') }}</div>
                </div>
                <div>
                    <div class="text-slate-500 text-xs">Broker</div>
                    <div class="font-medium">{{ $pelanggan->broker }}</div>
                </div>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-12 gap-4 mt-5">
            <div class="col-span-12 sm:col-span-4">
                <div class="box p-5 bg-primary/5">
                    <div class="text-slate-500 text-sm">Total Order</div>
                    <div class="text-2xl font-bold text-primary">{{ $totalOrder }}</div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-4">
                <div class="box p-5 bg-success/5">
                    <div class="text-slate-500 text-sm">Total Transaksi</div>
                    <div class="text-2xl font-bold text-success">Rp {{ number_format($totalTransaksi, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="col-span-12 sm:col-span-4">
                <div class="box p-5 {{ $totalPiutang > 0 ? 'bg-warning/5' : 'bg-success/5' }}">
                    <div class="text-slate-500 text-sm">Total Piutang</div>
                    <div class="text-2xl font-bold {{ $totalPiutang > 0 ? 'text-warning' : 'text-success' }}">
                        Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="intro-y flex items-center mt-5">
            <div class="flex gap-2">
                {{-- Filter Status --}}
                <a href="{{ route('pelanggan.produksi', $pelanggan->id) }}"
                    class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }} px-4">
                    Semua
                </a>
                <a href="{{ route('pelanggan.produksi', $pelanggan->id) }}?status=LUNAS"
                    class="btn {{ request('status') == 'LUNAS' ? 'btn-success' : 'btn-outline-secondary' }} px-4">
                    Lunas
                </a>
                <a href="{{ route('pelanggan.produksi', $pelanggan->id) }}?status=UTANG"
                    class="btn {{ request('status') == 'UTANG' ? 'btn-warning' : 'btn-outline-secondary' }} px-4">
                    Utang
                </a>
                <a href="{{ route('pelanggan.produksi', $pelanggan->id) }}?status=CANCELLED"
                    class="btn {{ request('status') == 'CANCELLED' ? 'btn-danger' : 'btn-outline-secondary' }} px-4">
                    Cancelled
                </a>

                {{-- Multi Tagihan --}}
                <button class="btn btn-info shadow-md ml-auto" data-tw-toggle="modal" data-tw-target="#modal-multi-tagihan">
                    <i data-lucide="file-plus" class="w-4 h-4 mr-2"></i> Multi Tagihan
                </button>
            </div>
        </div>

        {{-- TABEL PRODUKSI --}}
        <div class="intro-y box p-5 mt-5">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="whitespace-nowrap">#</th>
                            <th class="whitespace-nowrap">No. Invoice</th>
                            <th class="whitespace-nowrap">Tanggal</th>
                            <th class="whitespace-nowrap">Item</th>
                            <th class="text-right whitespace-nowrap">Total</th>
                            <th class="text-right whitespace-nowrap">Dibayar</th>
                            <th class="text-right whitespace-nowrap">Sisa</th>
                            <th class="text-center whitespace-nowrap">Status</th>
                            <th class="text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produksis as $index => $p)
                            <tr class="{{ !$p->status ? 'bg-slate-100 opacity-70' : '' }}">
                                <td>{{ $produksis->firstItem() + $index }}</td>
                                <td>
                                    <a href="{{ route('produksi.invoice', $p->id_produksi) }}"
                                        class="text-primary font-medium">
                                        #{{ $p->id_produksi }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</td>
                                <td>
                                    @php
                                        $items = $p->detailProduksi->take(2);
                                        $count = $p->detailProduksi->count();
                                    @endphp
                                    @foreach ($items as $item)
                                        <div class="text-sm">{{ $item->deskripsi }} ({{ $item->jumlah }}x)</div>
                                    @endforeach
                                    @if ($count > 2)
                                        <small class="text-slate-400">+{{ $count - 2 }} item</small>
                                    @endif
                                </td>
                                <td class="text-right font-medium">Rp {{ number_format($p->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="text-right text-success">Rp {{ number_format($p->total_dibayar, 0, ',', '.') }}
                                </td>
                                <td class="text-right {{ $p->sisa_tagihan > 0 ? 'text-warning' : 'text-success' }}">
                                    Rp {{ number_format($p->sisa_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if (!$p->status)
                                        <span
                                            class="px-2 py-1 bg-danger/20 text-danger rounded-full text-xs">CANCELLED</span>
                                    @elseif($p->keterangan == 'LUNAS')
                                        <span class="px-2 py-1 bg-success/20 text-success rounded-full text-xs">LUNAS</span>
                                    @else
                                        <span class="px-2 py-1 bg-warning/20 text-warning rounded-full text-xs">UTANG</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="flex justify-center gap-1">
                                        <a href="{{ route('produksi.invoice', $p->id_produksi) }}"
                                            class="btn btn-sm btn-outline-primary" title="Detail">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        @if ($p->keterangan == 'UTANG' && $p->status)
                                            <a href="{{ route('piutang.show', $p->id_produksi) }}"
                                                class="btn btn-sm btn-outline-warning" title="Bayar">
                                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-slate-500">
                                    <i data-lucide="shopping-bag" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                    Belum ada riwayat produksi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $produksis->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL MULTI TAGIHAN --}}
    <div id="modal-multi-tagihan" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">
                        <i data-lucide="file-plus" class="w-5 h-5 mr-2 inline text-info"></i>
                        Multi Tagihan
                    </h2>
                    <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('pelanggan.multi-tagihan', $pelanggan->id) }}" method="POST"
                        target="_blank">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label font-medium">Pilih Rentang Tanggal</label>
                            <p class="text-slate-500 text-xs mb-3">
                                Pilih tanggal untuk menggabungkan tagihan UTANG
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="form-label text-xs">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div>
                                <label class="form-label text-xs">Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="p-3 bg-info/10 rounded-lg">
                            <div class="flex items-center gap-2 text-sm">
                                <i data-lucide="info" class="w-4 h-4 text-info"></i>
                                <span class="text-info font-medium">Informasi:</span>
                            </div>
                            <ul class="mt-2 text-xs text-slate-600 space-y-1 list-disc pl-4">
                                <li>Hanya order dengan status <strong>UTANG</strong> yang akan digabung</li>
                                <li>Order <strong>LUNAS</strong> dan <strong>CANCELLED</strong> tidak termasuk</li>
                                <li>Tagihan gabungan akan ditampilkan di tab baru</li>
                            </ul>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary">
                        Batal
                    </button>
                    <button type="button" onclick="document.querySelector('#modal-multi-tagihan form').submit()"
                        class="btn btn-info">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Tampilkan Tagihan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
