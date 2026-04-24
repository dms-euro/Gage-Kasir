@extends('layouts.app')

@section('title', 'Kas Buku')

@section('content')
    <div>
        {{-- ================= HEADER ================= --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-8 mb-6 gap-3">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Kas Buku</h2>
            </div>
        </div>

        {{-- ================= STATISTIK (5 KOLOM) ================= --}}
        <div class="grid grid-cols-3 gap-5">

            {{-- Total Saldo --}}
            <div class="intro-y box p-5 zoom-in bg-gradient-to-r from-primary/10 to-primary/5 border border-primary/20">
                <div class="flex items-center">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-primary/10 rounded-full flex items-center justify-center">
                        <i data-lucide="wallet" class="w-5 h-5 lg:w-6 lg:h-6 text-primary"></i>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <div class="text-slate-500 text-xs lg:text-sm">Total Saldo</div>
                        <div class="text-base lg:text-xl font-bold text-primary">
                            Rp {{ number_format($saldo, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Pemasukan --}}
            <div class="intro-y box p-5 zoom-in">
                <div class="flex items-center">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-success/10 rounded-full flex items-center justify-center">
                        <i data-lucide="arrow-down-left" class="w-5 h-5 lg:w-6 lg:h-6 text-success"></i>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <div class="text-slate-500 text-xs lg:text-sm">Total Pemasukan</div>
                        <div class="text-base lg:text-xl font-bold text-success">
                            Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Pengeluaran --}}
            <div class="intro-y box p-5 zoom-in">
                <div class="flex items-center">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-danger/10 rounded-full flex items-center justify-center">
                        <i data-lucide="arrow-up-right" class="w-5 h-5 lg:w-6 lg:h-6 text-danger"></i>
                    </div>
                    <div class="ml-3 lg:ml-4">
                        <div class="text-slate-500 text-xs lg:text-sm">Total Pengeluaran</div>
                        <div class="text-base lg:text-xl font-bold text-danger">
                            Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {{-- ================= TOOLBAR ================= --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-6 mb-4">

            {{-- Filter & Export --}}
            <div class="flex gap-2">
                <button class="btn btn-outline-secondary shadow-sm flex-1 sm:flex-none" data-tw-toggle="modal"
                    data-tw-target="#modal-filter">
                    <i data-lucide="filter" class="w-4 h-4 mr-2"></i>Filter
                    @if (request('start_date') || request('end_date') || request('tipe') || request('kategori'))
                        <span class="ml-1 w-2 h-2 bg-primary rounded-full"></span>
                    @endif
                </button>
                <button class="btn btn-outline-primary shadow-sm flex-1 sm:flex-none" data-tw-toggle="modal"
                    data-tw-target="#modal-export">
                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>Laporan
                </button>
            </div>

            {{-- Transaksi Baru (Kanan di desktop, full width di mobile) --}}
            <button class="btn btn-primary shadow-md w-full sm:w-auto" data-tw-toggle="modal"
                data-tw-target="#modal-tambah">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Transaksi Baru
            </button>
        </div>

        {{-- ================= TABEL ================= --}}
        <div class="intro-y box p-5 mt-5">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-darkmode-800">
                            <th class="whitespace-nowrap">Tanggal</th>
                            <th class="whitespace-nowrap">Tipe</th>
                            <th class="whitespace-nowrap">Kategori</th>
                            <th class="whitespace-nowrap">Keterangan</th>
                            <th class="text-right whitespace-nowrap">Nominal</th>
                            <th class="text-center whitespace-nowrap w-16"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kas as $k)
                            <tr class="hover:bg-slate-50 dark:hover:bg-darkmode-700">
                                <td>{{ $k->tanggal->format('d M Y') }}</td>
                                <td>
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium {{ $k->tipe == 'masuk' ? 'bg-success/20 text-success' : 'bg-danger/20 text-danger' }}">
                                        {{ $k->tipe == 'masuk' ? 'MASUK' : 'KELUAR' }}
                                    </span>
                                </td>
                                <td>{{ $k->kategori }}</td>
                                <td class="max-w-xs truncate">{{ $k->keterangan ?? '-' }}</td>
                                <td
                                    class="text-right font-medium {{ $k->tipe == 'masuk' ? 'text-success' : 'text-danger' }}">
                                    {{ $k->tipe == 'masuk' ? '+' : '−' }} Rp {{ number_format($k->nominal, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('kas.destroy', $k->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus transaksi? Saldo akan dikembalikan.')">
                                        @csrf @method('DELETE')
                                        <button class="text-slate-400 hover:text-danger transition" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-slate-500">
                                    <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                    Belum ada transaksi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-5">
                {{ $kas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div id="modal-tambah" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kas.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">
                            <i data-lucide="plus-circle" class="w-5 h-5 mr-2 inline text-primary"></i>
                            Transaksi Baru
                        </h2>
                        <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Tipe</label>

                            <select name="tipe" id="tipeKas" class="form-select" required>
                                <option value="masuk">Pemasukan (+)</option>
                                <option value="keluar">Pengeluaran (-)</option>
                            </select>

                            <div class="mt-2">
                                <span id="previewTipe" class="px-2 py-1 rounded text-xs bg-success/10 text-success">
                                    Pemasukan
                                </span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Kategori</label>
                            <input type="text" name="kategori" class="form-control"
                                placeholder="Contoh: DIGITAL, OPERASIONAL, GAJI" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Nominal (Rp)</label>
                            <input type="number" name="nominal" class="form-control" min="1"
                                placeholder="Masukkan nominal" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-24">Batal</button>
                        <button type="submit" class="btn btn-primary w-24">Simpan</button>
                    </div>
                </form>
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
                        Filter Data
                    </h2>
                    <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form action="{{ route('kas.index') }}" method="GET" id="filter-form">
                    <div class="modal-body">
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
                            <label class="form-label font-medium">Tipe Transaksi</label>
                            <select name="tipe" class="form-select">
                                <option value="">Semua</option>
                                <option value="masuk" {{ request('tipe') == 'masuk' ? 'selected' : '' }}>Pemasukan
                                </option>
                                <option value="keluar" {{ request('tipe') == 'keluar' ? 'selected' : '' }}>Pengeluaran
                                </option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label font-medium">Kategori</label>
                            <input type="text" name="kategori" class="form-control" placeholder="Cari kategori..."
                                value="{{ request('kategori') }}">
                        </div>
                        <div class="p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-2 text-sm">
                                <i data-lucide="info" class="w-4 h-4 text-primary"></i>
                                <span class="text-slate-600">Filter Aktif:</span>
                            </div>
                            <div class="mt-1 text-xs text-slate-500">
                                @if (request('start_date') && request('end_date'))
                                    Periode: {{ request('start_date') }} s/d {{ request('end_date') }}
                                @else
                                    Semua tanggal
                                @endif
                                <br>
                                Tipe: {{ request('tipe') ?? 'Semua' }} | Kategori: {{ request('kategori') ?? 'Semua' }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('kas.index') }}" class="btn btn-outline-secondary">Reset</a>
                        <button type="submit" class="btn btn-primary">Terapkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EXPORT --}}
    <div id="modal-export" class="modal" tabindex="-1" aria-hidden="true">
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
                <form action="{{ route('kas.export') }}" method="GET">
                    <div class="modal-body">
                        <input type="hidden" name="tipe" value="{{ request('tipe') }}">
                        <input type="hidden" name="kategori" value="{{ request('kategori') }}">

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
                        <div class="p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-2 text-sm">
                                <i data-lucide="info" class="w-4 h-4 text-primary"></i>
                                <span class="text-slate-600">Data yang akan diexport:</span>
                            </div>
                            <div class="mt-1 text-xs text-slate-500">
                                @if (request('start_date') && request('end_date'))
                                    Periode: {{ request('start_date') }} s/d {{ request('end_date') }}
                                @else
                                    Semua data
                                @endif
                                <br>
                                Tipe: {{ request('tipe') ?? 'Semua' }} | Kategori: {{ request('kategori') ?? 'Semua' }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="download" class="w-4 h-4 mr-2"></i>Export
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const select = document.getElementById('tipeKas');
                const preview = document.getElementById('previewTipe');

                function update() {
                    if (select.value === 'masuk') {
                        preview.innerText = 'Pemasukan';
                        preview.className = 'px-2 py-1 rounded text-xs bg-success/10 text-success';
                    } else {
                        preview.innerText = 'Pengeluaran';
                        preview.className = 'px-2 py-1 rounded text-xs bg-danger/10 text-danger';
                    }
                }

                select.addEventListener('change', update);
                update();
            });
        </script>
    @endpush
@endsection
