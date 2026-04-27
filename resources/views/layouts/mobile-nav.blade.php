<style>
    .mobile-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 65px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-top: 1px solid #e2e8f0;
        display: flex;
        z-index: 50;
    }

    .mobile-nav a,
    .mobile-nav button {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        font-size: 11px;
        color: #64748b;
        position: relative;
    }

    .mobile-nav i {
        width: 22px;
        height: 22px;
    }

    .mobile-nav .active {
        color: #1e40af;
        font-weight: 600;
    }

    .mobile-nav .active::before {
        content: "";
        position: absolute;
        top: 0;
        width: 28px;
        height: 3px;
        background: #1e40af;
        border-radius: 10px;
    }

    /* 🔥 INI KUNCINYA */
    @media (min-width: 768px) {
        .mobile-nav {
            display: none !important;
        }
    }
</style>

<div class="mobile-nav md:hidden">

    {{-- ABSEN --}}
    <a href="{{ route('absensi.index') }}" class="{{ request()->routeIs('absensi.index') ? 'active' : '' }}">
        <i data-lucide="camera"></i>
        <span>Absen</span>
    </a>

    {{-- RIWAYAT --}}
    <a href="{{ route('absensi.riwayat') }}" class="{{ request()->routeIs('absensi.riwayat') ? 'active' : '' }}">
        <i data-lucide="history"></i>
        <span>Riwayat</span>
    </a>

    {{-- LOGOUT --}}
    <button onclick="openLogoutModal()">
        <i data-lucide="log-out"></i>
        <span>Logout</span>
    </button>

</div>
