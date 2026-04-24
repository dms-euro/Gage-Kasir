@extends('layouts.app')
@section('title', 'Kategori Produksi')
@section('content')
    <div>
        <h2 class="intro-y text-lg font-medium mt-10">
            Categories
        </h2>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                    <button class="btn btn-primary shadow-md mr-2" data-tw-toggle="modal" data-tw-target="#modal-pelanggan-jenis">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Kategori Pelangan Baru
                    </button>
                </div>
                <div class="hidden md:block mx-auto text-slate-500">
                    Menampilkan {{ $jenisPelanggan->count() }} dari {{ $jenisPelanggan->total() }} data kategori
                </div>
                <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                    <div class="w-56 relative text-slate-500">
                        <input type="text" id="searchInput" class="form-control w-56 box pr-10"
                            placeholder="Search..." />
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    </div>
                </div>
            </div>
            <!-- BEGIN: Data List -->
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                <table class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">#</th>
                            <th class="whitespace-nowrap">Nama Kategori Pelanggan</th>
                            <th class="text-center whitespace-nowrap">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jenisPelanggan as $index => $kategori)
                            <tr class="intro-x kategori-row">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $kategori->nama_jenis }}</td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a class="flex items-center mr-3" href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#modal-edit"
                                            onclick="openEdit({{ $kategori->id }}, '{{ $kategori->nama_jenis }}')">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                        </a>
                                        <a class="flex items-center text-danger" href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#delete-confirmation-modal"
                                            onclick="setDelete({{ $kategori->id }})">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center">
                <nav class="w-full sm:w-auto sm:mr-auto">
                    {{ $jenisPelanggan->links() }}
                </nav>
            </div>
        </div>

        {{-- MODAL --}}
        {{-- Tambah --}}
        <div id="modal-pelanggan-jenis" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- HEADER -->
                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">
                            Tambah Kategori Pelanggan
                        </h2>
                    </div>
                    <!-- BODY -->
                    <form action="{{ route('jenis-pelanggan.store') }}" method="POST">
                        @csrf
                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                            <!-- NAMA -->
                            <div class="col-span-12">
                                <label>Nama Kategori Pelanggan</label>
                                <input type="text" name="nama_jenis" class="form-control" placeholder="Kategori Pelanggan">
                            </div>
                        </div>
                        <!-- FOOTER -->
                        <div class="modal-footer text-right">
                            <button type="reset" class="btn btn-outline-secondary mr-1" data-tw-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- Edit --}}
        <div id="modal-edit" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h2 class="font-medium text-base">Edit Kategori Pelanggan</h2>
                    </div>

                    <form id="edit-form" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">
                            <label>Nama Kategori Pelanggan</label>
                            <input type="text" name="nama_jenis" id="edit_nama" class="form-control">
                        </div>

                        <div class="modal-footer">
                            <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary">Batal</button>

                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        {{-- Hapus --}}
        <div id="delete-confirmation-modal" class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="delete-form" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="modal-body p-0">
                            <div class="p-5 text-center">
                                <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                <div class="text-3xl mt-5">Are you sure?</div>
                                <div class="text-slate-500 mt-2">
                                    Data akan dihapus permanen
                                </div>
                            </div>

                            <div class="px-5 pb-8 text-center">
                                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">
                                    Cancel
                                </button>

                                <button type="submit" class="btn btn-danger w-24">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                function openEdit(id, nama) {
                    document.getElementById('edit_nama').value = nama;

                    document.getElementById('edit-form').action =
                        `/jenis-pelanggan/${id}`;
                }
            </script>
            <script>
                function setDelete(id) {
                    let form = document.getElementById('delete-form');
                    form.action = `/jenis-pelanggan/${id}`;
                }
            </script>
            <script>
                document.getElementById('searchInput').addEventListener('keyup', function() {
                    let keyword = this.value.toLowerCase();
                    let rows = document.querySelectorAll('.kategori-row');

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
