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
        <div>
            <h3 class="fw-bold text-success m-0"><i class="bi bi-clock-history"></i> Riwayat Transaksi</h3>
            <small style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; color: #b45309; font-weight: 700;">Kulu Asri - Jagonya Ikan Bakar!</small>
        </div>
        <a href="{{ route('pos.index') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-arrow-left me-1"></i> Kembali ke Kasir</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
    @endif

    <!-- Summary Pendapatan Hari Ini -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-success">
                <span class="text-muted small fw-bold">TOTAL OMSET HARI INI</span>
                <h4 class="fw-bolder text-success mb-1">Rp {{ number_format($todaySummary['total'] ?? 0, 0, ',', '.') }}</h4>
                <small class="text-muted">{{ $todaySummary['count'] ?? 0 }} Transaksi Lunas</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-primary">
                <span class="text-muted small fw-bold">DARI POS KASIR</span>
                <h5 class="fw-bold text-primary mb-1">Rp {{ number_format($todaySummary['pos'] ?? 0, 0, ',', '.') }}</h5>
                <small class="text-muted">{{ $todaySummary['pos_count'] ?? 0 }} Transaksi Kasir</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-warning">
                <span class="text-muted small fw-bold">DARI QR SELF-ORDER</span>
                <h5 class="fw-bold text-warning-emphasis mb-1">Rp {{ number_format($todaySummary['qr'] ?? 0, 0, ',', '.') }}</h5>
                <small class="text-muted">{{ $todaySummary['qr_count'] ?? 0 }} Transaksi Meja</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-info">
                <span class="text-muted small fw-bold">RINCIAN METODE</span>
                <div class="small mt-1">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Cash:</span>
                        <span class="fw-bold">Rp {{ number_format($todaySummary['cash'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">QRIS:</span>
                        <span class="fw-bold">Rp {{ number_format($todaySummary['qris'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Filter -->
    <form method="GET" action="{{ route('pos.history') }}" class="row g-3 mb-4 align-items-end bg-white p-3 rounded-3 shadow-sm border-0 mx-0">
        <div class="col-md-3">
            <label for="filter_type" class="form-label fw-semibold text-muted">Urutkan Berdasarkan</label>
            <select class="form-select" id="filter_type" name="filter_type" onchange="toggleFilterInputs()">
                <option value="date" {{ request('filter_type', 'date') == 'date' ? 'selected' : '' }}>Tanggal</option>
                <option value="month" {{ request('filter_type') == 'month' ? 'selected' : '' }}>Bulan</option>
                <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>Tahun</option>
            </select>
        </div>
        
        <div class="col-md-3" id="input_date_container">
            <label for="filter_date" class="form-label fw-semibold text-muted">Pilih Tanggal</label>
            <input type="date" class="form-control" id="filter_date" name="date" value="{{ request('date', request('filter_type') == 'date' ? date('Y-m-d') : date('Y-m-d')) }}">
        </div>

        <div class="col-md-3 d-none" id="input_month_container">
            <label for="filter_month" class="form-label fw-semibold text-muted">Pilih Bulan</label>
            <input type="month" class="form-control" id="filter_month" name="month" value="{{ request('month', date('Y-m')) }}">
        </div>

        <div class="col-md-3 d-none" id="input_year_container">
            <label for="filter_year" class="form-label fw-semibold text-muted">Pilih Tahun</label>
            <select class="form-select" id="filter_year" name="year">
                @php
                    $currentYear = date('Y');
                @endphp
                @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                    <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-success flex-grow-1"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('pos.history') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
        </div>
    </form>

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
                        <td>
                            <span class="badge bg-secondary font-monospace">{{ $trx->transaction_number ?? '-' }}</span>
                            <div class="mt-1 d-flex gap-1 flex-wrap">
                                @if($trx->order_source === 'qr')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 11px;"><i class="bi bi-qr-code me-1"></i>QR Meja</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 11px;"><i class="bi bi-shop me-1"></i>POS</span>
                                @endif
                                <span class="badge bg-light text-dark border" style="font-size: 11px;">{{ $trx->payment_method ?? 'Cash' }}</span>
                            </div>
                        </td>
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
                            <a href="{{ route('pos.kitchen_ticket', [$trx->id, 'all' => 1]) }}" target="_blank" class="btn btn-sm btn-secondary text-white">
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
    function toggleFilterInputs() {
        const filterType = document.getElementById('filter_type').value;
        const dateContainer = document.getElementById('input_date_container');
        const monthContainer = document.getElementById('input_month_container');
        const yearContainer = document.getElementById('input_year_container');
        
        dateContainer.classList.add('d-none');
        monthContainer.classList.add('d-none');
        yearContainer.classList.add('d-none');
        
        document.getElementById('filter_date').disabled = true;
        document.getElementById('filter_month').disabled = true;
        document.getElementById('filter_year').disabled = true;

        if (filterType === 'date') {
            dateContainer.classList.remove('d-none');
            document.getElementById('filter_date').disabled = false;
        } else if (filterType === 'month') {
            monthContainer.classList.remove('d-none');
            document.getElementById('filter_month').disabled = false;
        } else if (filterType === 'year') {
            yearContainer.classList.remove('d-none');
            document.getElementById('filter_year').disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleFilterInputs();
    });

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
