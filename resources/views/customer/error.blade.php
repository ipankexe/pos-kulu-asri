<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pemberitahuan Meja' }} - Rumah Makan Kulu Asri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8faf9;
            color: #2b3e34;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-card {
            max-width: 440px;
            width: 100%;
            background: white;
            border-radius: 28px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(27, 94, 32, 0.08);
            border: 1px solid #e2ece5;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background: #fee2e2;
            color: #dc2626;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-circle">
            <i class="bi bi-qr-code-scan"></i>
        </div>
        <h4 class="fw-bold mb-3 text-dark">{{ $title ?? 'Pemberitahuan' }}</h4>
        <p class="text-muted mb-4" style="line-height: 1.6;">
            {{ $message ?? 'Maaf, terjadi kendala saat memuat menu meja ini. Silakan hubungi kasir atau pelayan kami.' }}
        </p>
        <div class="p-3 bg-light rounded-4 text-start small text-secondary mb-4">
            <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle me-1 text-success"></i> Solusi Cepat:</div>
            • Pastikan Anda memindai kode QR yang terpasang di meja makan Anda.<br>
            • Atau Anda dapat langsung memesan melalui kasir restoran.
        </div>
        <div class="text-muted small">
            <i class="bi bi-shop me-1"></i> Rumah Makan Kulu Asri
        </div>
    </div>
</body>
</html>
