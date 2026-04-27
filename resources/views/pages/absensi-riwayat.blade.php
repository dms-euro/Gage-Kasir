@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
    <!-- BEGIN: Content -->
    <div class="pb-20 md:pb-0">
        <h2 class="intro-y text-lg font-medium mt-10 px-4 md:px-0">
            Riwayat Absensi
        </h2>

        <div class="grid grid-cols-12 gap-6 mt-5">
            <!-- BEGIN: Filter -->
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2 px-4 md:px-0">
                <div class="flex flex-wrap gap-2 md:w-auto">
                    <input type="date" id="filterDate" class="form-control flex-1 md:w-56" placeholder="Pilih Tanggal"
                        value="{{ request('date') }}">
                    <button id="btnFilter" class="btn btn-primary shadow-md">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i> Filter
                    </button>
                    <button id="btnReset" class="btn btn-outline-secondary shadow-md">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Reset
                    </button>
                </div>
            </div>
            <!-- END: Filter -->

            <!-- BEGIN: Data List - Desktop Table -->
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible hidden md:block">
                <table class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">TANGGAL</th>
                            <th class="whitespace-nowrap">HARI</th>
                            <th class="whitespace-nowrap text-center">STATUS</th>
                            <th class="whitespace-nowrap text-center">CHECK IN</th>
                            <th class="whitespace-nowrap text-center">CHECK OUT</th>
                            <th class="text-center whitespace-nowrap">FOTO IN</th>
                            <th class="text-center whitespace-nowrap">FOTO OUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item)
                            <tr class="intro-x">
                                <td class="font-mono whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                                    <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
                                        {{ \Carbon\Carbon::parse($item->date)->translatedFormat('F Y') }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->date)->translatedFormat('l') }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusClass = match ($item->status) {
                                            'hadir' => 'text-success',
                                            'terlambat' => 'text-warning',
                                            default => 'text-danger',
                                        };
                                        $statusIcon = match ($item->status) {
                                            'hadir' => 'check-circle',
                                            'terlambat' => 'clock',
                                            default => 'x-circle',
                                        };
                                    @endphp
                                    <div class="flex items-center justify-center {{ $statusClass }}">
                                        <i data-lucide="{{ $statusIcon }}" class="w-4 h-4 mr-2"></i>
                                        {{ ucfirst($item->status) }}
                                    </div>
                                </td>
                                <td class="text-center font-mono whitespace-nowrap">
                                    {{ $item->check_in_time ? \Carbon\Carbon::parse($item->check_in_time)->format('H:i:s') : '-' }}
                                </td>
                                <td class="text-center font-mono whitespace-nowrap">
                                    {{ $item->check_out_time ? \Carbon\Carbon::parse($item->check_out_time)->format('H:i:s') : '-' }}
                                </td>
                                <td class="text-center">
                                    @if ($item->check_in_photo)
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="showPhoto('{{ asset('storage/' . $item->check_in_photo) }}', '{{ \Carbon\Carbon::parse($item->date)->format('d F Y') }}', 'Foto Check In')">
                                            <i data-lucide="camera" class="w-3 h-3 mr-1"></i> Lihat
                                        </button>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->check_out_photo)
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="showPhoto('{{ asset('storage/' . $item->check_out_photo) }}', '{{ \Carbon\Carbon::parse($item->date)->format('d F Y') }}', 'Foto Check Out')">
                                            <i data-lucide="camera" class="w-3 h-3 mr-1"></i> Lihat
                                        </button>
                                    @else
                                        <span class="text-slate-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-500">
                                    <i data-lucide="camera-off" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                                    <p>Belum ada data absensi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- END: Data List - Desktop Table -->

            <!-- BEGIN: Data List - Mobile Cards -->
            <div class="col-span-12 md:hidden">
                <div class="space-y-3 px-4 pb-4">
                    <table class="table table-report -mt-2">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">TANGGAL</th>
                                <th class="whitespace-nowrap text-center">STATUS</th>
                                <th class="text-center whitespace-nowrap">FOTO IN & OUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $item)
                                <tr class="intro-x">
                                    <td class="font-mono whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
                                            {{ \Carbon\Carbon::parse($item->date)->translatedFormat('F Y') }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusClass = match ($item->status) {
                                                'hadir' => 'text-success',
                                                'terlambat' => 'text-warning',
                                                default => 'text-danger',
                                            };
                                            $statusIcon = match ($item->status) {
                                                'hadir' => 'check-circle',
                                                'terlambat' => 'clock',
                                                default => 'x-circle',
                                            };
                                        @endphp
                                        <div class="flex items-center justify-center {{ $statusClass }}">
                                            <i data-lucide="{{ $statusIcon }}" class="w-4 h-4 mr-2"></i>
                                            {{ ucfirst($item->status) }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex gap-2 justify-center items-center">
                                            @if ($item->check_in_photo)
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="showPhoto('{{ asset('storage/' . $item->check_in_photo) }}', '{{ \Carbon\Carbon::parse($item->date)->format('d F Y') }}', 'Foto Check In')">
                                                    <i data-lucide="camera" class="w-3 h-3 mr-1"></i> In
                                                </button>
                                            @else
                                                <span class="text-slate-400 text-xs">-</span>
                                            @endif

                                            @if ($item->check_out_photo)
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="showPhoto('{{ asset('storage/' . $item->check_out_photo) }}', '{{ \Carbon\Carbon::parse($item->date)->format('d F Y') }}', 'Foto Check Out')">
                                                    <i data-lucide="camera" class="w-3 h-3 mr-1"></i> Out
                                                </button>
                                            @else
                                                <span class="text-slate-400 text-xs">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-slate-500">
                                        <i data-lucide="camera-off" class="w-12 h-12 mx-auto mb-3 text-slate-300"></i>
                                        <p>Belum ada data absensi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($data->hasPages())
                    <div class="mt-4 px-4 pb-6">
                        {{ $data->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
            <!-- END: Data List - Mobile Cards -->

            <!-- BEGIN: Pagination -->
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center px-4 md:px-0">
                <nav class="w-full sm:w-auto sm:mr-auto">
                    {{ $data->appends(request()->query())->links() }}
                </nav>
                <div class="text-sm text-slate-500 mt-3 sm:mt-0">
                    Total {{ $data->total() }} data
                </div>
            </div>
            <!-- END: Pagination -->
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
                    <div class="px-5 pb-2 text-center text-danger text-sm" id="modalError" style="display: none;">
                        Foto tidak ditemukan
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
        // Filter by date
        document.getElementById('btnFilter').addEventListener('click', function() {
            let date = document.getElementById('filterDate').value;
            let url = new URL(window.location.href);
            if (date) {
                url.searchParams.set('date', date);
            } else {
                url.searchParams.delete('date');
            }
            window.location.href = url.toString();
        });

        // Reset filter
        document.getElementById('btnReset').addEventListener('click', function() {
            window.location.href = "{{ route('absensi.riwayat') }}";
        });

        // Search input with debounce
        let searchInput = document.getElementById('searchInput');
        let searchTimeout;
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    let url = new URL(window.location.href);
                    if (this.value) {
                        url.searchParams.set('search', this.value);
                    } else {
                        url.searchParams.delete('search');
                    }
                    window.location.href = url.toString();
                }, 500);
            });
        }

        // Show photo modal
        function showPhoto(photoUrl, date, title) {
            const modal = document.getElementById('photoModal');
            const modalImage = document.getElementById('modalImage');
            const modalDate = document.getElementById('modalDate');
            const modalTitle = document.getElementById('modalTitle');
            const modalError = document.getElementById('modalError');

            // Reset
            if (modalError) modalError.style.display = 'none';
            modalImage.src = photoUrl;
            modalDate.innerText = 'Diambil pada: ' + date;
            modalTitle.innerText = title;

            // Handle error
            modalImage.onerror = function() {
                if (modalError) modalError.style.display = 'block';
                modalImage.src =
                    'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%239ca3af" stroke-width="2"%3E%3Crect x="2" y="2" width="20" height="20" rx="2.18"%3E%3C/rect%3E%3Cpath d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 17h5M17 7h5"%3E%3C/path%3E%3C/svg%3E';
            };

            // Show modal with Tailwind/Midone
            if (typeof tailwind !== 'undefined' && tailwind.Modal) {
                const modalInstance = tailwind.Modal.getOrCreateInstance(modal);
                modalInstance.show();
            }

            // Re-init lucide icons
            if (typeof lucide !== 'undefined') {
                setTimeout(() => lucide.createIcons(), 100);
            }
        }

        // Re-init lucide after DOM load
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
@endpush
