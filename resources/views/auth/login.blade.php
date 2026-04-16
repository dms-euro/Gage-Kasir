@extends('auth.layouts.app')

@section('title', 'Halaman Login')

@section('content')
    <div class="container sm:px-10">
        <div class="block xl:grid grid-cols-2 gap-4">
            <div class="hidden xl:flex flex-col min-h-screen">
                <div class="my-auto">
                    <img alt="Ilustrasi" class="-intro-x w-1/2 -mt-16"
                        src="{{ asset('templates/Compiled/dist/images/illustration.svg') }}">
                    <div class="-intro-x text-white font-medium text-4xl leading-tight mt-10">
                        Selangkah lagi untuk
                        <br>
                        masuk ke akun Anda.
                    </div>
                </div>
            </div>
            <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                <div
                    class="my-auto mx-auto xl:ml-20 bg-white dark:bg-darkmode-600 xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                    <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">
                        Masuk
                    </h2>
                    <div class="intro-x mt-2 text-slate-400 xl:hidden text-center">
                        Selangkah lagi untuk masuk ke akun Anda.
                    </div>
                    {{-- FORM --}}
                    <form action="{{ route('loginProses') }}" method="POST">
                        @csrf
                        <div class="intro-x mt-8">
                            <input type="text" name="username" value="{{ old('username') }}"
                                class="intro-x login__input form-control py-3 px-4 block" placeholder="Nama Pengguna"
                                required autofocus>
                            <input type="password" name="password"
                                class="intro-x login__input form-control py-3 px-4 block mt-4" placeholder="Kata Sandi"
                                required>
                            <button type="submit" id="login-btn"
                                class="intro-x btn btn-primary py-3 px-4 w-full block mt-4">
                                <span id="btn-text">Masuk</span>
                                <span id="btn-loading" class="hidden">
                                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin inline mr-2"></i> Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.querySelector('form').addEventListener('submit', function() {
                document.getElementById('btn-text').classList.add('hidden');
                document.getElementById('btn-loading').classList.remove('hidden');
                document.getElementById('login-btn').disabled = true;
            });
        </script>
    @endpush
@endsection
