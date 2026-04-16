@extends('layouts.app')

@section('title', 'Piutang')

@section('content')
    <div>
        <h2 class="intro-y text-lg font-medium mt-10">
            Daftar Piutang
        </h2>

        <div class="grid grid-cols-12 gap-6 mt-5">
            {{-- TOOLBAR --}}
            <div class="intro-y col-span-12 flex flex-wrap xl:flex-nowrap items-center mt-2">
                <div class="flex w-full sm:w-auto">
                    {{-- Search --}}
                    <div class="w-48 relative text-slate-500">
                        <form action="{{ route('piutang.index') }}" method="GET" id="search-form">
                            <input type="text" name="search" class="form-control w-48 box pr-10"
                                placeholder="Cari invoice..." value="{{ request('search') }}">
                            <button type="submit" class="absolute my-auto inset-y-0 right-0 mr-3">
                                <i class="w-4 h-4" data-lucide="search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="hidden xl:block mx-auto text-slate-500">
                    {{ $piutangs->firstItem() ?? 0 }} - {{ $piutangs->lastItem() ?? 0 }} dari {{ $piutangs->total() }} data
                </div>

                <div class="w-full xl:w-auto flex items-center mt-3 xl:mt-0 gap-2">
                    {{-- Export Button --}}
                    <button type="button" class="btn btn-outline-primary shadow-md" data-tw-toggle="modal"
                        data-tw-target="#export-modal">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export PDF
                    </button>
                </div>
            </div>

            {{-- SUMMARY CARDS --}}
            <div class="intro-y col-span-12">
                <div class="grid grid-cols-2 gap-4">
                    <div class="box p-5 bg-primary/5">
                        <div class="text-slate-500 text-sm">Total Piutang</div>
                        <div class="text-2xl font-bold text-primary mt-1">
                            Rp {{ number_format($totalOutstanding, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="box p-5 bg-warning/5">
                        <div class="text-slate-500 text-sm">Piutang Aktif</div>
                        <div class="text-2xl font-bold text-warning mt-1">
                            {{ $totalCount }} Invoice
                        </div>
                    </div>
                </div>
            </div>

            {{-- DATA LIST --}}
            <div class="intro-y col-span-12 overflow-auto">
                <table class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">INVOICE</th>
                            <th class="whitespace-nowrap">PELANGGAN</th>
                            <th class="text-center whitespace-nowrap">STATUS</th>
                            <th class="whitespace-nowrap">TANGGAL</th>
                            <th class="text-right whitespace-nowrap">TOTAL</th>
                            <th class="text-right whitespace-nowrap">DIBAYAR</th>
                            <th class="text-right whitespace-nowrap">SISA</th>
                            <th class="text-center whitespace-nowrap">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($piutangs as $p)
                            <tr class="intro-x">
                                <td class="!py-4">
                                    <a href="{{ route('piutang.show', $p->id_produksi) }}"
                                        class="underline decoration-dotted font-medium text-primary">
                                        #{{ $p->id_produksi }}
                                    </a>
                                </td>
                                <td>
                                    <div class="font-medium">{{ $p->pelanggan->nama ?? '-' }}</div>
                                    <div class="text-slate-500 text-xs">{{ $p->pelanggan->cv ?? '-' }}</div>
                                </td>
                                <td class="text-center">
                                    @if ($p->sisa_tagihan == 0)
                                        <span class="text-success flex items-center justify-center">
                                            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> LUNAS
                                        </span>
                                    @else
                                        <span class="text-warning flex items-center justify-center">
                                            <i data-lucide="clock" class="w-4 h-4 mr-1"></i> UTANG
                                        </span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($p->produksi->tanggal)->format('d-m-Y') }}</td>
                                <td class="text-right">Rp {{ number_format($p->total_tagihan, 0, ',', '.') }}</td>
                                <td class="text-right text-success">Rp {{ number_format($p->total_terbayar, 0, ',', '.') }}
                                </td>
                                <td
                                    class="text-right font-medium {{ $p->sisa_tagihan > 0 ? 'text-warning' : 'text-success' }}">
                                    Rp {{ number_format($p->sisa_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('piutang.show', $p->id_produksi) }}" class="btn btn-sm btn-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-slate-500">
                                    Tidak ada data piutang
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="intro-y col-span-12">
                {{ $piutangs->appends(request()->query())->links() }}
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
                        Export PDF - Laporan Piutang
                    </h2>
                    <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="export-form" action="{{ route('piutang.export-pdf') }}" method="GET">
                        <input type="hidden" name="search" value="{{ request('search') }}">

                        <div class="mb-4">
                            <label class="form-label font-medium">Pilih Rentang Tanggal</label>
                            <p class="text-slate-500 text-xs mb-3">
                                Kosongkan jika ingin export semua data
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label text-xs">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="export_start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div>
                                <label class="form-label text-xs">Tanggal Akhir</label>
                                <input type="date" name="end_date" id="export_end_date" class="form-control"
                                    value="{{ request('end_date') }}">
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-slate-50 rounded-lg">
                            <div class="flex items-center gap-2 text-sm">
                                <i data-lucide="info" class="w-4 h-4 text-primary"></i>
                                <span class="text-slate-600">Data yang akan diexport:</span>
                            </div>
                            <div class="mt-2 text-xs text-slate-500">
                                <span id="export-info">
                                    Semua data piutang
                                </span>
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

    @push('scripts')
        <script>
            document.getElementById('export_start_date')?.addEventListener('change', updateExportInfo);
            document.getElementById('export_end_date')?.addEventListener('change', updateExportInfo);

            function updateExportInfo() {
                let startDate = document.getElementById('export_start_date')?.value;
                let endDate = document.getElementById('export_end_date')?.value;
                let infoEl = document.getElementById('export-info');

                let info = [];

                if (startDate && endDate) {
                    info.push(`Periode: ${startDate} s/d ${endDate}`);
                } else if (startDate) {
                    info.push(`Dari: ${startDate}`);
                } else if (endDate) {
                    info.push(`Sampai: ${endDate}`);
                } else {
                    info.push(`Semua tanggal`);
                }

                infoEl.innerText = info.join(' | ');
            }

            function submitExport() {
                let form = document.getElementById('export-form');
                form.submit();
            }

            document.addEventListener('DOMContentLoaded', updateExportInfo);
        </script>
    @endpush
@endsection
