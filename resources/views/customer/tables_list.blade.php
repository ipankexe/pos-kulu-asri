<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pilih Meja - Rumah Makan Kulu Asri</title>

    <!-- Google Fonts & Bootstrap 5 & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #1b5e20;
            --primary-light: #2e7d32;
            --primary-soft: #e8f5e9;
            --accent: #d97706;
            --bg-page: #f8faf9;
            --card-border: #e2ece5;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-page);
            color: #0f172a;
            min-height: 100vh;
        }

        .hero-banner {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: white;
            padding: 32px 20px 24px;
            border-bottom-left-radius: 28px;
            border-bottom-right-radius: 28px;
            box-shadow: 0 10px 30px rgba(27, 94, 32, 0.15);
            text-align: center;
        }

        .restaurant-brand {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .tagline {
            font-style: italic;
            font-size: 14px;
            color: #fcd34d;
            font-weight: 600;
        }

        .table-card {
            background: white;
            border: 1.5px solid var(--card-border);
            border-radius: 18px;
            padding: 16px;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .table-card:hover, .table-card:active {
            border-color: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(27, 94, 32, 0.12);
            color: inherit;
        }

        .table-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .badge-status {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 50px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="hero-banner">
        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 small mb-2">
            <i class="bi bi-qr-code-scan me-1"></i> Testing & Self-Order
        </span>
        <h1 class="restaurant-brand">Rumah Makan Kulu Asri</h1>
        <p class="tagline mb-0">Pilih Meja untuk Membuka Menu</p>
    </div>

    <div class="container py-4" style="max-width: 540px;">
        <div class="alert alert-info border-0 rounded-4 shadow-sm py-2 px-3 small d-flex align-items-center mb-3">
            <i class="bi bi-info-circle-fill fs-5 me-2 text-info"></i>
            <div>Silakan pilih nomor meja Anda di bawah ini untuk melihat daftar menu dan langsung memesan.</div>
        </div>

        <div class="d-flex flex-column gap-2">
            @forelse($tables as $table)
                <a href="{{ route('customer.table_order', $table->qr_token) }}" class="table-card">
                    <div class="d-flex align-items-center gap-3">
                        <div class="table-icon-box">
                            <i class="bi bi-cup-hot-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $table->name }}</h6>
                            <small class="text-muted">
                                @if($table->status === 'occupied')
                                    <span class="badge bg-warning bg-opacity-10 text-warning badge-status">Terisi</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success badge-status">Tersedia</span>
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill small">
                            Buka Menu <i class="bi bi-arrow-right ms-1"></i>
                        </span>
                    </div>
                </a>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-exclamation-circle fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada meja yang aktif saat ini.
                </div>
            @endforelse
        </div>
    </div>

</body>
</html>
