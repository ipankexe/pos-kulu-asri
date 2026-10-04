<!-- Mobile Top Navigation Bar (< 992px) -->
<header class="ka-mobile-topbar d-flex d-lg-none align-items-center justify-content-between px-3 py-2 sticky-top shadow-sm" style="background: linear-gradient(135deg, #02241b 0%, #064e3b 100%); border-bottom: 1px solid rgba(255,255,255,0.12); z-index: 1020; min-height: 58px; width: 100%;">
    <!-- Left: Hamburger toggle & Restaurant Brand -->
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm text-white p-2 d-flex align-items-center justify-content-center rounded-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas" aria-controls="adminSidebarOffcanvas" style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2); width: 40px; height: 40px;" aria-label="Buka Menu Manajemen">
            <i class="bi bi-list fs-4"></i>
        </button>
        <div class="d-flex align-items-center gap-2">
            <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-15 rounded-2 p-1" style="width: 32px; height: 32px;">
                <i class="bi bi-shop text-warning fs-6"></i>
            </div>
            <div>
                <div class="fw-bolder text-white lh-1" style="font-size: 15px; letter-spacing: -0.01em;">Kulu Asri</div>
                <small style="font-size: 10px; color: #fde68a; font-style: italic; font-weight: 600;">Jagonya Ikan Bakar!</small>
            </div>
        </div>
    </div>

    <!-- Right: Quick Jump to POS & User Avatar -->
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('pos.index') }}" class="btn btn-sm fw-bold text-white d-flex align-items-center gap-1 px-3 py-1.5 rounded-pill shadow-sm" style="background: linear-gradient(135deg, #d97706 0%, #ea580c 100%); font-size: 11.5px; text-decoration: none;">
            <i class="bi bi-display"></i>
            <span>POS Kasir</span>
        </a>
        <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 34px; height: 34px; font-size: 13px; border: 1px solid rgba(255,255,255,0.25);" title="{{ auth()->user()->name ?? 'Admin' }}">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        </div>
    </div>
</header>

<!-- Desktop Sidebar (>= 992px) -->
<aside class="sidebar d-none d-lg-flex flex-column" style="width: 280px; min-height: 100vh; height: 100vh; position: sticky; top: 0; background: linear-gradient(180deg, #032d20 0%, #064e3b 55%, #02241b 100%); color: white; border-right: 1px solid rgba(255,255,255,0.08); box-shadow: 4px 0 25px rgba(0,0,0,0.12); flex-shrink: 0; z-index: 100; overflow-y: auto;">
    <!-- Brand Identity Header -->
    <div class="p-4 border-bottom border-light border-opacity-10 text-center position-relative">
        <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 rounded-3 p-2 mb-2 shadow-sm border border-light border-opacity-25" style="width: 48px; height: 48px;">
            <i class="bi bi-shop fs-4 text-warning"></i>
        </div>
        <div class="fs-4 fw-bolder tracking-tight text-white mb-0" style="letter-spacing: -0.02em;">Kulu Asri</div>
        <small style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; color: #fde68a; font-size: 12px; display: inline-block; margin-top: 2px; font-weight: 600; letter-spacing: 0.3px;">
            Jagonya Ikan Bakar!
        </small>
    </div>

    <!-- Quick Jump to POS Kasir -->
    <div class="px-3 pt-3 pb-2">
        <a href="{{ route('pos.index') }}" class="d-flex align-items-center justify-content-between text-decoration-none px-3 py-2 rounded-3 text-white fw-bold shadow-sm" style="background: linear-gradient(135deg, #d97706 0%, #ea580c 100%); font-size: 13px; transition: all 0.2s ease;">
            <span class="d-flex align-items-center gap-2">
                <i class="bi bi-display fs-6"></i> Buka Terminal POS
            </span>
            <i class="bi bi-arrow-right-short fs-5"></i>
        </a>
    </div>

    <!-- Navigation Menu Items -->
    <div class="nav-menu-scroll px-3 py-2 flex-grow-1" style="overflow-y: auto;">
        <!-- Section: Overview -->
        <div class="text-uppercase px-3 pt-3 pb-1" style="font-size: 10px; font-weight: 700; letter-spacing: 1px; color: rgba(255,255,255,0.4);">
            Ringkasan
        </div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <!-- Section: Resto Operations -->
        <div class="text-uppercase px-3 pt-3 pb-1" style="font-size: 10px; font-weight: 700; letter-spacing: 1px; color: rgba(255,255,255,0.4);">
            Katalog & Resto
        </div>
        <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span>Produk & Stok</span>
        </a>
        <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i>
            <span>Kategori Menu</span>
        </a>
        <a href="{{ route('tables.index') }}" class="sidebar-link {{ request()->routeIs('tables.*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i>
            <span>Kelola Meja & QR</span>
        </a>

        <!-- Section: Reports & Audits -->
        <div class="text-uppercase px-3 pt-3 pb-1" style="font-size: 10px; font-weight: 700; letter-spacing: 1px; color: rgba(255,255,255,0.4);">
            Laporan & Audit
        </div>
        <a href="{{ route('admin.reports') }}" class="sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i>
            <span>Laporan Transaksi</span>
        </a>
        <a href="{{ route('admin.voids') }}" class="sidebar-link {{ request()->routeIs('admin.voids') ? 'active' : '' }}">
            <i class="bi bi-shield-x"></i>
            <span>Void Audit Logs</span>
        </a>

        <!-- Section: Settings -->
        <div class="text-uppercase px-3 pt-3 pb-1" style="font-size: 10px; font-weight: 700; letter-spacing: 1px; color: rgba(255,255,255,0.4);">
            Konfigurasi
        </div>
        <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span>Manajemen Pengguna</span>
        </a>
    </div>

    <!-- Bottom User Profile & Logout -->
    <div class="p-3 mt-auto border-top border-light border-opacity-10" style="background: rgba(0,0,0,0.15);">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 36px; height: 36px; font-size: 14px; flex-shrink: 0;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="text-truncate">
                    <div class="fw-bold text-white text-truncate" style="font-size: 13px;">{{ auth()->user()->name ?? 'Administrator' }}</div>
                    <small class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 px-2 py-0" style="font-size: 10px;">
                        {{ ucfirst(auth()->user()->role ?? 'admin') }}
                    </small>
                </div>
            </div>
            
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-sm btn-outline-light border-opacity-25 p-2 rounded-3 text-warning" title="Keluar / Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>
</aside>

<!-- Mobile Offcanvas Drawer (< 992px) -->
<div class="offcanvas offcanvas-start text-white d-lg-none" tabindex="-1" id="adminSidebarOffcanvas" aria-labelledby="adminSidebarOffcanvasLabel" style="width: 290px; background: linear-gradient(180deg, #032d20 0%, #064e3b 55%, #02241b 100%); border-right: 1px solid rgba(255,255,255,0.12); z-index: 1055;">
    <!-- Offcanvas Header -->
    <div class="offcanvas-header p-3 border-bottom border-light border-opacity-10 align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-15 rounded-3 p-2" style="width: 40px; height: 40px; border: 1px solid rgba(255,255,255,0.2);">
                <i class="bi bi-shop fs-5 text-warning"></i>
            </div>
            <div>
                <h6 class="offcanvas-title fw-bolder text-white mb-0" id="adminSidebarOffcanvasLabel" style="font-size: 16px;">Kulu Asri</h6>
                <small style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; color: #fde68a; font-size: 11px; font-weight: 600;">Jagonya Ikan Bakar!</small>
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>

    <!-- Quick Jump to POS Kasir -->
    <div class="px-3 pt-3 pb-2">
        <a href="{{ route('pos.index') }}" class="d-flex align-items-center justify-content-between text-decoration-none px-3 py-2.5 rounded-3 text-white fw-bold shadow-sm" style="background: linear-gradient(135deg, #d97706 0%, #ea580c 100%); font-size: 13px;">
            <span class="d-flex align-items-center gap-2">
                <i class="bi bi-display fs-6"></i> Buka Terminal POS
            </span>
            <i class="bi bi-arrow-right-short fs-5"></i>
        </a>
    </div>

    <!-- Navigation Menu Items (Scrollable) -->
    <div class="offcanvas-body nav-menu-scroll px-3 py-2 flex-grow-1" style="overflow-y: auto;">
        <!-- Section: Overview -->
        <div class="text-uppercase px-3 pt-2 pb-1" style="font-size: 10px; font-weight: 700; letter-spacing: 1px; color: rgba(255,255,255,0.45);">
            Ringkasan
        </div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <!-- Section: Resto Operations -->
        <div class="text-uppercase px-3 pt-3 pb-1" style="font-size: 10px; font-weight: 700; letter-spacing: 1px; color: rgba(255,255,255,0.45);">
            Katalog & Resto
        </div>
        <a href="{{ route('products.index') }}" class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span>Produk & Stok</span>
        </a>
        <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i>
            <span>Kategori Menu</span>
        </a>
        <a href="{{ route('tables.index') }}" class="sidebar-link {{ request()->routeIs('tables.*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i>
            <span>Kelola Meja & QR</span>
        </a>

        <!-- Section: Reports & Audits -->
        <div class="text-uppercase px-3 pt-3 pb-1" style="font-size: 10px; font-weight: 700; letter-spacing: 1px; color: rgba(255,255,255,0.45);">
            Laporan & Audit
        </div>
        <a href="{{ route('admin.reports') }}" class="sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i>
            <span>Laporan Transaksi</span>
        </a>
        <a href="{{ route('admin.voids') }}" class="sidebar-link {{ request()->routeIs('admin.voids') ? 'active' : '' }}">
            <i class="bi bi-shield-x"></i>
            <span>Void Audit Logs</span>
        </a>

        <!-- Section: Settings -->
        <div class="text-uppercase px-3 pt-3 pb-1" style="font-size: 10px; font-weight: 700; letter-spacing: 1px; color: rgba(255,255,255,0.45);">
            Konfigurasi
        </div>
        <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span>Manajemen Pengguna</span>
        </a>
    </div>

    <!-- Bottom User Profile & Logout -->
    <div class="p-3 border-top border-light border-opacity-10" style="background: rgba(0,0,0,0.2);">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 36px; height: 36px; font-size: 14px; flex-shrink: 0;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="text-truncate">
                    <div class="fw-bold text-white text-truncate" style="font-size: 13px;">{{ auth()->user()->name ?? 'Administrator' }}</div>
                    <small class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-25 px-2 py-0" style="font-size: 10px;">
                        {{ ucfirst(auth()->user()->role ?? 'admin') }}
                    </small>
                </div>
            </div>
            
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();" class="btn btn-sm btn-outline-light border-opacity-25 p-2 rounded-3 text-warning" title="Keluar / Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>
</div>

<style>
    .sidebar-link {
        color: rgba(241, 245, 249, 0.78);
        text-decoration: none;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        font-size: 13.5px;
        border-radius: 10px;
        margin-bottom: 3px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 3px solid transparent;
        min-height: 44px; /* Touch target accessibility standard */
    }
    .sidebar-link i {
        font-size: 1.15rem;
        opacity: 0.85;
    }
    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        transform: translateX(3px);
    }
    .sidebar-link.active {
        background: rgba(255, 255, 255, 0.14);
        color: #ffffff;
        font-weight: 700;
        border-left: 3px solid #fbbf24;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .sidebar-link.active i {
        color: #fbbf24;
        opacity: 1;
    }

    /* Mobile & Tablet Responsiveness */
    @media (max-width: 991.98px) {
        body > .d-flex,
        .d-flex:has(> .ka-mobile-topbar),
        .d-flex:has(> .sidebar) {
            flex-direction: column !important;
            flex-wrap: wrap !important;
        }
        .sidebar {
            display: none !important;
        }
        .ka-mobile-topbar {
            width: 100% !important;
            flex-shrink: 0 !important;
        }
        .main-content {
            width: 100% !important;
            max-width: 100vw !important;
            padding: 16px 14px !important;
            box-sizing: border-box !important;
        }
    }
    @media (min-width: 576px) and (max-width: 991.98px) {
        .main-content {
            padding: 24px 20px !important;
        }
    }
</style>

<script>
    (function() {
        function adjustLayoutForMobile() {
            var isMobile = window.innerWidth < 992;
            var flexParents = document.querySelectorAll('.d-flex');
            flexParents.forEach(function(el) {
                if (el.querySelector('.ka-mobile-topbar') || el.querySelector('.sidebar')) {
                    if (isMobile) {
                        el.classList.add('flex-column');
                    } else {
                        el.classList.remove('flex-column');
                    }
                }
            });
        }
        window.addEventListener('resize', adjustLayoutForMobile);
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', adjustLayoutForMobile);
        } else {
            adjustLayoutForMobile();
        }
    })();
</script>
