@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
    <div class="pb-20 md:pb-0">
        <div class="intro-y flex items-center justify-between flex-wrap gap-3 mt-8">
            <h2 class="text-lg font-medium truncate mr-5">Rekap Absensi</h2>
            <div class="flex gap-2">
                <button id="exportPdfBtn" class="btn btn-danger shadow-md">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export PDF
                </button>
            </div>
        </div>

        <!-- BEGIN: Filter -->
        <div class="intro-y box p-5 mt-5">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex gap-2">
                    <a href="{{ route('absensi.rekap', array_merge(request()->all(), ['mode' => 'hari'])) }}"
                        class="btn {{ $mode == 'hari' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2"></i> Harian
                    </a>
                    <a href="{{ route('absensi.rekap', array_merge(request()->all(), ['mode' => 'bulan'])) }}"
                        class="btn {{ $mode == 'bulan' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i data-lucide="calendar-days" class="w-4 h-4 mr-2"></i> Bulanan
                    </a>
                </div>

                <div class="flex-1"></div>

                <div class="flex gap-2">
                    @if ($mode == 'hari')
                        <input type="date" id="filterDate" class="form-control w-56" value="{{ $date }}">
                        <button id="btnFilter" class="btn btn-primary">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i> Filter
                        </button>
                    @else
                        <input type="month" id="filterMonth" class="form-control w-56" value="{{ $month }}">
                        <button id="btnFilterMonth" class="btn btn-primary">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i> Filter
                        </button>
                    @endif

                    <input type="text" id="searchUser" class="form-control w-56" placeholder="Cari karyawan..."
                        value="{{ $search }}">

                    <button id="btnReset" class="btn btn-outline-secondary">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <!-- END: Filter -->

        @if ($mode == 'hari')
            <!-- BEGIN: Tabel Harian -->
            <div class="intro-y box p-5 mt-5">
                <div class="overflow-auto lg:overflow-visible">
                    <table class="table table-report">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">NO</th>
                                <th class="whitespace-nowrap">NAMA KARYAWAN</th>
                                <th class="whitespace-nowrap text-center">STATUS</th>
                                <th class="whitespace-nowrap text-center">CHECK IN</th>
                                <th class="whitespace-nowrap text-center">FOTO IN</th>
                                <th class="whitespace-nowrap text-center">CHECK OUT</th>
                                <th class="whitespace-nowrap text-center">FOTO OUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                @php
                                    $absensi = $data[$user->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="font-medium">{{ $user->nama }}</td>
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
                                                <i data-lucide="{{ $statusIcon }}" class="w-4 h-4 mr-2"></i>
                                                {{ ucfirst($absensi->status) }}
                                            </div>
                                        @else
                                            <span class="text-danger flex items-center justify-center">
                                                <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> Tidak Hadir
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center font-mono">
                                        {{ $absensi && $absensi->check_in_time ? \Carbon\Carbon::parse($absensi->check_in_time)->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($absensi && $absensi->check_in_photo)
                                            <button class="btn btn-sm btn-outline-primary"
                                                onclick="showPhotoModal('{{ asset('storage/' . $absensi->check_in_photo) }}', '{{ $user->nama }}', 'Check In', '{{ \Carbon\Carbon::parse($date)->format('d F Y') }}')">
                                                <i data-lucide="camera" class="w-3 h-3 mr-1"></i> Lihat
                                            </button>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center font-mono">
                                        {{ $absensi && $absensi->check_out_time ? \Carbon\Carbon::parse($absensi->check_out_time)->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($absensi && $absensi->check_out_photo)
                                            <button class="btn btn-sm btn-outline-primary"
                                                onclick="showPhotoModal('{{ asset('storage/' . $absensi->check_out_photo) }}', '{{ $user->nama }}', 'Check Out', '{{ \Carbon\Carbon::parse($date)->format('d F Y') }}')">
                                                <i data-lucide="camera" class="w-3 h-3 mr-1"></i> Lihat
                                            </button>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- END: Tabel Harian -->
        @else
            <!-- BEGIN: Kalender Bulanan -->
            <div class="intro-y box p-5 mt-5">
                <div class="overflow-auto">
                    <table class="table table-report table-bordered">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap sticky left-0 bg-white">NO</th>
                                <th class="whitespace-nowrap sticky left-0 bg-white">NAMA KARYAWAN</th>
                                @for ($day = 1; $day <= $daysInMonth; $day++)
                                    <th class="whitespace-nowrap text-center" style="min-width: 60px;">
                                        {{ $day }}
                                        <div class="text-xs text-slate-400">
                                            {{ \Carbon\Carbon::createFromDate(substr($month, 0, 4), substr($month, 5, 2), $day)->translatedFormat('D') }}
                                        </div>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                @php
                                    $userAbsensi = $absensiMap[$user->id] ?? [];
                                @endphp
                                <tr>
                                    <td class="sticky left-0 bg-white">{{ $loop->iteration }}</td>
                                    <td class="sticky left-0 bg-white font-medium">{{ $user->nama }}</td>
                                    @for ($day = 1; $day <= $daysInMonth; $day++)
                                        @php
                                            $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
                                            $absensiDay = $userAbsensi[$dayStr] ?? null;
                                            $statusColor = '';
                                            $statusText = '';
                                            if ($absensiDay) {
                                                if ($absensiDay->status == 'hadir') {
                                                    $statusColor = 'bg-success/20 text-success';
                                                    $statusText = 'H';
                                                } elseif ($absensiDay->status == 'terlambat') {
                                                    $statusColor = 'bg-warning/20 text-warning';
                                                    $statusText = 'T';
                                                } else {
                                                    $statusColor = 'bg-danger/20 text-danger';
                                                    $statusText = 'A';
                                                }
                                            } else {
                                                $statusColor = 'bg-slate-100 text-slate-400';
                                                $statusText = '-';
                                            }
                                        @endphp
                                        <td class="text-center p-1">
                                            @if ($absensiDay)
                                                <button
                                                    onclick="showDetailModal('{{ $user->nama }}', '{{ $dayStr }}', '{{ $month }}', '{{ $absensiDay->check_in_time }}', '{{ $absensiDay->check_out_time }}', '{{ $absensiDay->status }}', '{{ asset('storage/' . $absensiDay->check_in_photo) }}', '{{ asset('storage/' . $absensiDay->check_out_photo) }}')"
                                                    class="w-8 h-8 rounded-full {{ $statusColor }} hover:opacity-80 transition-all font-semibold text-sm">
                                                    {{ $statusText }}
                                                </button>
                                            @else
                                                <div
                                                    class="w-8 h-8 rounded-full {{ $statusColor }} flex items-center justify-center text-sm">
                                                    {{ $statusText }}
                                                </div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $daysInMonth + 2 }}" class="text-center py-8">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Legend -->
                <div class="flex flex-wrap gap-4 mt-4 pt-3 border-t">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-success/20 border border-success"></div>
                        <span class="text-xs">Hadir (H)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-warning/20 border border-warning"></div>
                        <span class="text-xs">Terlambat (T)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-danger/20 border border-danger"></div>
                        <span class="text-xs">Tidak Hadir (A)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-slate-100 border border-slate-200"></div>
                        <span class="text-xs">Belum Ada Data (-)</span>
                    </div>
                </div>
            </div>
            <!-- END: Kalender Bulanan -->
        @endif
    </div>

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
                    <div id="modalError" class="px-5 pb-2 text-center text-danger text-sm" style="display: none;">
                        Foto tidak ditemukan
                    </div>
                    <div class="px-5 pb-5 text-center">
                        <p id="modalInfo" class="text-slate-500 text-sm"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Photo Modal -->

    <!-- BEGIN: Detail Modal untuk Bulanan -->
    <div id="detailModal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto" id="detailModalTitle">Detail Absensi</h2>
                    <button data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-2 border-b">
                            <span class="text-slate-500">Karyawan:</span>
                            <span class="font-semibold" id="detailUserName"></span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b">
                            <span class="text-slate-500">Tanggal:</span>
                            <span class="font-semibold" id="detailDate"></span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b">
                            <span class="text-slate-500">Status:</span>
                            <span id="detailStatus" class="font-semibold"></span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b">
                            <span class="text-slate-500">Check In:</span>
                            <span class="font-mono" id="detailCheckIn"></span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b">
                            <span class="text-slate-500">Check Out:</span>
                            <span class="font-mono" id="detailCheckOut"></span>
                        </div>
                        <div class="pt-2">
                            <div class="grid grid-cols-2 gap-3">
                                <button id="viewPhotoIn" class="btn btn-outline-primary">
                                    <i data-lucide="camera" class="w-4 h-4 mr-2"></i> Lihat Foto Check In
                                </button>
                                <button id="viewPhotoOut" class="btn btn-outline-primary">
                                    <i data-lucide="camera" class="w-4 h-4 mr-2"></i> Lihat Foto Check Out
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Detail Modal -->

    <form id="exportForm" method="GET" action="{{ route('absensi.export') }}" target="_blank">
        <input type="hidden" name="mode" id="exportMode" value="{{ $mode }}">
        <input type="hidden" name="date" id="exportDate" value="{{ $date ?? '' }}">
        <input type="hidden" name="month" id="exportMonth" value="{{ $month ?? '' }}">
        <input type="hidden" name="search" id="exportSearch" value="{{ $search ?? '' }}">
    </form>
@endsection

@push('scripts')
    <script>
        // Filter Harian
        const btnFilter = document.getElementById('btnFilter');
        const btnFilterMonth = document.getElementById('btnFilterMonth');
        const btnReset = document.getElementById('btnReset');
        const filterDate = document.getElementById('filterDate');
        const filterMonth = document.getElementById('filterMonth');
        const searchUser = document.getElementById('searchUser');

        if (btnFilter) {
            btnFilter.addEventListener('click', function() {
                let url = new URL(window.location.href);
                url.searchParams.set('mode', 'hari');
                if (filterDate.value) url.searchParams.set('date', filterDate.value);
                if (searchUser.value) url.searchParams.set('search', searchUser.value);
                window.location.href = url.toString();
            });
        }

        if (btnFilterMonth) {
            btnFilterMonth.addEventListener('click', function() {
                let url = new URL(window.location.href);
                url.searchParams.set('mode', 'bulan');
                if (filterMonth.value) url.searchParams.set('month', filterMonth.value);
                if (searchUser.value) url.searchParams.set('search', searchUser.value);
                window.location.href = url.toString();
            });
        }

        if (btnReset) {
            btnReset.addEventListener('click', function() {
                window.location.href = "{{ route('absensi.rekap') }}?mode={{ $mode }}";
            });
        }

        // Export PDF
        const exportBtn = document.getElementById('exportPdfBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                const exportForm = document.getElementById('exportForm');
                const mode = '{{ $mode }}';

                if (mode === 'hari') {
                    document.getElementById('exportDate').value = '{{ $date ?? now()->toDateString() }}';
                } else {
                    document.getElementById('exportMonth').value = '{{ $month ?? now()->format('Y-m') }}';
                }
                document.getElementById('exportSearch').value = searchUser ? searchUser.value : '';
                exportForm.submit();
            });
        }

        // Show Photo Modal
        function showPhotoModal(photoUrl, userName, type, date) {
            const modal = document.getElementById('photoModal');
            const modalImage = document.getElementById('modalImage');
            const modalInfo = document.getElementById('modalInfo');
            const modalTitle = document.getElementById('modalTitle');
            const modalError = document.getElementById('modalError');

            if (modalError) modalError.style.display = 'none';
            modalImage.src = photoUrl;
            modalInfo.innerText = `${userName} - ${type} - ${date}`;
            modalTitle.innerText = `Foto ${type}`;

            modalImage.onerror = function() {
                if (modalError) modalError.style.display = 'block';
                modalImage.src =
                    'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%239ca3af" stroke-width="2"%3E%3Crect x="2" y="2" width="20" height="20" rx="2.18"%3E%3C/rect%3E%3Cpath d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"%3E%3C/path%3E%3C/svg%3E';
            };

            if (typeof tailwind !== 'undefined' && tailwind.Modal) {
                const modalInstance = tailwind.Modal.getOrCreateInstance(modal);
                modalInstance.show();
            }

            setTimeout(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }, 100);
        }

        // Show Detail Modal for Bulanan
        let photoInUrl = '',
            photoOutUrl = '';

        function showDetailModal(userName, day, month, checkInTime, checkOutTime, status, checkInPhoto, checkOutPhoto) {
            photoInUrl = checkInPhoto;
            photoOutUrl = checkOutPhoto;

            const modal = document.getElementById('detailModal');
            document.getElementById('detailUserName').innerText = userName;
            document.getElementById('detailDate').innerText = `${day} ${month}`;
            document.getElementById('detailCheckIn').innerText = checkInTime ? checkInTime : '-';
            document.getElementById('detailCheckOut').innerText = checkOutTime ? checkOutTime : '-';

            const statusEl = document.getElementById('detailStatus');
            if (status === 'hadir') {
                statusEl.innerHTML = '<span class="text-success">Hadir</span>';
            } else if (status === 'terlambat') {
                statusEl.innerHTML = '<span class="text-warning">Terlambat</span>';
            } else {
                statusEl.innerHTML = '<span class="text-danger">Tidak Hadir</span>';
            }

            document.getElementById('detailModalTitle').innerText = `Detail Absensi - ${userName}`;

            if (typeof tailwind !== 'undefined' && tailwind.Modal) {
                const modalInstance = tailwind.Modal.getOrCreateInstance(modal);
                modalInstance.show();
            }

            setTimeout(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }, 100);
        }

        // View Photo from Detail Modal
        document.getElementById('viewPhotoIn')?.addEventListener('click', function() {
            if (photoInUrl) {
                showPhotoModal(photoInUrl, document.getElementById('detailUserName').innerText, 'Check In', document
                    .getElementById('detailDate').innerText);
            } else {
                alert('Foto Check In tidak tersedia');
            }
        });

        document.getElementById('viewPhotoOut')?.addEventListener('click', function() {
            if (photoOutUrl) {
                showPhotoModal(photoOutUrl, document.getElementById('detailUserName').innerText, 'Check Out',
                    document.getElementById('detailDate').innerText);
            } else {
                alert('Foto Check Out tidak tersedia');
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
@endpush
