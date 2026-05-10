@extends('layouts.app')
@section('title', 'Pelanggan')
@section('content')
    <div>
        <h2 class="intro-y text-lg font-medium mt-10">
            Daftar Pelanggan
        </h2>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                <button class="btn btn-primary shadow-md mr-2" data-tw-toggle="modal" data-tw-target="#modal-pelanggan">
                    <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Pelanggan Baru
                </button>

                {{-- Filter Jenis Pelanggan --}}
                <div class="w-auto mr-2">
                    <select id="filter-jenis" class="form-select w-40" onchange="window.location.href=this.value">
                        <option value="{{ route('pelanggan.index') }}">Semua Jenis</option>
                        @foreach ($jenisPelanggans as $jenis)
                            <option value="{{ route('pelanggan.index', ['jenis_pelanggan_id' => $jenis->id]) }}"
                                {{ request('jenis_pelanggan_id') == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama_jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="hidden md:block mx-auto text-slate-500">
                    Menampilkan {{ $pelanggans->firstItem() ?? 0 }} - {{ $pelanggans->lastItem() ?? 0 }} dari
                    {{ $pelanggans->total() }} data pelanggan
                </div>
                <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                    <div class="w-56 relative text-slate-500">
                        <form method="GET">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control w-56 box pr-10" placeholder="Search..." />
                            @if (request('jenis_pelanggan_id'))
                                <input type="hidden" name="jenis_pelanggan_id" value="{{ request('jenis_pelanggan_id') }}">
                            @endif
                        </form>
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    </div>
                </div>
            </div>

            <div class="intro-y col-span-12 overflow-auto">
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">#</th>
                                <th class="whitespace-nowrap">ID Pelanggan</th>
                                <th class="whitespace-nowrap">Jenis Pelanggan</th>
                                <th class="whitespace-nowrap">Nama</th>
                                <th class="whitespace-nowrap">CV</th>
                                <th class="whitespace-nowrap">Alamat</th>
                                <th class="whitespace-nowrap">No. HP</th>
                                <th class="whitespace-nowrap text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pelanggans as $pelanggan)
                                <tr class="pelanggan-row">
                                    <td>{{ $loop->iteration + ($pelanggans->firstItem() - 1) }}</td>
                                    <td class="searchable">{{ $pelanggan->id_pelanggan }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match ($pelanggan->jenisPelanggan?->nama_jenis) {
                                                'Broker' => 'badge-info',
                                                'Non Broker' => 'badge-secondary',
                                                'Kena Pajak' => 'badge-warning',
                                                'CSR' => 'badge-success',
                                                default => 'badge-default',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $pelanggan->jenisPelanggan?->nama_jenis ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="searchable">{{ $pelanggan->nama }}</td>
                                    <td class="searchable">{{ $pelanggan->cv ?? '-' }}</td>
                                    <td class="searchable">{{ $pelanggan->alamat ?? '-' }}</td>
                                    <td class="searchable">{{ $pelanggan->no_hp ?? '-' }}</td>
                                    <td>
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Tombol Riwayat Produksi --}}
                                            <a href="{{ route('pelanggan.produksi', $pelanggan->id) }}"
                                                class="btn btn-sm btn-outline-primary shadow-md" title="Riwayat Produksi">
                                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                            </a>

                                            {{-- Tombol Edit --}}
                                            <button class="btn btn-sm btn-outline-warning shadow-md" data-tw-toggle="modal"
                                                data-tw-target="#modal-edit-pelanggan"
                                                onclick="openEdit(
                                                        '{{ $pelanggan->id }}',
                                                        '{{ $pelanggan->nama }}',
                                                        '{{ $pelanggan->cv }}',
                                                        `{{ $pelanggan->alamat }}`,
                                                        '{{ $pelanggan->no_hp }}',
                                                        '{{ $pelanggan->jenis_pelanggan_id }}'
                                                    )">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('pelanggan.destroy', $pelanggan->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus pelanggan ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger shadow-md">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-5">
                <nav class="w-full sm:w-auto sm:mr-auto">
                    {{ $pelanggans->appends(request()->query())->links() }}
                </nav>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Pelanggan --}}
    <div id="modal-pelanggan" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">
                        Tambah Pelanggan
                    </h2>
                    <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form action="{{ route('pelanggan.store') }}" method="POST">
                    @csrf
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">

                        {{-- ID Pelanggan --}}
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">ID Pelanggan</label>
                            <input type="text" class="form-control bg-slate-100" value="{{ $previewId }}" readonly>
                        </div>

                        {{-- Jenis Pelanggan --}}
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Jenis Pelanggan <span class="text-danger">*</span></label>
                            <select name="jenis_pelanggan_id" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                @foreach ($jenisPelanggans as $jenis)
                                    <option value="{{ $jenis->id }}"
                                        {{ old('jenis_pelanggan_id') == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_jenis }}
                                        @if ($jenis->keterangan)
                                            - {{ $jenis->keterangan }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nama --}}
                        <div class="col-span-12">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama pelanggan"
                                value="{{ old('nama') }}" required>
                        </div>

                        {{-- CV --}}
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">CV / Perusahaan</label>
                            <input type="text" name="cv" class="form-control" placeholder="Nama CV"
                                value="{{ old('cv') }}">
                        </div>

                        {{-- No HP / WhatsApp --}}
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">No HP / WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-100 font-medium">+62</span>
                                <input type="text" name="no_hp" class="form-control" placeholder="81234567890"
                                    value="{{ old('no_hp') }}" pattern="[0-9]*" maxlength="13"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <small class="text-slate-400 mt-1 block">
                                Masukkan nomor tanpa 0 depan. Contoh:81234567890
                            </small>
                        </div>

                        {{-- Alamat --}}
                        <div class="col-span-12">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap" required>{{ old('alamat') }}</textarea>
                        </div>

                    </div>
                    <div class="modal-footer text-right">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary mr-1">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" class="w-4 h-4 mr-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Pelanggan --}}
    <div id="modal-edit-pelanggan" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base">Edit Pelanggan</h2>
                    <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form id="form-edit" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">

                        {{-- Jenis Pelanggan --}}
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Jenis Pelanggan <span class="text-danger">*</span></label>
                            <select name="jenis_pelanggan_id" id="edit_jenis_pelanggan_id" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                @foreach ($jenisPelanggans as $jenis)
                                    <option value="{{ $jenis->id }}">
                                        {{ $jenis->nama_jenis }}
                                        @if ($jenis->keterangan)
                                            - {{ $jenis->keterangan }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- No HP / WhatsApp --}}
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">No HP / WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-100 font-medium">+62</span>
                                <input type="text" name="no_hp" id="edit_no_hp" class="form-control"
                                    placeholder="81234567890" pattern="[0-9]*" maxlength="13"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                            </div>
                            <small class="text-slate-400 mt-1 block">
                                Masukkan nomor tanpa 0 depan. Contoh:81234567890
                            </small>
                        </div>

                        {{-- Nama --}}
                        <div class="col-span-12">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama" class="form-control" required>
                        </div>

                        {{-- CV --}}
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">CV / Perusahaan</label>
                            <input type="text" name="cv" id="edit_cv" class="form-control"
                                placeholder="Nama CV">
                        </div>

                        {{-- Alamat --}}
                        <div class="col-span-12 sm:col-span-6">
                            <label class="form-label">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" id="edit_alamat" class="form-control" rows="2" required></textarea>
                        </div>

                    </div>
                    <div class="modal-footer text-right">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" class="w-4 h-4 mr-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openEdit(id, nama, cv, alamat, no_hp, jenis_pelanggan_id) {
                document.getElementById('edit_nama').value = nama || '';
                document.getElementById('edit_cv').value = cv || '';
                document.getElementById('edit_alamat').value = alamat || '';

                // Bersihkan nomor HP (tampilkan tanpa +62 di form)
                let cleanNo = (no_hp || '').replace(/[^0-9]/g, '');
                if (cleanNo.startsWith('62')) {
                    cleanNo = cleanNo.substring(2);
                }
                if (cleanNo.startsWith('0')) {
                    cleanNo = cleanNo.substring(1);
                }
                document.getElementById('edit_no_hp').value = cleanNo;

                document.getElementById('edit_jenis_pelanggan_id').value = jenis_pelanggan_id || '';

                document.getElementById('form-edit').action = '/pelanggan/' + id;
            }
        </script>
    @endpush
@endsection
