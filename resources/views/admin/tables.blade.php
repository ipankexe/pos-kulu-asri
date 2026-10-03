<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Meja & QR Code - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #1b5e20 0%, #2e7d32 100%); color: white; box-shadow: 4px 0 20px rgba(0,0,0,0.1); }
        .sidebar a { color: rgba(255,255,255,0.7); text-decoration: none; padding: 12px 25px; display: block; font-weight: 500; border-left: 4px solid transparent; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid #f57c00; }
        .main-content { padding: 40px; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .btn-action-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: none;
            transition: all 0.2s;
        }
        .btn-action-icon:hover { transform: scale(1.1); }
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    @include('admin.sidebar')

    <!-- Main Content -->
    <div class="flex-grow-1 main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Manajemen Meja & QR Self-Order</h2>
                <p class="text-muted small m-0">Kelola nomor meja, status ketersediaan, serta QR Code unik pelanggan.</p>
            </div>
            <button class="btn btn-success fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Meja Baru
            </button>
        </div>

        @if(session('success')) <div class="alert alert-success rounded-3 border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger rounded-3 border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}</div> @endif
        @if($errors->any()) <div class="alert alert-danger rounded-3 border-0 shadow-sm">{{ $errors->first() }}</div> @endif

        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Nama Meja</th>
                            <th class="py-3">Status Meja</th>
                            <th class="py-3">Status QR Meja</th>
                            <th class="py-3">QR Code Akses</th>
                            <th class="text-end px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables as $table)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="fw-bold fs-6 text-dark">{{ $table->name }}</span>
                            </td>
                            <td class="py-3">
                                @if($table->status === 'available')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Tersedia
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-bold">
                                        <i class="bi bi-cup-hot-fill me-1"></i> Terisi (Occupied)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($table->is_active)
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                                        <i class="bi bi-check2 me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-bold">
                                        <i class="bi bi-slash-circle me-1"></i> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#qrPreviewModal{{ $table->id }}">
                                        <i class="bi bi-qr-code-scan me-1"></i> Lihat QR
                                    </button>
                                    <a href="{{ route('tables.print_qr', $table->id) }}" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3 text-dark" title="Cetak Stand Meja">
                                        <i class="bi bi-printer me-1"></i> Cetak
                                    </a>
                                </div>
                            </td>
                            <td class="text-end px-4 py-3">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    <!-- Toggle Active -->
                                    <form action="{{ route('tables.toggle_active', $table->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn-action-icon btn-light {{ $table->is_active ? 'text-secondary' : 'text-success' }}" title="{{ $table->is_active ? 'Nonaktifkan Meja' : 'Aktifkan Meja' }}">
                                            <i class="bi {{ $table->is_active ? 'bi-toggle-on fs-5 text-success' : 'bi-toggle-off fs-5 text-muted' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Regenerate QR Token -->
                                    <form action="{{ route('tables.regenerate_qr', $table->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin meregenerasi QR Code meja ini? QR Code lama tidak akan dapat digunakan lagi.');">
                                        @csrf
                                        <button class="btn-action-icon btn-light text-warning" title="Regenerate QR Token Baru">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>

                                    <!-- Edit -->
                                    <button class="btn-action-icon btn-light text-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $table->id }}" title="Edit Nama Meja">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Delete -->
                                    <form action="{{ route('tables.destroy', $table->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus meja ini?');">
                                        @csrf @method('DELETE')
                                        <button class="btn-action-icon btn-light text-danger" title="Hapus Meja" {{ $table->status === 'occupied' ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Preview QR -->
                        <div class="modal fade" id="qrPreviewModal{{ $table->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4 text-center p-4">
                                    <h5 class="fw-bold mb-1">{{ $table->name }}</h5>
                                    <p class="text-muted small mb-3">Scan untuk membuka menu dan memesan</p>
                                    
                                    <div class="p-3 bg-light rounded-4 d-inline-block mx-auto mb-3 border">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($table->qr_url) }}" 
                                             alt="QR {{ $table->name }}" class="img-fluid rounded-3" style="width: 200px; height: 200px;">
                                    </div>

                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control form-control-sm text-center" value="{{ $table->qr_url }}" readonly id="url-{{ $table->id }}">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="navigator.clipboard.writeText('{{ $table->qr_url }}'); alert('Tautan meja berhasil disalin!');">
                                            <i class="bi bi-clipboard"></i> Salin
                                        </button>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                        <a href="{{ $table->qr_url }}" target="_blank" class="btn btn-primary rounded-pill px-3 fw-bold">
                                            <i class="bi bi-eye-fill me-1"></i> Tes POV Pelanggan
                                        </a>
                                        <a href="{{ route('tables.print_qr', $table->id) }}" target="_blank" class="btn btn-success rounded-pill px-3 fw-bold">
                                            <i class="bi bi-printer-fill me-1"></i> Cetak Stand Meja
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{ $table->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <form action="{{ route('tables.update', $table->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0 bg-light rounded-top-4">
                                            <h5 class="modal-title fw-bold">Edit Meja</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nama Meja</label>
                                                <input type="text" name="name" class="form-control" value="{{ $table->name }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada meja terdaftar.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('tables.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold">Tambah Meja Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama / Nomor Meja</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Meja 01, Meja Lesehan 2, VIP 1" required>
                        <small class="text-muted">QR Code unik akan digenerate otomatis untuk meja ini.</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan & Generate QR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
