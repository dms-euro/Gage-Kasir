@php
    function active($route)
    {
        return request()->routeIs($route) ? 'side-menu--active' : '';
    }

    function isOpen($routes)
    {
        foreach ((array) $routes as $route) {
            if (request()->routeIs($route)) {
                return true;
            }
        }
        return false;
    }
@endphp

<nav class="side-nav">
    <ul>
        @if (auth()->user()->level == 'admin' or auth()->user()->level == 'front office')
            {{-- DASHBOARD --}}
            <li>
                <a href="{{ route('dashboard.index') }}" class="side-menu {{ active('dashboard.*') }}">
                    <div class="side-menu__icon"><i data-lucide="home"></i></div>
                    <div class="side-menu__title">Dashboard</div>
                </a>
            </li>

            {{-- PRODUKSI --}}
            <li>
                <a href="{{ route('produksi.index', ['mode' => 'today']) }}" class="side-menu {{ active('produksi.*') }}">
                    <div class="side-menu__icon"><i data-lucide="shopping-bag"></i></div>
                    <div class="side-menu__title">Produksi</div>
                </a>
            </li>

            {{-- PIUTANG --}}
            <li>
                <a href="{{ route('piutang.index') }}" class="side-menu {{ active('piutang.*') }}">
                    <div class="side-menu__icon"><i data-lucide="credit-card"></i></div>
                    <div class="side-menu__title">Piutang</div>
                </a>
            </li>

            {{-- PELANGGAN --}}
            @php
                $jenisPelanggans = App\Models\JenisPelanggan::get();
            @endphp
            <li class="{{ isOpen('pelanggan.*') ? 'side-menu--active side-menu--open' : '' }}">
                <a href="javascript:;" class="side-menu {{ isOpen('pelanggan.*') ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"><i data-lucide="users"></i></div>
                    <div class="side-menu__title">
                        Pelanggan
                        <div class="side-menu__sub-icon"><i data-lucide="chevron-down"></i></div>
                    </div>
                </a>

                <ul style="{{ isOpen('pelanggan.*') ? 'display:block;' : '' }}">
                    @foreach ($jenisPelanggans as $jenis)
                        <li>
                            <a href="{{ route('pelanggan.index', ['jenis_pelanggan_id' => $jenis->id]) }}"
                                class="side-menu {{ request('jenis_pelanggan_id') == $jenis->id ? 'side-menu--active' : '' }}">
                                <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                                <div class="side-menu__title"> {{ $jenis->nama_jenis }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <li>
                <a href="{{ route('kas.index') }}"
                    class="side-menu {{ request()->routeIs('kas.*') ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"><i data-lucide="book-open"></i></div>
                    <div class="side-menu__title">Kas Buku</div>
                </a>
            </li>
        @endif
        {{-- PENGATURAN (ADMIN ONLY) --}}
        @if (auth()->user()->level == 'admin')
            <li>
                <a href="{{ route('absensi.rekap') }}"
                    class="side-menu {{ request()->routeIs('absensi.*') ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"><i data-lucide="calendar"></i></div>
                    <div class="side-menu__title">Rekap Absensi</div>
                </a>
            </li>
            <li
                class="{{ isOpen(['profile-perusahaan.*', 'user.*', 'jenis-pelanggan.*', 'kategori.*']) ? 'side-menu--active side-menu--open' : '' }}">
                <a href="javascript:;"
                    class="side-menu {{ isOpen(['profile-perusahaan.*', 'user.*', 'jenis-pelanggan.*', 'kategori.*']) ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"><i data-lucide="settings"></i></div>
                    <div class="side-menu__title">
                        Pengaturan
                        <div class="side-menu__sub-icon"><i data-lucide="chevron-down"></i></div>
                    </div>
                </a>

                <ul
                    style="{{ isOpen(['kategori.*', 'profile-perusahaan.*', 'config-absensi.*', 'user.*', 'jenis-pelanggan.*']) ? 'display:block;' : '' }}">
                    <li>
                        <a href="{{ route('kategori.index') }}" class="side-menu {{ active('kategori.*') }}">
                            <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                            <div class="side-menu__title">Kategori Produksi</div>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('jenis-pelanggan.index') }}"
                            class="side-menu {{ active('jenis-pelanggan.*') }}">
                            <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                            <div class="side-menu__title">Kategori Pelanggan</div>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('profile-perusahaan.index') }}"
                            class="side-menu {{ active('profile-perusahaan.*') }}">
                            <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                            <div class="side-menu__title">Profil Perusahaan</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('config-absensi.index') }}"
                            class="side-menu {{ active('config-absensi.index') }}">
                            <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                            <div class="side-menu__title">Konfigurasi Absensi</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profile-perusahaan.index') }}"
                            class="side-menu {{ active('profile-perusahaan.*') }}">
                            <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                            <div class="side-menu__title">Profil Perusahaan</div>
                        </a>
                    </li>


                    <li>
                        <a href="{{ route('user.index') }}" class="side-menu {{ active('user.*') }}">
                            <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                            <div class="side-menu__title">Daftar User</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        {{-- AKUN --}}
        <li>
            <a href="{{ route('me.index') }}" class="side-menu {{ active('me.*') }}">
                <div class="side-menu__icon"><i data-lucide="user"></i></div>
                <div class="side-menu__title">Akun</div>
            </a>
        </li>

        @if (auth()->user()->level == 'karyawan' || auth()->user()->level == 'front office')
            <li>
                <a href="{{ route('absensi.index') }}" class="side-menu {{ active('absensi') }}">
                    <div class="side-menu__icon"><i data-lucide="camera"></i></div>
                    <div class="side-menu__title">Absen Hari Ini</div>
                </a>
            </li>
            <li>
                <a href="{{ route('absensi.riwayat') }}" class="side-menu {{ active('absensi.riwayat') }}">
                    <div class="side-menu__icon"><i data-lucide="history"></i></div>
                    <div class="side-menu__title">Riwayat Absensi</div>
                </a>
            </li>
        @endif


        {{-- LOGOUT --}}
        <li>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="side-menu w-full text-left">
                    <div class="side-menu__icon"><i data-lucide="log-out"></i></div>
                    <div class="side-menu__title">Logout</div>
                </button>
            </form>
        </li>

    </ul>
</nav>
