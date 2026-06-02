<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Admin</title>
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
    </style>
</head>
<body>
<div class="d-flex">
    <!-- Sidebar -->
    @include('admin.sidebar')

    <!-- Main Content -->
    <div class="flex-grow-1 main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark mb-0">Manajemen Pengguna</h2>
            <button class="btn btn-success fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
            </button>
        </div>

        @if(session('success')) <div class="alert alert-success rounded-3 border-0">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger rounded-3 border-0">{{ session('error') }}</div> @endif
        @if($errors->any()) <div class="alert alert-danger rounded-3 border-0">{{ $errors->first() }}</div> @endif

        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="py-3">Email</th>
                            <th class="py-3 text-center">Role Akses</th>
                            <th class="text-end px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="px-4 py-3 fw-bold">{{ $user->name }}</td>
                            <td class="py-3 text-muted">{{ $user->email }}</td>
                            <td class="py-3 text-center">
                                @if($user->role == 'admin')
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill">Admin</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill">Kasir</span>
                                @endif
                            </td>
                            <td class="text-end px-4 py-3">
                                <button class="btn btn-sm btn-light text-primary me-2 rounded-circle" data-bs-toggle="modal" data-bs-target="#editModal{{ $user->id }}" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @if($user->id != auth()->id())
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger rounded-circle" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0 bg-light rounded-top-4">
                                            <h5 class="modal-title fw-bold">Edit Pengguna</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Role Akses</label>
                                                <select name="role" class="form-select" required>
                                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    <option value="kasir" {{ $user->role == 'kasir' ? 'selected' : '' }}>Kasir</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Password Baru <span class="text-muted fw-normal">(Kosongkan jika tidak ingin diubah)</span></label>
                                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold">Tambah Pengguna Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Role Akses</label>
                        <select name="role" class="form-select" required>
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
