@extends('layouts.app')

@section('title', 'Daftar Pengguna')

@section('content')
    <div>
        <h2 class="intro-y text-lg font-medium mt-10">
            Daftar Pengguna
        </h2>

        <div class="grid grid-cols-12 gap-6 mt-5">
            {{-- TOOLBAR --}}
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                <button class="btn btn-primary shadow-md mr-2" data-tw-toggle="modal" data-tw-target="#modal-tambah-user">
                    <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Pengguna
                </button>

                <div class="hidden md:block mx-auto text-slate-500">
                    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }}
                    data
                </div>

                <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                    <div class="w-56 relative text-slate-500">
                        <form method="GET" action="{{ route('user.index') }}">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control w-56 box pr-10" placeholder="Search...">
                            <button type="submit" class="absolute my-auto inset-y-0 right-0 mr-3">
                                <i class="w-4 h-4" data-lucide="search"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TABEL USERS --}}
            <div class="intro-y col-span-12 overflow-auto">
                <div class="overflow-x-auto">
                    <table class="table table-report">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="whitespace-nowrap text-center w-10">#</th>
                                <th class="whitespace-nowrap">Username</th>
                                <th class="whitespace-nowrap">Nama</th>
                                <th class="whitespace-nowrap text-center">Level</th>
                                <th class="whitespace-nowrap">Terdaftar</th>
                                <th class="whitespace-nowrap text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr class="intro-x">
                                    <td class="text-center">{{ $users->firstItem() + $index }}</td>
                                    <td>
                                        <div class="font-medium">{{ $user->username }}</div>
                                    </td>
                                    <td>{{ $user->nama }}</td>
                                    <td class="text-center">
                                        @if ($user->level == 'admin')
                                            <span
                                                class="px-2 py-1 bg-primary/20 text-primary rounded-full text-xs">Admin</span>
                                        @elseif ($user->level == 'front office')
                                            <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs">Front
                                                Office</span>
                                        @else
                                            <span
                                                class="px-2 py-1 bg-slate-100 text-slate-600 rounded-full text-xs">Karyawan</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <div class="flex justify-center items-center gap-1">
                                            <button class="btn btn-sm btn-outline-secondary"
                                                onclick="openEdit('{{ $user->id }}', '{{ $user->username }}', '{{ $user->nama }}', '{{ $user->level }}')"
                                                title="Edit">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>

                                            @if ($user->id != auth()->id())
                                                <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                                    class="inline" onsubmit="return confirm('Yakin hapus pengguna ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Hapus">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-slate-500">
                                        <i data-lucide="users" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                                        Belum ada pengguna
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGINATION --}}
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center">
                <nav class="w-full sm:w-auto sm:mr-auto">
                    {{ $users->appends(request()->query())->links() }}
                </nav>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH USER --}}
    <div id="modal-tambah-user" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">
                        <i data-lucide="user-plus" class="w-5 h-5 mr-2 inline"></i>
                        Tambah Pengguna
                    </h2>
                    <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('user.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Level <span class="text-danger">*</span></label>
                            <select name="level" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="front office">Front Office</option>
                                <option value="karyawan">Karyawan</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
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

    {{-- MODAL EDIT USER --}}
    <div id="modal-edit-user" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">
                        <i data-lucide="edit" class="w-5 h-5 mr-2 inline"></i>
                        Edit Pengguna
                    </h2>
                    <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form id="form-edit-user" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Level <span class="text-danger">*</span></label>
                            <select name="level" id="edit_level" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="front office">Front Office</option>
                                <option value="karyawan">Karyawan</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password <span class="text-slate-400">(Kosongkan jika tidak
                                    diubah)</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Password baru">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-24">Batal</button>
                        <button type="submit" class="btn btn-primary w-24">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openEdit(id, username, nama, level) {
                document.getElementById('edit_username').value = username;
                document.getElementById('edit_nama').value = nama;
                document.getElementById('edit_level').value = level;
                document.getElementById('form-edit-user').action = `/users/${id}`;

                // Buka modal
                const modal = tailwind.Modal.getOrCreateInstance(document.getElementById('modal-edit-user'));
                modal.show();
            }
        </script>
    @endpush
@endsection
