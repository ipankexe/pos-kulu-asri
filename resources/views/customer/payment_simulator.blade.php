<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulator Pembayaran - Order #{{ $transaction->transaction_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f1f5f3;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .gateway-card {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 28px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.06);
            border: 1px solid #e2ece5;
            overflow: hidden;
        }
        .gateway-header {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: white;
            padding: 24px;
            text-align: center;
        }
        .qris-box {
            background: #f8faf9;
            border: 2px dashed #c8e6c9;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .amount-highlight {
            font-size: 26px;
            font-weight: 800;
            color: #d97706;
        }
    </style>
</head>
<body>

    <div class="gateway-card">
        <div class="gateway-header">
            <span class="badge bg-warning text-dark mb-2 px-3 py-1 rounded-pill fw-bold">
                <i class="bi bi-shield-check me-1"></i> SIMULATOR PAYMENT GATEWAY
            </span>
            <h5 class="fw-bold m-0">Kulu Asri Pay Checkout</h5>
            <small class="opacity-75">Sandbox Testing Environment</small>
        </div>

        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <div>
                    <span class="text-muted small d-block">Nomor Pesanan</span>
                    <strong class="text-dark">#{{ $transaction->transaction_number }}</strong>
                </div>
                <div class="text-end">
                    <span class="text-muted small d-block">Meja</span>
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill">
                        {{ $transaction->table_number }}
                    </span>
                </div>
            </div>

            <div class="text-center mb-3">
                <span class="text-muted small d-block">Total Tagihan</span>
                <div class="amount-highlight">Rp {{ number_format($transaction->total, 0, ',', '.') }}</div>
            </div>

            <!-- QRIS Mock Barcode Area -->
            <div class="qris-box">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <span class="fw-bold text-dark" style="letter-spacing: 1px;">QRIS</span>
                    <small class="text-muted">• Standar Pembayaran Nasional</small>
                </div>
                <div class="my-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=KULU-ASRI-MOCK-{{ $transaction->transaction_number }}" 
                         alt="QRIS Mock" class="rounded-3 shadow-sm border p-2 bg-white" style="width: 170px; height: 170px;">
                </div>
                <small class="text-muted d-block">
                    Mendukung BCA, Mandiri, BRI, BNI, GoPay, OVO, ShopeePay, DANA.
                </small>
            </div>

            <!-- Form Simulasi Webhook -->
            <form action="{{ route('customer.mock_payment.process', $transaction->transaction_number) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Metode Bayar Simulasi</label>
                    <select name="payment_method" class="form-select rounded-3">
                        <option value="QRIS" selected>QRIS (BCA Mobile / GoPay)</option>
                        <option value="GoPay">GoPay Instant</option>
                        <option value="ShopeePay">ShopeePay</option>
                        <option value="BCA Virtual Account">BCA Virtual Account</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" name="action" value="success" class="btn btn-success py-3 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-check-circle-fill me-2"></i> Simulasi Bayar Berhasil (PAID)
                    </button>
                    <button type="submit" name="action" value="fail" class="btn btn-outline-danger py-2 rounded-pill fw-bold border-0">
                        <i class="bi bi-x-circle me-1"></i> Simulasi Gagal / Batal
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-light p-3 text-center border-top">
            <div style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-weight: 700; color: #1b5e20; font-size: 13.5px; letter-spacing: 0.3px;">
                Kulu Asri <span style="color: #d97706;">-</span> Jagonya Ikan Bakar!
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 10.5px;">
                <i class="bi bi-info-circle me-1"></i> Mode pengujian ini memvalidasi alur pembayaran tanpa potongan saldo riil.
            </small>
        </div>
    </div>

</body>
</html>
