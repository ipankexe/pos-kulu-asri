<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR - {{ $table->name }} | Rumah Makan Kulu Asri</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #eef2f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .no-print-bar {
            margin-bottom: 25px;
            display: flex;
            gap: 12px;
        }
        .btn {
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-print { background: #1b5e20; color: white; box-shadow: 0 4px 15px rgba(27,94,32,0.3); }
        .btn-print:hover { background: #2e7d32; transform: translateY(-2px); }
        .btn-back { background: white; color: #333; border: 1px solid #ccc; }
        .btn-back:hover { background: #f8f9fa; }

        /* QR Table Stand Card (A5 size proportion) */
        .qr-card {
            width: 380px;
            background: #ffffff;
            border-radius: 28px;
            padding: 36px 30px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            border: 2px solid #e2ece5;
            position: relative;
            overflow: hidden;
        }

        .qr-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 10px;
            background: linear-gradient(90deg, #1b5e20, #43a047, #f57c00);
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e8f5e9;
            color: #1b5e20;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .restaurant-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            color: #1b5e20;
            margin-bottom: 4px;
        }

        .restaurant-subtitle {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
            font-size: 15px;
            font-weight: 700;
            color: #d97706;
            margin-bottom: 24px;
            letter-spacing: 0.5px;
        }

        .table-pill {
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 18px;
            margin: 0 auto 24px;
            display: inline-block;
            box-shadow: 0 6px 18px rgba(46, 125, 50, 0.25);
        }
        .table-pill .table-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.85;
            display: block;
        }
        .table-pill .table-name {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .qr-wrapper {
            background: #fafdfa;
            padding: 18px;
            border-radius: 20px;
            border: 2px dashed #c8e6c9;
            display: inline-block;
            margin-bottom: 20px;
        }
        #qrcode {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #qrcode img, #qrcode canvas {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .instruction-box {
            background: #fdfbf7;
            border: 1px solid #fae8ce;
            border-radius: 16px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }
        .instruction-title {
            font-weight: 700;
            font-size: 14px;
            color: #d97706;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .instruction-desc {
            font-size: 11.5px;
            color: #555;
            line-height: 1.4;
        }

        .footer-note {
            font-size: 10px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print-bar {
                display: none;
            }
            .qr-card {
                box-shadow: none;
                border: 1.5px solid #ccc;
                page-break-inside: avoid;
                margin: auto;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="{{ route('tables.index') }}" class="btn btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Admin
        </a>
        <a href="{{ route('customer.table_order', $table->qr_token) }}" target="_blank" class="btn" style="background: #0284c7; color: white;">
            <i class="bi bi-phone"></i> Buka POV Pelanggan (HP)
        </a>
        <button onclick="window.print()" class="btn btn-print">
            <i class="bi bi-printer-fill"></i> Cetak QR Stand
        </button>
    </div>

    <div class="qr-card">
        <div class="brand-badge">
            <i class="bi bi-shop"></i> Kulu Asri Official
        </div>
        <h1 class="restaurant-title">Rumah Makan Kulu Asri</h1>
        <p class="restaurant-subtitle">Jagonya Ikan Bakar!</p>

        <div class="table-pill">
            <span class="table-label">Nomor Meja</span>
            <span class="table-name">{{ $table->name }}</span>
        </div>

        <div class="qr-wrapper">
            <div id="qrcode"></div>
        </div>

        <div class="instruction-box">
            <div class="instruction-title">
                <i class="bi bi-qr-code-scan"></i> Scan untuk Memesan
            </div>
            <p class="instruction-desc">
                Buka kamera HP Anda, scan QR Code di atas, pilih menu favorit, dan lakukan pembayaran langsung dari meja Anda.
            </p>
        </div>

        <div class="footer-note" style="font-weight: 600;">
            Token: {{ substr($table->qr_token, 0, 10) }}•••• • Kulu Asri - Jagonya Ikan Bakar!
        </div>
    </div>

    <!-- QRCode JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        const qrUrl = "{{ $table->qr_url }}";
        new QRCode(document.getElementById("qrcode"), {
            text: qrUrl,
            width: 200,
            height: 200,
            colorDark: "#1b5e20",
            colorLight: "#fafdfa",
            correctLevel: QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>
