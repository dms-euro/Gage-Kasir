@extends('layouts.app')
@section('title', 'Profile Perusahaan')
@section('content')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                Update Profil Perusahaan
            </h2>
        </div>
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 lg:col-span-4 2xl:col-span-3 flex lg:block flex-col-reverse">
                <div class="intro-y box mt-5">
                    <div class="relative flex items-center p-5">
                        <div class="w-12 h-12 image-fit">
                            <img src="{{ asset('storage/' . $profil->logo) }}" class="rounded-full">
                        </div>
                        <div class="ml-4 mr-auto">
                            <div class="font-medium text-base">{{ $profil->nama }}</div>
                            <div class="text-slate-500">{{ $profil->email }}</div>
                        </div>
                    </div>
                    <div class="p-5 border-t border-slate-200/60 dark:border-darkmode-400">
                        <div class="flex items-center text-primary font-medium mt-4"> <i data-lucide="activity"
                                class="w-4 h-4 mr-2"></i> {{ $profil->nama }} </div>
                        <div class="flex items-center text-primary font-medium mt-4" href=""> <i
                                data-lucide="activity" class="w-4 h-4 mr-2"></i> {{ $profil->email }} </div>
                        <div class="flex items-center text-primary font-medium mt-4" href=""> <i
                                data-lucide="activity" class="w-4 h-4 mr-2"></i> {{ $profil->telepon }} </div>
                        <div class="flex items-center text-primary font-medium mt-4" href=""> <i
                                data-lucide="activity" class="w-4 h-4 mr-2"></i> {{ $profil->alamat }} </div>
                        <div class="flex items-center text-primary font-medium mt-4" href=""> <i
                                data-lucide="activity" class="w-4 h-4 mr-2"></i> {{ $profil->no_rekening }} </div>
                    </div>
                </div>
            </div>
            <!-- END: Profile Menu -->
            <div class="col-span-12 lg:col-span-8 2xl:col-span-9">
                <!-- BEGIN: Display Information -->
                <div class="intro-y box lg:mt-5">
                    <div class="flex items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Display Information
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="flex flex-col-reverse xl:flex-row flex-col">
                            <div class="flex-1 mt-6 xl:mt-0">
                                <form action="{{ route('profile-perusahaan.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-12 gap-x-5">
                                        <div class="col-span-12 2xl:col-span-6">
                                            <div>
                                                <label for="update-profile-form-1" class="form-label">Nama
                                                    Perusahaan</label>
                                                <input id="update-profile-form-1" type="text" class="form-control"
                                                    name="nama" placeholder="Input text" value="{{ $profil->nama }}">
                                            </div>
                                            <div class="mt-3">
                                                <label for="update-profile-form-2" class="form-label">Email</label>
                                                <input id="update-profile-form-2" type="text" class="form-control"
                                                    name="email" placeholder="Input text" value="{{ $profil->email }}">
                                            </div>
                                        </div>
                                        <div class="col-span-12 2xl:col-span-6">
                                            <div class="mt-3 2xl:mt-0">
                                                <label for="update-profile-form-3" class="form-label">No Rekening</label>
                                                <input id="update-profile-form-3" type="text" class="form-control"
                                                    name="no_rekening" placeholder="Input text"
                                                    value="{{ $profil->no_rekening }}">
                                            </div>
                                            <div class="mt-3">
                                                <label for="update-profile-form-4" class="form-label">Telepon</label>
                                                <input id="update-profile-form-4" type="text" class="form-control"
                                                    name="telepon" placeholder="Input text" value="{{ $profil->telepon }}">
                                            </div>
                                        </div>
                                        <div class="col-span-12">
                                            <div class="mt-3">
                                                <label for="update-profile-form-5" class="form-label">Alamat</label>
                                                <textarea id="update-profile-form-5" class="form-control" name="alamat" placeholder="Adress">{{ $profil->alamat }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-20 mt-3">Save</button>
                                </form>
                            </div>
                            <div class="w-52 mx-auto xl:mr-0 xl:ml-6">
                                <div class="border-2 border-dashed shadow-sm border-slate-200/60 rounded-md p-5">
                                    <div class="h-40 relative image-fit mx-auto">
                                        <img id="previewImage" class="rounded-md object-cover w-full h-full"
                                            src="{{ $profil->logo ? asset('storage/' . $profil->logo) : asset('dist/images/profile-10.jpg') }}">
                                    </div>
                                    <form action="{{ route('profile-perusahaan.update') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="mt-5 relative">
                                            <button type="button" class="btn btn-primary w-full">
                                                Change Photo
                                            </button>
                                            <input type="file" name="logo"
                                                class="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer"
                                                onchange="previewFile(event)">
                                        </div>
                                        <button type="submit" class="btn btn-success w-full mt-2 text-white">
                                            Simpan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function previewFile(event) {
            const input = event.target;
            const preview = document.getElementById('previewImage');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
