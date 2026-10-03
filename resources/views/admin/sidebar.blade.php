<div class="sidebar d-flex flex-column" style="width: 280px;">
    <div class="p-4 border-bottom border-light border-opacity-25 mb-3 text-center">
        <div class="fs-4 fw-bold"><i class="bi bi-shop"></i> Kulu Asri</div>
        <small style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; color: #fde68a; font-size: 12px; display: block; margin-top: 2px;">Jagonya Ikan Bakar!</small>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-3"></i> Dashboard</a>
    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}"><i class="bi bi-box-seam me-3"></i> Produk & Stok</a>
    <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}"><i class="bi bi-tags me-3"></i> Kategori</a>
    <a href="{{ route('tables.index') }}" class="{{ request()->routeIs('tables.*') ? 'active' : '' }}"><i class="bi bi-grid-3x3-gap me-3"></i> Kelola Meja</a>
    <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i class="bi bi-receipt me-3"></i> Laporan Transaksi</a>
    <a href="{{ route('admin.voids') }}" class="{{ request()->routeIs('admin.voids') ? 'active' : '' }}"><i class="bi bi-x-circle me-3"></i> Void Logs</a>
    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="bi bi-people me-3"></i> Manajemen Pengguna</a>
    
    <hr class="border-light opacity-25 mx-4 my-4">
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-warning">
        <i class="bi bi-box-arrow-right me-3"></i> Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <div class="px-3 py-3 text-center mt-auto" style="border-top: 1px solid rgba(255,255,255,0.1);">
        <small style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-size: 11px; color: #e2ece5; opacity: 0.85;">
            Kulu Asri - Jagonya Ikan Bakar!
        </small>
    </div>
</div>
