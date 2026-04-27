@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
    <!-- BEGIN: Content -->
    <div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h2 class="intro-y text-lg font-medium mt-10">
                Rekap Absensi
            </h2>
        </div>

        <div class="grid grid-cols-12 gap-6 mt-5">
            <!-- BEGIN: Filter -->
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                <div class="flex gap-2">
                    <a href="{{ route('absensi.rekap', array_merge(['mode' => 'hari'], request()->except('mode'))) }}"
                        class="btn {{ $mode == 'hari' ? 'btn-primary' : 'btn-outline-primary' }} shadow-md">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2"></i> Hari ini
                    </a>
                    <a href="{{ route('absensi.rekap', array_merge(['mode' => 'bulan'], request()->except('mode'))) }}"
                        class="btn {{ $mode == 'bulan' ? 'btn-primary' : 'btn-outline-primary' }} shadow-md">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2"></i> Bulan ini
                    </a>
                    <a href="{{ route('absensi.export', request()->all()) }}" target="_blank"
                        class="btn btn-danger shadow-md">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export PDF
                    </a>
                </div>
                {{-- gap --}}
                <div class="hidden md:block mx-auto text-slate-500"></div>

                <div class="flex gap-2">
                    @if ($mode == 'hari')
                        <input type="date" id="filterDate" class="form-control w-48"
                            value="{{ $date ?? now()->toDateString() }}">
                        <button id="btnFilter" class="btn btn-primary shadow-md">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i> Filter
                        </button>
                    @else
                        <input type="month" id="filterMonth" class="form-control w-40"
                            value="{{ $month ?? now()->format('Y-m') }}">
                        <button id="btnFilter" class="btn btn-primary shadow-md">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i> Filter
                        </button>
                    @endif
                    <button id="btnReset" class="btn btn-outline-secondary shadow-md">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Reset
                    </button>
                </div>
            </div>
            <!-- END: Filter -->

            <!-- BEGIN: Data List -->
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">

                {{-- MODE HARIAN --}}
                @if ($mode == 'hari')
                    <table class="table table-report -mt-2">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">#</th>
                                <th class="whitespace-nowrap">NAMA KARYAWAN</th>
                                <th class="whitespace-nowrap">LEVEL</th>
                                <th class="text-center whitespace-nowrap">STATUS</th>
                                <th class="text-center whitespace-nowrap">CHECK IN</th>
                                <th class="text-center whitespace-nowrap">CHECK OUT</th>
                                <th class="text-center whitespace-nowrap">KETERLAMBATAN</th>
                                <th class="text-center whitespace-nowrap">FOTO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $index => $user)
                                @php
                                    $absensi = $data[$user->id] ?? null;
                                @endphp
                                <tr class="intro-x">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="font-medium whitespace-nowrap">{{ $user->nama }}</div>
                                        <div class="text-slate-500 text-xs whitespace-nowrap">{{ $user->level }}</div>
                                    </td>
                                    <td class="text-center">{{ $user->level }}</td>
                                    <td class="text-center">
                                        @if ($absensi)
                                            @php
                                                $statusClass = match ($absensi->status) {
                                                    'hadir' => 'text-success',
                                                    'terlambat' => 'text-warning',
                                                    default => 'text-danger',
                                                };
                                                $statusIcon = match ($absensi->status) {
                                                    'hadir' => 'check-circle',
                                                    'terlambat' => 'clock',
                                                    default => 'x-circle',
                                                };
                                            @endphp
                                            <div class="flex items-center justify-center {{ $statusClass }}">
                                                <i data-lucide="{{ $statusIcon }}" class="w-4 h-4 mr-1"></i>
                                                {{ ucfirst($absensi->status) }}
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center text-danger">
                                                <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                                                Tidak Absen
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center font-mono">
                                        {{ $absensi && $absensi->check_in_time ? \Carbon\Carbon::parse($absensi->check_in_time)->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="text-center font-mono">
                                        {{ $absensi && $absensi->check_out_time ? \Carbon\Carbon::parse($absensi->check_out_time)->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($absensi && $absensi->check_in_late > 0)
                                            <span class="text-warning font-medium">{{ $absensi->check_in_late }}
                                                menit</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="table-report__action">
                                        <div class="flex justify-center items-center gap-2">
                                            @if ($absensi && $absensi->check_in_photo)
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="showPhoto('{{ asset('storage/' . $absensi->check_in_photo) }}', '{{ $date }}', 'Foto Masuk')">
                                                    <i data-lucide="image" class="w-3 h-3"></i> In
                                                </button>
                                            @endif
                                            @if ($absensi && $absensi->check_out_photo)
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="showPhoto('{{ asset('storage/' . $absensi->check_out_photo) }}', '{{ $date }}', 'Foto Pulang')">
                                                    <i data-lucide="image" class="w-3 h-3"></i> Out
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-8 text-slate-500">Tidak ada data karyawan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif

                {{-- MODE BULANAN --}}
                @if ($mode == 'bulan')
                    <div class="overflow-x-auto">
                        <div class="box p-5">
                            <div class="text-center mb-4">
                                <h3 class="text-lg font-medium text-slate-800">
                                    {{ \Carbon\Carbon::create()->month((int) $monthNum)->translatedFormat('F') }}
                                    {{ $year }}
                                </h3>
                                <p class="text-xs text-slate-400">Rekap Absensi Bulanan</p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="table table-bordered table-sm" style="min-width: 900px;">
                                    <thead>
                                        <tr class="bg-slate-100">
                                            <th class="sticky left-0 bg-slate-100 z-10 whitespace-nowrap text-center">
                                                Karyawan</th>
                                            <th class="sticky left-0 bg-slate-100 z-10 whitespace-nowrap text-center">Level
                                            </th>
                                            @for ($i = 1; $i <= $daysInMonth; $i++)
                                                <th class="text-center whitespace-nowrap">
                                                    <div class="text-sm font-semibold">{{ $i }}</div>
                                                    <div class="text-xs text-slate-400">
                                                        {{ \Carbon\Carbon::create()->day($i)->translatedFormat('D') }}
                                                    </div>
                                                </th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td class="sticky left-0 bg-white font-medium whitespace-nowrap">
                                                    {{ $user->nama }}
                                                </td>
                                                <td
                                                    class="sticky left-0 bg-white whitespace-nowrap text-xs text-slate-400 text-center">
                                                    {{ $user->level}}
                                                </td>
                                                @for ($i = 1; $i <= $daysInMonth; $i++)
                                                    @php
                                                        $day = str_pad($i, 2, '0', STR_PAD_LEFT);
                                                        $absensi = isset($absensiMap[$user->id][$day])
                                                            ? $absensiMap[$user->id][$day]
                                                            : null;
                                                        $status = $absensi ? $absensi->status : 'absen';

                                                        if ($status == 'hadir') {
                                                            $bgColor = 'bg-primary text-white';
                                                            $displayText = 'Hadir';
                                                        } elseif ($status == 'terlambat') {
                                                            $bgColor = 'bg-warning text-white';
                                                            $displayText = 'Terlambat';
                                                        } else {
                                                            $bgColor = 'bg-danger text-white';
                                                            $displayText = '-';
                                                        }
                                                    @endphp
                                                    <td class="text-center p-1 {{ $bgColor }} font-semibold"
                                                        style="min-width: 70px;">
                                                        {{ $displayText }}
                                                    </td>
                                                @endfor
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- LEGEND --}}
                            <div class="mt-5 pt-4 border-t border-slate-200 flex flex-wrap gap-5 justify-center">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded bg-primary border border-primary"></span>
                                    <span class="text-xs">Hadir</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded bg-warning border border-warning"></span>
                                    <span class="text-xs">Terlambat</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded bg-danger border border-danger"></span>
                                    <span class="text-xs">Absen / Alpha</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- END: Data List -->
        </div>
    </div>
    <!-- END: Content -->

    <!-- BEGIN: Photo Modal -->
    <div id="photoModal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto" id="modalTitle">Foto Absensi</h2>
                    <button data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-5 bg-slate-100 flex justify-center items-center min-h-[300px]">
                        <img id="modalImage" src="" alt="Foto Absensi"
                            class="max-w-full max-h-[500px] rounded-lg shadow-md">
                    </div>
                    <div class="px-5 pb-5 text-center">
                        <p id="modalDate" class="text-slate-500 text-sm"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Photo Modal -->
@endsection

@push('scripts')
    <script>
        // Filter
        document.getElementById('btnFilter').addEventListener('click', function() {
            let url = new URL(window.location.href);

            @if ($mode == 'hari')
                let date = document.getElementById('filterDate').value;
                if (date) url.searchParams.set('date', date);
                else url.searchParams.delete('date');
            @else
                let month = document.getElementById('filterMonth').value;
                if (month) url.searchParams.set('month', month);
                else url.searchParams.delete('month');
            @endif

            url.searchParams.set('mode', '{{ $mode }}');
            window.location.href = url.toString();
        });

        // Reset
        document.getElementById('btnReset').addEventListener('click', function() {
            let url = new URL(window.location.href);
            @if ($mode == 'hari')
                url.searchParams.delete('date');
            @else
                url.searchParams.delete('month');
            @endif
            url.searchParams.delete('search');
            url.searchParams.set('mode', '{{ $mode }}');
            window.location.href = url.toString();
        });

        // Search
        let searchInput = document.getElementById('searchInput');
        let searchTimeout;
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                let url = new URL(window.location.href);
                if (this.value) {
                    url.searchParams.set('search', this.value);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.set('mode', '{{ $mode }}');
                window.location.href = url.toString();
            }, 500);
        });

        // Show photo modal
        function showPhoto(photoUrl, date, title) {
            const modal = document.getElementById('photoModal');
            const modalImage = document.getElementById('modalImage');
            const modalDate = document.getElementById('modalDate');
            const modalTitle = document.getElementById('modalTitle');

            modalImage.src = photoUrl;
            modalDate.innerText = 'Tanggal: ' + date;
            modalTitle.innerText = title;

            const modalInstance = tailwind.Modal.getOrCreateInstance(modal);
            modalInstance.show();

            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        // Refresh Lucide
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
@endpush

<style>
    .sticky {
        position: sticky;
        left: 0;
        z-index: 10;
    }
</style>
