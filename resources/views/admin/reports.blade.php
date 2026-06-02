<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Admin</title>
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
            <h2 class="fw-bold text-dark mb-0">Laporan Transaksi Kasir</h2>
            
            <div class="d-flex gap-3">
                <form action="{{ route('admin.reports') }}" method="GET" class="d-flex align-items-center" id="filterForm">
                    <input type="date" name="custom_date" id="customDateInput" class="form-control rounded-pill border-success text-success fw-bold shadow-sm me-2 {{ ($filter ?? 'all') == 'custom_date' ? '' : 'd-none' }}" value="{{ $customDate ?? \Carbon\Carbon::now()->toDateString() }}" onchange="document.getElementById('filterForm').submit()" style="max-width: 150px;">
                    <select name="filter" id="filterSelect" class="form-select rounded-pill border-success text-success fw-bold shadow-sm me-2" onchange="toggleDateInput()" style="cursor: pointer; width: 160px;">
                        <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                        <option value="daily" {{ $filter == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="weekly" {{ $filter == 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="monthly" {{ $filter == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="yearly" {{ $filter == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom_date" {{ $filter == 'custom_date' ? 'selected' : '' }}>Tanggal Spesifik</option>
                    </select>
                </form>

                <a href="{{ route('admin.reports.export', ['filter' => $filter, 'custom_date' => $customDate ?? '']) }}" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">
                    <i class="bi bi-file-earmark-excel me-2"></i> Export Excel
                </a>
            </div>
        </div>

        <div class="card card-custom overflow-hidden">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Tanggal / Waktu</th>
                            <th class="py-3">Kasir</th>
                            <th class="py-3">Pelanggan</th>
                            <th class="py-3">Meja</th>
                            <th class="py-3 text-end">Total Belanja</th>
                            <th class="py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td class="px-4 py-3">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 fw-semibold">{{ $trx->user->name ?? '-' }}</td>
                            <td class="py-3">{{ $trx->customer_name }}</td>
                            <td class="py-3 text-center">{{ $trx->table_number }}</td>
                            <td class="py-3 text-end fw-bold">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                            <td class="py-3 text-center">
                                @if($trx->status == 'paid')
                                    <span class="badge bg-success rounded-pill px-3 py-1">SUKSES</span>
                                    <button class="btn btn-sm btn-outline-danger ms-2 rounded-pill" onclick="confirmVoid({{ $trx->id }})">VOID</button>
                                    <form id="void-form-{{ $trx->id }}" action="{{ route('admin.void', $trx->id) }}" method="POST" class="d-none">
                                        @csrf
                                        <input type="hidden" name="reason" id="void-reason-{{ $trx->id }}">
                                    </form>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3 py-1">VOID</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi terekam.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleDateInput() {
        const select = document.getElementById('filterSelect');
        const dateInput = document.getElementById('customDateInput');
        if (select.value === 'custom_date') {
            dateInput.classList.remove('d-none');
            // Do not submit immediately, wait for user to pick date
        } else {
            dateInput.classList.add('d-none');
            document.getElementById('filterForm').submit();
        }
    }

    function confirmVoid(id) {
        Swal.fire({
            title: 'Void Transaksi?',
            html: '<input id="swal-input-reason" class="swal2-input" placeholder="Contoh: Salah input pesanan" style="max-width: 100%;">',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Void',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            didOpen: () => {
                document.getElementById('swal-input-reason').focus();
            },
            preConfirm: () => {
                const reason = document.getElementById('swal-input-reason').value;
                if (!reason || reason.trim() === '') {
                    Swal.showValidationMessage('Alasan VOID wajib diisi!')
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('void-reason-' + id).value = result.value;
                document.getElementById('void-form-' + id).submit();
            }
        });
    }
</script>
</body>
</html>
