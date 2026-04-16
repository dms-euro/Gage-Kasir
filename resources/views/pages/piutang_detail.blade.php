@extends('layouts.app')

@section('title', 'Detail Piutang #' . $piutang->id_produksi)

@section('content')
    <div>
        <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                Detail Piutang #{{ $piutang->id_produksi }}
            </h2>
            <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
                <a href="{{ route('produksi.invoice', $piutang->id_produksi) }}"
                    class="btn btn-outline-primary shadow-md mr-2" target="_blank">
                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Print Invoice
                </a>
                <a href="{{ route('piutang.index') }}" class="btn btn-outline-secondary shadow-md">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
                </a>
            </div>
        </div>

        <div class="intro-y grid grid-cols-11 gap-5 mt-5">
            {{-- LEFT COLUMN --}}
            <div class="col-span-12 lg:col-span-4">

                {{-- INFO --}}
                <div class="box p-5">
                    <div class="font-medium text-base border-b pb-3 mb-4">Info Piutang</div>
                    <div class="space-y-3">
                        <div class="flex">
                            <span class="text-slate-500 w-24">Invoice</span>
                            <span class="font-medium">: #{{ $piutang->id_produksi }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-slate-500 w-24">Tanggal</span>
                            <span>: {{ $piutang->produksi->tanggal }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-slate-500 w-24">Status</span>
                            <span class="{{ $piutang->sisa_tagihan == 0 ? 'text-success' : 'text-warning' }}">
                                : {{ $piutang->sisa_tagihan == 0 ? 'LUNAS' : 'UTANG' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- PELANGGAN --}}
                <div class="box p-5 mt-5">
                    <div class="font-medium text-base border-b pb-3 mb-4">Data Pelanggan</div>
                    <div class="space-y-2">
                        <div class="font-medium">{{ $piutang->pelanggan->nama_lengkap ?? $piutang->pelanggan->nama }}</div>
                        <div class="text-slate-500 text-sm">ID: {{ $piutang->pelanggan->id_pelanggan }}</div>
                        <div class="text-sm">{{ $piutang->pelanggan->cp ?? ($piutang->pelanggan->no_hp ?? '-') }}</div>
                        <div class="text-sm text-slate-500">{{ $piutang->pelanggan->alamat ?? '-' }}</div>
                    </div>
                </div>

                {{-- RINGKASAN --}}
                <div class="box p-5 mt-5">
                    <div class="font-medium text-base border-b pb-3 mb-4">Ringkasan</div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-500 w-24">Total Tagihan</span>
                            <span class="font-medium">: Rp {{ number_format($piutang->total_tagihan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 w-24">Sudah Dibayar</span>
                            <span class="text-success">: Rp
                                {{ number_format($piutang->total_terbayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-black w-24">Sisa Tagihan</span>
                            <span class="text-warning">: Rp {{ number_format($piutang->sisa_tagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- FORM BAYAR --}}
                @if ($piutang->sisa_tagihan > 0)
                    <div class="box p-5 mt-5">
                        <div class="font-medium text-base border-b pb-3 mb-4">Form Pembayaran</div>
                        <form action="{{ route('piutang.bayar', $piutang->id_produksi) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label">Nominal</label>
                                <input type="number" name="nominal" class="form-control"
                                    max="{{ $piutang->sisa_tagihan }}" required>
                                <small class="text-slate-500">Maks: Rp
                                    {{ number_format($piutang->sisa_tagihan, 0, ',', '.') }}</small>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Metode</label>
                                <select name="pembayaran" class="form-select" required>
                                    <option value="Tunai">Tunai</option>
                                    <option value="Bank">Transfer Bank</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-full">
                                Simpan Pembayaran
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-span-12 lg:col-span-7">

                {{-- ITEM --}}
                <div class="box p-5">
                    <div class="font-medium text-base border-b pb-3 mb-4">Item Order</div>
                    <table class="table table-sm">
                        <thead>
                            <tr class="bg-slate-50">
                                <th>Deskripsi</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($piutang->produksi->detailProduksi as $item)
                                <tr>
                                    <td>
                                        <div>{{ $item->deskripsi }}</div>
                                        <small class="text-slate-500">{{ $item->kategori->nama_kategori ?? '-' }}</small>
                                    </td>
                                    <td class="text-right">{{ $item->jumlah }}</td>
                                    <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada item</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-bold">
                                <td colspan="3" class="text-right">Total</td>
                                <td class="text-right text-primary">Rp
                                    {{ number_format($piutang->total_tagihan, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- RIWAYAT --}}
                <div class="box p-5 mt-5">
                    <div class="font-medium text-base border-b pb-3 mb-4">Riwayat Pembayaran</div>
                    <table class="table table-sm">
                        <thead>
                            <tr class="bg-slate-50">
                                <th>Tanggal</th>
                                <th>Cicilan Ke</th>
                                <th>Metode</th>
                                <th class="text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($piutang->detailPiutang as $d)
                                <tr>
                                    <td>{{ $d->tanggal }}</td>
                                    <td>{{ $d->cicilan_ke }}</td>
                                    <td>{{ $d->pembayaran }}</td>
                                    <td class="text-right">Rp {{ number_format($d->nominal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-slate-500">Belum ada pembayaran</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
