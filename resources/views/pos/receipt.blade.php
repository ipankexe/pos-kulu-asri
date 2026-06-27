<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->id }}</title>
    <style>
        body { font-family: monospace; width: 300px; margin: 0 auto; padding: 20px 0; color: #000; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .border-dashed { border-top: 1px dashed #000; border-bottom: 1px dashed #000; margin: 10px 0; padding: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 0; }
        .right { text-align: right; }
        .d-flex { display: flex; justify-content: space-between; }
        @media print {
            body { width: 100%; margin: 0; padding: 0; }
            @page { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <div style="margin-bottom: 5px;">
            <img src="{{ asset('images/logo.jpg') }}" alt="Kulu Asri Logo" style="width: 150px; height: auto; filter: grayscale(100%) contrast(120%); display: inline-block;">
        </div>
        <p style="margin:5px 0;">Jl.Singosari No.7 Kecamatan Kranganyar Kab.Pekalongan<br>Telp: +62 856-4184-7054</p>
    </div>

    <div class="border-dashed">
        <div class="d-flex"><span>Tgl</span>: {{ $transaction->created_at->format('d/m/Y H:i') }}</div>
        <div class="d-flex"><span>No. TRX</span>: {{ $transaction->transaction_number ?? '-' }}</div>
        <div class="d-flex"><span>Kasir</span>: {{ $transaction->user->name }}</div>
        <div class="d-flex"><span>Pelanggan</span>: {{ $transaction->customer_name }} (Meja {{ $transaction->table_number }})</div>
    </div>

    <table>
        @foreach($transaction->details as $detail)
        <tr>
            <td colspan="3">{{ $detail->product->name }}</td>
        </tr>
        <tr>
            <td>{{ $detail->qty }} x</td>
            <td>{{ number_format($detail->price, 0, ',', '.') }}</td>
            <td class="right">{{ number_format($detail->qty * $detail->price, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="border-dashed">
        <div class="d-flex fw-bold"><span>TOTAL</span><span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span></div>
        <div class="d-flex"><span>Tunai</span><span>Rp {{ number_format($transaction->payment, 0, ',', '.') }}</span></div>
        <div class="d-flex"><span>Kembali</span><span>Rp {{ number_format($transaction->change, 0, ',', '.') }}</span></div>
    </div>

    <div class="text-center mt-3">
        <p>Terima Kasih atas kunjungan Anda!</p>
    </div>
</body>
</html>
