<nav class="side-nav">
    <ul>
        <li>
            <a href="{{ route('dashboard.index') }}" class="side-menu side-menu--active">
                <div class="side-menu__icon"> <i data-lucide="home"></i> </div>
                <div class="side-menu__title">
                    Dashboard
                </div>
            </a>
        </li>
        <li>
            <a href="javascript:;" class="side-menu">
                <div class="side-menu__icon"><i data-lucide="users"></i></div>
                <div class="side-menu__title">
                    Pelanggan
                    <div class="side-menu__sub-icon"><i data-lucide="chevron-down"></i></div>
                </div>
            </a>

            <ul>
                <li>
                    <a href="{{ route('pelanggan.index', ['broker' => 'broker']) }}" class="side-menu">
                        <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                        <div class="side-menu__title">Broker</div>
                    </a>
                </li>

                <li>
                    <a href="{{ route('pelanggan.index', ['broker' => 'non-broker']) }}" class="side-menu">
                        <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                        <div class="side-menu__title">Non Broker</div>
                    </a>
                </li>

                <li>
                    <a href="{{ route('pelanggan.index', ['broker' => 'kenapajak']) }}" class="side-menu">
                        <div class="side-menu__icon"><i data-lucide="activity"></i></div>
                        <div class="side-menu__title">Kena Pajak</div>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="javascript:;" class="side-menu">
                <div class="side-menu__icon"> <i data-lucide="shopping-bag"></i> </div>
                <div class="side-menu__title">
                    Produksi
                    <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
                </div>
            </a>
            <ul class="">
                <li>
                    <a href="{{ route('kategori.index') }}" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Kategori </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('produksi.index', ['mode' => 'today']) }}"
                        class="side-menu {{ request('mode', 'today') == 'today' ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Produksi Hari Ini </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('produksi.index', ['mode' => 'all']) }}"
                        class="side-menu {{ request('mode') == 'all' ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Semua Produksi </div>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ route('piutang.index') }}" class="side-menu">
                <div class="side-menu__icon"> <i data-lucide="credit-card"></i> </div>
                <div class="side-menu__title"> Piutang </div>
            </a>
        </li>
        <li class="side-nav__devider my-6"></li>
        <li>
            <a href="javascript:;" class="side-menu">
                <div class="side-menu__icon"> <i data-lucide="settings"></i> </div>
                <div class="side-menu__title">
                    Pengaturan
                    <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
                </div>
            </a>
            <ul class="">
                <li>
                    <a href="{{ route('profile-perusahaan.index') }}" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Profil Perusahaan </div>
                    </a>
                </li>
                <li>
                    <a href="side-menu-light-crud-form.html" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Daftar User </div>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="side-menu-light-point-of-sale.html" class="side-menu">
                <div class="side-menu__icon"> <i data-lucide="user"></i> </div>
                <div class="side-menu__title"> Akun </div>
            </a>
        </li>
        <li>
            <a href="{{ route('logout') }}" class="side-menu">
                <div class="side-menu__icon"> <i data-lucide="log-out"></i> </div>
                <div class="side-menu__title"> Logout </div>
            </a>
        </li>
    </ul>
</nav>
