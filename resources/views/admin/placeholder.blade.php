<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #1b5e20 0%, #2e7d32 100%); color: white; }
        .sidebar a { color: rgba(255,255,255,0.7); text-decoration: none; padding: 12px 25px; display: block; font-weight: 500; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" style="width: 280px;">
        <div class="p-4 fs-4 fw-bold border-bottom border-light border-opacity-25 mb-3 text-center"><i class="bi bi-shop"></i> Kulu Asri</div>
        <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-3"></i> Dashboard</a>
        <a href="{{ route('products.index') }}"><i class="bi bi-box-seam me-3"></i> Produk & Stok</a>
        <a href="{{ route('categories.index') }}"><i class="bi bi-tags me-3"></i> Kategori</a>
        <a href="{{ route('admin.reports') }}"><i class="bi bi-receipt me-3"></i> Laporan Transaksi</a>
        <a href="{{ route('admin.voids') }}"><i class="bi bi-x-circle me-3"></i> Void Logs</a>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-5 text-center">
        <h2 class="fw-bold text-dark mb-4">{{ $title }}</h2>
        <div class="card border-0 shadow-sm rounded-4 p-5">
            <div class="card-body py-5">
                <i class="bi bi-tools text-warning mb-3" style="font-size: 4rem;"></i>
                <h4 class="fw-bold">Halaman sedang dalam pengembangan</h4>
                <p class="text-muted">Modul {{ $title }} ini belum diimplementasi secara penuh dalam versi saat ini. Namun tautannya sudah disiapkan (bisa diakses).</p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-success mt-3 rounded-pill px-4">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
