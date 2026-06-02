<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - POS Kulu Asri</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success"><i class="bi bi-clock-history"></i> Riwayat Transaksi</h3>
        <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Kasir</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4">Waktu</th>
                        <th>No. TRX</th>
                        <th>No Meja</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        <td class="px-4">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge bg-secondary">{{ $trx->transaction_number ?? '-' }}</span></td>
                        <td>{{ $trx->table_number }}</td>
                        <td>{{ $trx->customer_name }}</td>
                        <td>Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                        <td>
                            @if($trx->status == 'paid')
                                <span class="badge bg-success">PAID</span>
                            @elseif($trx->status == 'unpaid')
                                <span class="badge bg-warning text-dark">BELUM BAYAR</span>
                            @else
                                <span class="badge bg-danger">VOID</span>
                                <div class="small text-muted mt-1" title="Alasan: {{ $trx->voidLog->reason ?? '-' }}">
                                    Oleh: {{ $trx->voidLog->void_by ?? '-' }}
                                </div>
                            @endif
                        </td>
                        <td class="text-end px-4">
                            @if($trx->status == 'paid' || $trx->status == 'unpaid')
                            <button class="btn btn-sm btn-danger" onclick="confirmVoid({{ $trx->id }})">VOID</button>
                            <a href="{{ route('pos.receipt', $trx->id) }}" target="_blank" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-printer"></i> Struk
                            </a>
                            <a href="{{ route('pos.kitchen_ticket', $trx->id) }}" target="_blank" class="btn btn-sm btn-secondary text-white">
                                <i class="bi bi-printer"></i> Dapur
                            </a>
                            @endif
                            
                            <form id="void-form-{{ $trx->id }}" action="{{ route('pos.void', $trx->id) }}" method="POST" class="d-none">
                                @csrf
                                <input type="hidden" name="reason" id="void-reason-{{ $trx->id }}">
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
