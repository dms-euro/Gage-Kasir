@extends('layouts.app')

@section('title', 'Produksi')

@section('content')
    <div>
        <h2 class="intro-y text-lg font-medium mt-10">
            Produksi Hari Ini
        </h2>

        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap xl:flex-nowrap items-center mt-2">
                <div class="w-full xl:w-auto flex items-center mt-3 xl:mt-0">
                    <button class="btn btn-primary shadow-md mr-2" data-tw-toggle="modal" data-tw-target="#modal-produksi">
                        <i data-lucide="shopping-bag" class="w-4 h-4 mr-2"></i>
                        Order Baru
                    </button>
                    <div class="dropdown">
                        <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                            <span class="w-5 h-5 flex items-center justify-center">
                                <i class="w-4 h-4" data-lucide="plus"></i>
                            </span>
                        </button>
                        <div class="dropdown-menu w-40">
                            <ul class="dropdown-content">
                                <li>
                                    <a href="{{ route('pelanggan.index') }}" class="dropdown-item">
                                        <i data-lucide="users" class="w-4 h-4 mr-2"></i> Tambah Pelanggan
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('kategori.index') }}" class="dropdown-item">
                                        <i data-lucide="tag" class="w-4 h-4 mr-2"></i> Kategori
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="hidden xl:block mx-auto text-slate-500">
                    Showing {{ $produksis->firstItem() ?? 0 }} to {{ $produksis->lastItem() ?? 0 }} of
                    {{ $produksis->total() }} entries
                </div>

                <div class="flex w-full sm:w-auto mt-3 xl:mt-0 gap-2">
                    <div class="w-56 relative text-slate-500">
                        <form action="{{ route('produksi.index') }}" method="GET" id="search-form">
                            <input type="text" name="search" class="form-control w-56 box pr-10"
                                placeholder="Cari nota..." value="{{ request('search') }}">
                            <button type="submit" class="absolute my-auto inset-y-0 right-0 mr-3">
                                <i class="w-4 h-4" data-lucide="search"></i>
                            </button>
                        </form>
                    </div>
                    <button class="btn btn-outline-primary shadow-md">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export
                    </button>
                </div>
            </div>
        </div>

        {{-- TABEL PRODUKSI --}}
        <div class="intro-y col-span-12 overflow-auto 2xl:overflow-visible mt-5">
            <table class="table table-report -mt-2">
                <thead>
                    <tr>
                        <th class="text-center w-10">#</th>
                        <th class="whitespace-nowrap">No. Nota</th>
                        <th class="whitespace-nowrap">Tanggal</th>
                        <th class="whitespace-nowrap">Pelanggan</th>
                        <th class="whitespace-nowrap">Item</th>
                        <th class="text-right whitespace-nowrap">Total</th>
                        <th class="text-center whitespace-nowrap">Status</th>
                        <th class="text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produksis as $index => $produksi)
                        <tr class="intro-x">
                            <td class="text-center">{{ $produksis->firstItem() + $index }}</td>

                            <td>
                                <a href="{{ route('produksi.invoice', $produksi->id_produksi) }}"
                                    class="underline decoration-dotted font-medium text-primary">
                                    #{{ $produksi->id_produksi }}
                                </a>
                            </td>

                            <td>
                                @if ($produksi->tanggal instanceof \Carbon\Carbon)
                                    {{ $produksi->tanggal->format('d-m-Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($produksi->tanggal)->format('d-m-Y') }}
                                @endif
                            </td>

                            <td>
                                <div class="font-medium">{{ $produksi->pelanggan->nama }}</div>
                                <div class="text-slate-500 text-xs">
                                    {{ $produksi->pelanggan->cv ?? ($produksi->pelanggan->alamat ?? '-') }}</div>
                            </td>

                            <td>
                                <div class="text-sm">
                                    @php
                                        $items = $produksi->detailProduksi->take(2);
                                        $count = $produksi->detailProduksi->count();
                                    @endphp
                                    @foreach ($items as $item)
                                        <div>{{ $item->deskripsi }} ({{ $item->jumlah }}x)</div>
                                    @endforeach
                                    @if ($count > 2)
                                        <div class="text-slate-400 text-xs">+{{ $count - 2 }} item lainnya</div>
                                    @endif
                                </div>
                            </td>

                            <td class="text-right font-medium">
                                Rp {{ number_format($produksi->total_tagihan, 0, ',', '.') }}
                            </td>

                            <td class="text-center">
                                <span
                                    class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $produksi->keterangan == 'LUNAS' ? 'bg-success/20 text-success' : 'bg-warning/20 text-warning' }}">
                                    {{ $produksi->keterangan }}
                                </span>
                            </td>

                            <td>
                                <div class="flex justify-center items-center gap-2">
                                    {{-- Detail / Invoice --}}
                                    <a href="{{ route('produksi.invoice', $produksi->id_produksi) }}"
                                        class="flex items-center text-primary text-sm" title="Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>

                                    {{-- Cetak --}}
                                    {{-- <a href="{{ route('produksi.cetak', $produksi->id_produksi) }}"
                                        class="flex items-center text-info text-sm" target="_blank" title="Cetak">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </a> --}}

                                    {{-- Cancel (Hanya jika bisa dicancel) --}}
                                    @if ($produksi->can_cancel)
                                        <form action="{{ route('produksi.destroy', $produksi->id_produksi) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center text-danger text-sm"
                                                onclick="return confirm('Batalkan order #{{ $produksi->id_produksi }}?')"
                                                title="Cancel Order">
                                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-500">
                                <i data-lucide="file-text" class="w-10 h-10 mx-auto mb-2 text-slate-400"></i>
                                Belum ada order hari ini. Klik "Order Baru" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-5">
            <div class="w-full sm:w-auto sm:mr-auto">
                {{ $produksis->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL PILIH PELANGGAN --}}
    <div id="modal-produksi" class="modal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">
                        <i data-lucide="users" class="w-5 h-5 mr-2 inline"></i>
                        Pilih Pelanggan
                    </h2>
                    <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="p-5">
                    <input type="text" id="searchPelanggan" class="form-control"
                        placeholder="Cari nama pelanggan...">
                </div>

                <div class="p-5 overflow-auto max-h-96">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>CV</th>
                                <th>No HP</th>
                                <th>Broker</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tablePelanggan">
                            @foreach ($pelanggans as $p)
                                <tr class="pelanggan-row">
                                    <td>{{ $p->id_pelanggan }}</td>
                                    <td>{{ $p->nama }}</td>
                                    <td>{{ $p->cv ?? '-' }}</td>
                                    <td>{{ $p->no_hp ?? ($p->cp ?? '-') }}</td>
                                    <td>
                                        <span
                                            class="px-2 py-1 rounded-full text-xs
                                    {{ $p->broker == 'Broker' ? 'bg-primary/20 text-primary' : '' }}
                                    {{ $p->broker == 'Non Broker' ? 'bg-slate-100 text-slate-600' : '' }}
                                    {{ $p->broker == 'Pajak' ? 'bg-warning/20 text-warning' : '' }}">
                                            {{ $p->broker }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('produksi.create', ['pelanggan_id' => $p->id]) }}"
                                            class="btn btn-primary btn-sm">
                                            Pilih
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Search pelanggan di modal
            document.getElementById('searchPelanggan')?.addEventListener('keyup', function() {
                let keyword = this.value.toLowerCase();
                let rows = document.querySelectorAll('.pelanggan-row');

                rows.forEach(row => {
                    let text = row.innerText.toLowerCase();
                    if (text.includes(keyword)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        </script>
    @endpush
@endsection
