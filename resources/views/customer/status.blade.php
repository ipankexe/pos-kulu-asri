<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Status Pesanan #{{ $transaction->transaction_number }} - Kulu Asri</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #1b5e20;
            --primary-light: #2e7d32;
            --accent: #d97706;
            --text-main: #0f172a;
            --card-border: #e2ece5;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8faf9;
            color: var(--text-main);
            padding: 20px 16px 40px;
        }

        .status-card {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 28px;
            box-shadow: 0 10px 30px rgba(27, 94, 32, 0.06);
            border: 1px solid var(--card-border);
            overflow: hidden;
        }

        .status-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 28px 24px;
            text-align: center;
        }

        /* Stepper */
        .stepper {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 24px 0 16px;
        }
        .stepper::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 20px;
            right: 20px;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }
        .stepper-progress {
            position: absolute;
            top: 18px;
            left: 20px;
            height: 3px;
            background: var(--primary);
            z-index: 2;
            transition: width 0.4s ease;
        }

        .step-item {
            position: relative;
            z-index: 3;
            text-align: center;
            width: 25%;
        }
        .step-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: white;
            border: 3px solid #cbd5e1;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.3s;
        }
        .step-item.active .step-icon {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.2);
        }
        .step-item.completed .step-icon {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
        }
        .step-label {
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            line-height: 1.2;
        }
        .step-item.active .step-label,
        .step-item.completed .step-label {
            color: var(--primary);
        }
    </style>
</head>
<body>

    <div class="status-card">
        <div class="status-header">
            <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-20 px-3 py-1 rounded-pill mb-2" style="font-size: 12px;">
                <i class="bi bi-geo-alt-fill"></i> Meja {{ $transaction->table_number }}
            </div>
            <h4 class="fw-bold m-0" id="statusHeaderTitle">
                @if($transaction->isPaid())
                    Pesanan Telah Diterima!
                @elseif($transaction->payment_status === 'pending')
                    Menunggu Pembayaran
                @else
                    Pesanan Dibatalkan
                @endif
            </h4>
            <small class="opacity-75" id="statusHeaderSubtitle">
                Nomor Pesanan: #{{ $transaction->transaction_number }}
            </small>
        </div>

        <div class="p-4">
            <!-- Stepper Progress Tracker -->
            @php
                $orderStatus = $transaction->order_status;
                $stepIndex = 1;
                if ($orderStatus === 'confirmed') $stepIndex = 2;
                elseif ($orderStatus === 'preparing') $stepIndex = 3;
                elseif ($orderStatus === 'ready' || $orderStatus === 'completed') $stepIndex = 4;
                if (!$transaction->isPaid() && $transaction->payment_status !== 'pending') $stepIndex = 0;
            @endphp

            @if($transaction->payment_status === 'cancelled' || $transaction->payment_status === 'failed')
                <div class="alert alert-danger rounded-4 text-center my-3">
                    <i class="bi bi-x-circle-fill fs-3 d-block mb-1"></i>
                    <strong>Pembayaran Gagal / Dibatalkan</strong>
                    <div class="small">Pesanan ini tidak diproses. Silakan scan QR meja kembali untuk memesan.</div>
                </div>
            @else
                <div class="stepper" id="orderStepper">
                    <div class="stepper-progress" id="stepperProgressBar" style="width: {{ ($stepIndex - 1) * 33.3 }}%;"></div>
                    <div class="step-item {{ $stepIndex >= 1 ? ($stepIndex == 1 ? 'active' : 'completed') : '' }}" id="step1">
                        <div class="step-icon"><i class="bi bi-credit-card"></i></div>
                        <div class="step-label">Dibayar</div>
                    </div>
                    <div class="step-item {{ $stepIndex >= 2 ? ($stepIndex == 2 ? 'active' : 'completed') : '' }}" id="step2">
                        <div class="step-icon"><i class="bi bi-check2-circle"></i></div>
                        <div class="step-label">Dikonfirmasi</div>
                    </div>
                    <div class="step-item {{ $stepIndex >= 3 ? ($stepIndex == 3 ? 'active' : 'completed') : '' }}" id="step3">
                        <div class="step-icon"><i class="bi bi-fire"></i></div>
                        <div class="step-label">Dimasak</div>
                    </div>
                    <div class="step-item {{ $stepIndex >= 4 ? 'completed' : '' }}" id="step4">
                        <div class="step-icon"><i class="bi bi-bell-fill"></i></div>
                        <div class="step-label">Disajikan</div>
                    </div>
                </div>

                <div class="bg-light p-3 rounded-4 text-center mb-4 border">
                    <span class="badge bg-success px-3 py-2 rounded-pill fw-bold" id="badgeStatus">
                        @if($orderStatus === 'confirmed')
                            Dikonfirmasi & Masuk Antrean Dapur
                        @elseif($orderStatus === 'preparing')
                            Koki Sedang Memasak Pesanan Anda
                        @elseif($orderStatus === 'ready')
                            Pesanan Siap Disajikan ke Meja
                        @elseif($orderStatus === 'completed')
                            Pesanan Selesai Dinikmati
                        @else
                            Menunggu Konfirmasi
                        @endif
                    </span>
                    <p class="text-muted small mt-2 mb-0" id="statusDescription">
                        Mohon ditunggu di meja. Pelayan kami akan mengantarkan pesanan Anda begitu siap.
                    </p>
                </div>
            @endif

            <!-- Rincian Pesanan -->
            <div class="mb-4">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-receipt me-2 text-success"></i> Rincian Menu Pesanan</h6>
                <div class="border rounded-4 p-3 bg-white">
                    @foreach($transaction->details as $detail)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <div class="fw-bold text-dark small">{{ $detail->product->name ?? ('Item #' . $detail->product_id) }}</div>
                            <div class="text-muted small">{{ $detail->qty }}x @ Rp {{ number_format($detail->price, 0, ',', '.') }}</div>
                            @if($detail->notes)
                                <div class="badge bg-light text-secondary border small mt-1">
                                    <i class="bi bi-pencil me-1"></i>{{ $detail->notes }}
                                </div>
                            @endif
                        </div>
                        <div class="fw-bold text-dark small">
                            Rp {{ number_format($detail->qty * $detail->price, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach

                    <div class="d-flex justify-content-between pt-3 text-muted small">
                        <span>Metode Pembayaran</span>
                        <span class="fw-bold text-dark">{{ $transaction->payment_method ?? 'QRIS' }}</span>
                    </div>
                    <div class="d-flex justify-content-between pt-1 fs-5 fw-bold text-dark">
                        <span>Total Tagihan</span>
                        <span style="color: var(--primary);">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Tombol Tambah Pesanan -->
            @if($table)
            <div class="d-grid gap-2">
                <a href="{{ route('customer.table_order', $table->qr_token) }}" class="btn btn-outline-success py-3 rounded-pill fw-bold">
                    <i class="bi bi-plus-circle me-1"></i> Pesan Menu Tambahan
                </a>
            </div>
            @endif
        </div>

        <div class="bg-light p-3 text-center border-top">
            <div style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; font-weight: 700; color: var(--primary); font-size: 14.5px; letter-spacing: 0.3px;">
                Kulu Asri <span style="color: #d97706;">-</span> Jagonya Ikan Bakar!
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 11px;">Rumah Makan Kulu Asri • Cita Rasa Tradisional Nusantara</small>
        </div>
    </div>

    <!-- Live Polling Script -->
    <script>
        const trxNumber = "{{ $transaction->transaction_number }}";

        // Polling status setiap 5 detik secara ringan
        setInterval(async () => {
            try {
                const res = await fetch(`/order/status/${trxNumber}/check`);
                const data = await res.json();
                if (data.success) {
                    updateStatusUI(data.order_status, data.payment_status);
                }
            } catch (e) {
                console.log("Polling status error: ", e);
            }
        }, 5000);

        function updateStatusUI(orderStatus, paymentStatus) {
            let step = 1;
            let badgeText = "Menunggu Konfirmasi";
            let descText = "Pesanan Anda sedang dalam antrean.";

            if (orderStatus === 'confirmed') {
                step = 2;
                badgeText = "Dikonfirmasi & Masuk Antrean Dapur";
                descText = "Pesanan telah dikonfirmasi dan siap dimasak oleh tim dapur.";
            } else if (orderStatus === 'preparing') {
                step = 3;
                badgeText = "Koki Sedang Memasak Pesanan Anda";
                descText = "Makanan dan minuman Anda sedang dimasak dengan bahan segar.";
            } else if (orderStatus === 'ready') {
                step = 4;
                badgeText = "Pesanan Siap Disajikan ke Meja";
                descText = "Pesanan sudah matang dan sedang diantarkan ke meja Anda.";
            } else if (orderStatus === 'completed') {
                step = 4;
                badgeText = "Pesanan Selesai Dinikmati";
                descText = "Terima kasih atas kunjungan Anda di Rumah Makan Kulu Asri!";
            }

            const bar = document.getElementById('stepperProgressBar');
            if (bar) bar.style.width = `${(step - 1) * 33.3}%`;

            for (let i = 1; i <= 4; i++) {
                const el = document.getElementById(`step${i}`);
                if (!el) continue;
                el.className = 'step-item';
                if (i < step) el.classList.add('completed');
                else if (i === step) el.classList.add('active');
            }

            const badge = document.getElementById('badgeStatus');
            if (badge) badge.innerText = badgeText;

            const desc = document.getElementById('statusDescription');
            if (desc) desc.innerText = descText;
        }
    </script>
</body>
</html>
