<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Void Logs - Admin</title>
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
        <h2 class="fw-bold text-dark mb-4">Log Pembatalan (Void)</h2>

        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Waktu Batal</th>
                            <th class="py-3">Kasir Pembatal</th>
                            <th class="py-3">Alasan Void</th>
                            <th class="py-3">Info Transaksi</th>
                            <th class="py-3 text-end px-4">Nilai Struk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($voids as $void)
                        <tr>
                            <td class="px-4 py-3 text-danger fw-semibold">{{ $void->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 fw-bold">{{ $void->void_by }}</td>
                            <td class="py-3 fst-italic">"{{ $void->reason }}"</td>
                            <td class="py-3">
                                TRX ID: #{{ $void->transaction_id }}<br>
                                <small class="text-muted">{{ $void->transaction->customer_name ?? 'Unknown' }} (Meja {{ $void->transaction->table_number ?? '-' }})</small>
                            </td>
                            <td class="py-3 text-end px-4 fw-bold">Rp {{ number_format($void->transaction->total ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada riwayat pembatalan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($voids->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $voids->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
