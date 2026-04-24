@extends('layouts.app')

@section('title', 'Produksi')

@section('content')
    <div>
        <h2 class="intro-y text-lg font-medium mt-10">
            Produksi
        </h2>

        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap xl:flex-nowrap items-center mt-2">
                <div class="w-full xl:w-auto flex items-center mt-3 xl:mt-0 gap-2">
                    {{-- Order Baru --}}
                    <button class="btn btn-primary shadow-md mr-2" data-tw-toggle="modal" data-tw-target="#modal-produksi">
                        <i data-lucide="shopping-bag" class="w-4 h-4 mr-2"></i>
                        Order Baru
                    </button>

                    {{-- Filter --}}
                    <button class="btn btn-outline-secondary shadow-md mr-2" data-tw-toggle="modal"
                        data-tw-target="#modal-filter">
                        <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                        Filter
                        @if (request('start_date') || request('end_date') || request('jenis_pelanggan_id') || request('status_filter'))
                            <span class="ml-1 w-2 h-2 bg-primary rounded-full inline-block"></span>
                        @endif
                    </button>
                </div>

                <div class="hidden xl:block mx-auto text-slate-500">
                    Showing {{ $produksis->firstItem() ?? 0 }} to {{ $produksis->lastItem() ?? 0 }} of
                    {{ $produksis->total() }} entries
                </div>

                <div class="flex w-full sm:w-auto mt-3 xl:mt-0 gap-2">
                    {{-- Search --}}
                    <div class="w-56 relative text-slate-500">
                        <form action="{{ route('produksi.index') }}" method="GET" id="search-form">
                            {{-- Keep filter values --}}
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <input type="hidden" name="jenis_pelanggan_id" value="{{ request('jenis_pelanggan_id') }}">
                            <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">

                            <input type="text" name="search" class="form-control w-56 box pr-10"
                                placeholder="Cari nota..." value="{{ request('search') }}">
                            <button type="submit" class="absolute my-auto inset-y-0 right-0 mr-3">
                                <i class="w-4 h-4" data-lucide="search"></i>
                            </button>
                        </form>
                    </div>

                    {{-- Export PDF --}}
                    <button type="button" class="btn btn-outline-primary shadow-md" data-tw-toggle="modal"
                        data-tw-target="#export-modal">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Laporan Produksi
                    </button>
                </div>
            </div>
        </div>

        {{-- TABEL PRODUKSI --}}
        <div class="intro-y col-span-12 overflow-auto 2xl:overflow-visible mt-5">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="text-center w-10">#</th>
                        <th class="whitespace-nowrap">No. Nota</th>
                        <th class="whitespace-nowrap">Tanggal</th>
                        <th class="whitespace-nowrap">Pelanggan</th>
                        <th class="whitespace-nowrap">Jenis</th>
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
                                    {{ $produksi->pelanggan->cv ?? ($produksi->pelanggan->alamat ?? '-') }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match ($produksi->pelanggan->jenisPelanggan?->nama_jenis) {
                                        'Broker' => 'bg-primary/20 text-primary',
                                        'Non Broker' => 'bg-slate-100 text-slate-600',
                                        'Kena Pajak' => 'bg-warning/20 text-warning',
                                        'CSR' => 'bg-success/20 text-success',
                                        default => 'bg-slate-100 text-slate-500',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs {{ $badgeClass }}">
                                    {{ $produksi->pelanggan->jenisPelanggan?->nama_jenis ?? '-' }}
                                </span>
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
                                    <a href="{{ route('produksi.invoice', $produksi->id_produksi) }}"
                                        class="flex items-center text-primary text-sm" title="Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i> Detail
                                    </a>
                                    @if ($produksi->can_cancel)
                                        <button type="button" class="flex items-center text-danger text-sm"
                                            data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal"
                                            onclick="setDelete('{{ $produksi->id_produksi }}')" title="Cancel Order">
                                            <i data-lucide="x-circle" class="w-4 h-4"></i> Cancel
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-500">
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
                                <th>Jenis Pelanggan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tablePelanggan">
                            @foreach ($pelanggans as $p)
                                <tr class="pelanggan-row">
                                    <td>{{ $p->id_pelanggan }}</td>
                                    <td>{{ $p->nama }}</td>
                                    <td>{{ $p->cv ?? '-' }}</td>
                                    <td>{{ $p->no_hp ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match ($p->jenisPelanggan?->nama_jenis) {
                                                'Broker' => 'bg-primary/20 text-primary',
                                                'Non Broker' => 'bg-slate-100 text-slate-600',
                                                'Kena Pajak' => 'bg-warning/20 text-warning',
                                                'CSR' => 'bg-success/20 text-success',
                                                default => 'bg-slate-100 text-slate-500',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs {{ $badgeClass }}">
                                            {{ $p->jenisPelanggan?->nama_jenis ?? '-' }}
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

    {{-- Modal Delete --}}
    <div id="delete-confirmation-modal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body p-0">
                        <div class="p-5 text-center">
                            <i data-lucide="alert-triangle" class="w-16 h-16 text-warning mx-auto mt-3"></i>

                            <div class="text-2xl mt-5 font-medium">
                                Batalkan Order?
                            </div>

                            <div class="text-slate-500 mt-2" id="delete-text">
                                Order akan dibatalkan.
                            </div>
                        </div>

                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">
                                Batal
                            </button>

                            <button type="submit" class="btn btn-danger w-24">
                                Ya
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EXPORT PDF --}}
    <div id="export-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">
                        <i data-lucide="file-text" class="w-5 h-5 mr-2 inline text-primary"></i>
                        Export PDF
                    </h2>
                    <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="export-form" action="{{ route('produksi.export-pdf') }}" method="GET" target="_blank">
                        {{-- Bawa parameter filter yang ada --}}
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">

                        <div class="mb-4">
                            <label class="form-label font-medium">Pilih Rentang Tanggal</label>
                            <p class="text-slate-500 text-xs mb-3">
                                Kosongkan jika ingin export sesuai filter aktif
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="form-label text-xs">Tanggal Mulai</label>
                                <input type="date" name="export_start_date" id="export_start_date"
                                    class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div>
                                <label class="form-label text-xs">Tanggal Akhir</label>
                                <input type="date" name="export_end_date" id="export_end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>
                        </div>

                        {{-- FILTER JENIS PELANGGAN - SAMA PERSIS DENGAN MODAL FILTER --}}
                        <div class="mb-4">
                            <label class="form-label font-medium">Jenis Pelanggan</label>
                            <select name="export_jenis_pelanggan_id" id="export_jenis_pelanggan_id" class="form-select">
                                <option value="">Semua Jenis</option>
                                @foreach ($jenisPelanggans as $jenis)
                                    <option value="{{ $jenis->id }}"
                                        {{ request('jenis_pelanggan_id') == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- FILTER STATUS PEMBAYARAN - SAMA PERSIS DENGAN MODAL FILTER --}}
                        <div class="mb-4">
                            <label class="form-label font-medium">Status Pembayaran</label>
                            <select name="export_status_filter" id="export_status_filter" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="LUNAS" {{ request('status_filter') == 'LUNAS' ? 'selected' : '' }}>LUNAS
                                </option>
                                <option value="UTANG" {{ request('status_filter') == 'UTANG' ? 'selected' : '' }}>UTANG
                                </option>
                            </select>
                        </div>

                        <div class="mt-4 p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-2 text-sm">
                                <i data-lucide="info" class="w-4 h-4 text-primary"></i>
                                <span class="text-slate-600">Data yang akan diexport:</span>
                            </div>
                            <div class="mt-2 text-xs text-slate-500" id="export-info">
                                @if (request('start_date') && request('end_date'))
                                    Periode: {{ request('start_date') }} s/d {{ request('end_date') }}
                                @elseif(request('start_date'))
                                    Dari: {{ request('start_date') }}
                                @elseif(request('end_date'))
                                    Sampai: {{ request('end_date') }}
                                @else
                                    Semua tanggal
                                @endif
                                <br>
                                Jenis:
                                {{ $jenisPelanggans->where('id', request('jenis_pelanggan_id'))->first()->nama_jenis ?? 'Semua' }}
                                |
                                Status: {{ request('status_filter') ?? 'Semua' }}
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24">
                        Batal
                    </button>
                    <button type="button" onclick="submitExport()" class="btn btn-primary w-32">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FILTER --}}
    <div id="modal-filter" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">
                        <i data-lucide="filter" class="w-5 h-5 mr-2 inline text-primary"></i>
                        Filter Produksi
                    </h2>
                    <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="filter-form" action="{{ route('produksi.index') }}" method="GET">
                        <input type="hidden" name="search" value="{{ request('search') }}">

                        <div class="mb-4">
                            <label class="form-label font-medium">Rentang Tanggal</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label text-xs">Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                </div>
                                <div>
                                    <label class="form-label text-xs">Tanggal Akhir</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-medium">Jenis Pelanggan</label>
                            <select name="jenis_pelanggan_id" class="form-select">
                                <option value="">Semua Jenis</option>
                                @foreach ($jenisPelanggans as $jenis)
                                    <option value="{{ $jenis->id }}"
                                        {{ request('jenis_pelanggan_id') == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-medium">Status Pembayaran</label>
                            <select name="status_filter" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="LUNAS" {{ request('status_filter') == 'LUNAS' ? 'selected' : '' }}>LUNAS
                                </option>
                                <option value="UTANG" {{ request('status_filter') == 'UTANG' ? 'selected' : '' }}>UTANG
                                </option>
                            </select>
                        </div>

                        <div class="mt-4 p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-2 text-sm">
                                <i data-lucide="info" class="w-4 h-4 text-primary"></i>
                                <span class="text-slate-600">Filter Aktif:</span>
                            </div>
                            <div class="mt-2 text-xs text-slate-500" id="filter-info">
                                @if (request('start_date') && request('end_date'))
                                    Periode: {{ request('start_date') }} s/d {{ request('end_date') }}
                                @elseif(request('start_date'))
                                    Dari: {{ request('start_date') }}
                                @elseif(request('end_date'))
                                    Sampai: {{ request('end_date') }}
                                @else
                                    Semua tanggal
                                @endif
                                <br>
                                Jenis:
                                {{ $jenisPelanggans->where('id', request('jenis_pelanggan_id'))->first()->nama_jenis ?? 'Semua' }}
                                |
                                Status: {{ request('status_filter') ?? 'Semua' }}
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('produksi.index') }}" class="btn btn-outline-secondary">
                        Reset Filter
                    </a>
                    <button type="button" onclick="document.getElementById('filter-form').submit()"
                        class="btn btn-primary">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i> Terapkan Filter
                    </button>
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

            // Set delete action
            function setDelete(id) {
                let form = document.getElementById('delete-form');
                form.action = '/produksi/' + id;

                document.getElementById('delete-text').innerText =
                    'Order #' + id + ' akan dibatalkan.';
            }

            // Submit export
            function submitExport() {
                let form = document.getElementById('export-form');

                // Ambil nilai dari form export
                let startDate = document.getElementById('export_start_date')?.value;
                let endDate = document.getElementById('export_end_date')?.value;
                let jenisId = document.getElementById('export_jenis_pelanggan_id')?.value;
                let statusFilter = document.getElementById('export_status_filter')?.value;

                // Buat URL dengan parameter
                let url = form.action + '?';

                if (startDate) url += 'start_date=' + startDate + '&';
                if (endDate) url += 'end_date=' + endDate + '&';
                if (jenisId) url += 'jenis_pelanggan_id=' + jenisId + '&';
                if (statusFilter) url += 'status_filter=' + statusFilter + '&';

                // Tambahkan search jika ada
                let search = '{{ request('search') }}';
                if (search) url += 'search=' + search + '&';

                // Hapus & terakhir
                url = url.slice(0, -1);

                // Buka di tab baru
                window.open(url, '_blank');
            }

            // Update info export
            document.getElementById('export_start_date')?.addEventListener('change', updateExportInfo);
            document.getElementById('export_end_date')?.addEventListener('change', updateExportInfo);
            document.getElementById('export_jenis_pelanggan_id')?.addEventListener('change', updateExportInfo);
            document.getElementById('export_status_filter')?.addEventListener('change', updateExportInfo);

            function updateExportInfo() {
                let startDate = document.getElementById('export_start_date')?.value;
                let endDate = document.getElementById('export_end_date')?.value;
                let jenisId = document.getElementById('export_jenis_pelanggan_id')?.value;
                let statusFilter = document.getElementById('export_status_filter')?.value;
                let infoEl = document.getElementById('export-info');

                // Ambil nama jenis pelanggan
                let jenisNama = 'Semua';
                if (jenisId) {
                    let option = document.querySelector('#export_jenis_pelanggan_id option[value="' + jenisId + '"]');
                    if (option) jenisNama = option.textContent;
                }

                let info = '';
                if (startDate && endDate) {
                    info = `Periode: ${startDate} s/d ${endDate}\n`;
                } else if (startDate) {
                    info = `Dari: ${startDate}\n`;
                } else if (endDate) {
                    info = `Sampai: ${endDate}\n`;
                } else {
                    info = `Sesuai filter aktif\n`;
                }
                info += `Jenis: ${jenisNama} | Status: ${statusFilter || 'Semua'}`;

                infoEl.innerText = info;
            }

            // Update info export
            document.getElementById('export_start_date')?.addEventListener('change', updateExportInfo);
            document.getElementById('export_end_date')?.addEventListener('change', updateExportInfo);

            function updateExportInfo() {
                let startDate = document.getElementById('export_start_date')?.value;
                let endDate = document.getElementById('export_end_date')?.value;
                let infoEl = document.getElementById('export-info');

                let jenis = document.querySelector('select[name="jenis_pelanggan_id"] option:checked')?.text || 'Semua';
                let status = '{{ request('status_filter') ?? 'Semua' }}';

                let info = '';
                if (startDate && endDate) {
                    info = `Periode: ${startDate} s/d ${endDate}\n`;
                } else if (startDate) {
                    info = `Dari: ${startDate}\n`;
                } else if (endDate) {
                    info = `Sampai: ${endDate}\n`;
                } else {
                    info = `Sesuai filter aktif\n`;
                }
                info += `Jenis: ${jenis} | Status: ${status}`;

                infoEl.innerText = info;
            }
        </script>
    @endpush
@endsection
