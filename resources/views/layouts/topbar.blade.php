<div
    class="top-bar-boxed top-bar-boxed--simple-menu h-[70px] md:h-[65px] z-[51] border-b border-white/[0.08] mt-12 md:mt-0 -mx-3 sm:-mx-8 md:-mx-0 px-3 md:border-b-0 relative md:fixed md:inset-x-0 md:top-0 sm:px-8 md:px-10 md:pt-10 md:bg-gradient-to-b md:from-slate-100 md:to-transparent dark:md:from-darkmode-700">
    <div class="h-full flex items-center">
        <!-- BEGIN: Logo -->
        <a href="" class="logo -intro-x hidden md:flex xl:w-[180px] block">
            <img alt="Midone - HTML Admin Template" class="logo__image w-6"
                src="{{ asset('templates/Compiled/dist/images/logo.svg') }}">
            <span class="logo__text text-white text-lg ml-3"> Enigma </span>
        </a>
        <nav aria-label="breadcrumb" class="-intro-x h-[45px] mr-auto">
            <ol class="breadcrumb breadcrumb-light">

                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">Dashboard</a>
                </li>

                @if (request()->routeIs('dashboard.*'))
                    <li class="breadcrumb-item active">Dashboard</li>
                @elseif (request()->routeIs('pelanggan.*'))
                    <li class="breadcrumb-item active">Pelanggan</li>
                @elseif(request()->routeIs('produksi.*'))
                    <li class="breadcrumb-item active">Produksi</li>
                @elseif(request()->routeIs('kategori.*'))
                    <li class="breadcrumb-item active">Kategori</li>
                @elseif(request()->routeIs('piutang.*'))
                    <li class="breadcrumb-item active">Piutang</li>
                @elseif(request()->routeIs('user.*'))
                    <li class="breadcrumb-item active">User</li>
                @elseif(request()->routeIs('profile-perusahaan.*'))
                    <li class="breadcrumb-item active">Profil Perusahaan</li>
                @elseif(request()->routeIs('me.*'))
                    <li class="breadcrumb-item active">Akun</li>
                @else
                    <li class="breadcrumb-item active">System</li>
                @endif

            </ol>
        </nav>
        <div class="intro-x dropdown w-8 h-8">
            <div class="dropdown-toggle w-10 h-10 rounded-full overflow-hidden shadow-md flex items-center justify-center bg-slate-200 hover:scale-105 transition"
                role="button" aria-expanded="false" data-tw-toggle="dropdown">
                <i data-lucide="user" class="w-5 h-5 text-slate-600"></i>
            </div>
            <div class="dropdown-menu w-56">
                <ul
                    class="dropdown-content bg-primary/80 before:block before:absolute before:bg-black before:inset-0 before:rounded-md before:z-[-1] text-white">
                    <li class="p-2">
                        <div class="font-medium">{{ auth()->user()->username }}</div>
                        <div class="text-xs text-white/60 mt-0.5 dark:text-slate-500">{{ auth()->user()->level }}</div>
                    </li>
                    <li>
                        <hr class="dropdown-divider border-white/[0.08]">
                    </li>
                    <li>
                        <a href="{{ route('me.index') }}" class="dropdown-item hover:bg-white/5"> <i data-lucide="user"
                                class="w-4 h-4 mr-2"></i> Profile </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider border-white/[0.08]">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item hover:bg-white/5">
                                <i data-lucide="toggle-right" class="w-4 h-4 mr-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
