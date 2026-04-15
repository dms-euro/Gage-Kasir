@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div>
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Profil Saya
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        {{-- LEFT: PROFILE CARD --}}
        <div class="col-span-12 lg:col-span-4 2xl:col-span-3">
            <div class="intro-y box">
                <div class="relative flex items-center p-5">
                    <div class="w-12 h-12 image-fit bg-primary/10 rounded-full flex items-center justify-center">
                        <span class="text-primary font-bold text-xl">{{ substr(auth()->user()->nama, 0, 1) }}</span>
                    </div>
                    <div class="ml-4 mr-auto">
                        <div class="font-medium text-base">{{ auth()->user()->nama }}</div>
                        <div class="text-slate-500 text-sm">{{ auth()->user()->level }}</div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-200/60 dark:border-darkmode-400">
                    <div class="flex items-center">
                        <i data-lucide="user" class="w-4 h-4 mr-2 text-slate-500"></i>
                        <span class="text-slate-500">Username:</span>
                        <span class="ml-2 font-medium">{{ auth()->user()->username }}</span>
                    </div>
                    <div class="flex items-center mt-3">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2 text-slate-500"></i>
                        <span class="text-slate-500">Terdaftar:</span>
                        <span class="ml-2">{{ auth()->user()->created_at->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-200/60 dark:border-darkmode-400">
                    <a href="#profile" class="flex items-center text-primary font-medium">
                        <i data-lucide="user" class="w-4 h-4 mr-2"></i> Data Diri
                    </a>
                    <a href="#password" class="flex items-center mt-5">
                        <i data-lucide="lock" class="w-4 h-4 mr-2"></i> Ubah Password
                    </a>
                </div>
            </div>
        </div>

        {{-- RIGHT: FORMS --}}
        <div class="col-span-12 lg:col-span-8 2xl:col-span-9">

            {{-- FORM DATA DIRI --}}
            <div id="profile" class="intro-y box">
                <div class="flex items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">
                        <i data-lucide="user" class="w-5 h-5 mr-2 inline"></i>
                        Data Diri
                    </h2>
                </div>

                <div class="p-5">
                    <form action="{{ route('me.updateProfile') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control bg-slate-100" value="{{ auth()->user()->username }}" readonly>
                            <small class="text-slate-500">Username tidak dapat diubah</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control"
                                   value="{{ old('nama', auth()->user()->nama) }}" required>
                            @error('nama')
                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Level</label>
                            <input type="text" class="form-control bg-slate-100" value="{{ auth()->user()->level }}" readonly>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            {{-- FORM UBAH PASSWORD --}}
            <div id="password" class="intro-y box mt-5">
                <div class="flex items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">
                        <i data-lucide="lock" class="w-5 h-5 mr-2 inline"></i>
                        Ubah Password
                    </h2>
                </div>

                <div class="p-5">
                    <form action="{{ route('me.updatePassword') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control"
                                   placeholder="Masukkan password lama" required>
                            @error('current_password')
                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Masukkan password baru" required>
                            @error('password')
                                <div class="text-danger text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Ulangi password baru" required>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="key" class="w-4 h-4 mr-2"></i> Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
