@php
    function active($route)
    {
        return request()->routeIs($route) ? 'side-menu--active' : '';
    }
@endphp

<nav class="side-nav">
    <ul>

        {{-- DASHBOARD --}}
        <li>
            <a href="{{ route('dashboard.index') }}" class="side-menu {{ active('dashboard.*') }}">
                <div class="side-menu__icon"><i data-lucide="home"></i></div>
                <div class="side-menu__title">Dashboard</div>
            </a>
        </li>

        {{-- PELANGGAN --}}
        <li>
            <a href="javascript:;" class="side-menu {{ active('pelanggan.*') }}">
                <div class="side-menu__icon"><i data-lucide="users"></i></div>
                <div class="side-menu__title">
                    Pelanggan
                    <div class="side-menu__sub-icon"><i data-lucide="chevron-down"></i></div>
                </div>
            </a>

            <ul>
                <li>
                    <a href="{{ route('pelanggan.index', ['broker' => 'broker']) }}"
                        class="side-menu {{ request('broker') == 'broker' ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                        <div class="side-menu__title">Broker</div>
                    </a>
                </li>

                <li>
                    <a href="{{ route('pelanggan.index', ['broker' => 'non-broker']) }}"
                        class="side-menu {{ request('broker') == 'non-broker' ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                        <div class="side-menu__title">Non Broker</div>
                    </a>
                </li>

                <li>
                    <a href="{{ route('pelanggan.index', ['broker' => 'kenapajak']) }}"
                        class="side-menu {{ request('broker') == 'kenapajak' ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                        <div class="side-menu__title">Kena Pajak</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- PRODUKSI --}}
        <li>
            <a href="javascript:;" class="side-menu {{ active('produksi.*') || active('kategori.*') }}">
                <div class="side-menu__icon"><i data-lucide="shopping-bag"></i></div>
                <div class="side-menu__title">
                    Produksi
                    <div class="side-menu__sub-icon"><i data-lucide="chevron-down"></i></div>
                </div>
            </a>

            <ul>
                @if (auth()->user()->level == 'admin')
                    <li>
                        <a href="{{ route('kategori.index') }}" class="side-menu {{ active('kategori.*') }}">
                            <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                            <div class="side-menu__title">Kategori</div>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('produksi.index', ['mode' => 'today']) }}"
                        class="side-menu {{ request('mode', 'today') == 'today' ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                        <div class="side-menu__title">Produksi Hari Ini</div>
                    </a>
                </li>

                <li>
                    <a href="{{ route('produksi.index', ['mode' => 'all']) }}"
                        class="side-menu {{ request('mode') == 'all' ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                        <div class="side-menu__title">Semua Produksi</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- PIUTANG --}}
        <li>
            <a href="{{ route('piutang.index') }}" class="side-menu {{ active('piutang.*') }}">
                <div class="side-menu__icon"><i data-lucide="credit-card"></i></div>
                <div class="side-menu__title">Piutang</div>
            </a>
        </li>

        <li class="side-nav__devider my-6"></li>

        @if (auth()->user()->level == 'admin')
            <li>
                <a href="javascript:;" class="side-menu {{ active('profile-perusahaan.*') || active('user.*') }}">
                    <div class="side-menu__icon"><i data-lucide="settings"></i></div>
                    <div class="side-menu__title">
                        Pengaturan
                        <div class="side-menu__sub-icon"><i data-lucide="chevron-down"></i></div>
                    </div>
                </a>

                <ul>
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

        <li>
            <a href="{{ route('me.index') }}" class="side-menu {{ active('me.*') }}">
                <div class="side-menu__icon"><i data-lucide="user"></i></div>
                <div class="side-menu__title">Akun</div>
            </a>
        </li>

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
