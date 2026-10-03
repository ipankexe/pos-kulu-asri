<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Dapur #{{ $transaction->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: monospace; width: 300px; margin: 0 auto; padding: 10px 0; color: #000; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .border-dashed { border-top: 2px dashed #000; border-bottom: 2px dashed #000; margin: 10px 0; padding: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 8px 0; vertical-align: top; }
        .qty-col { width: 40px; font-weight: bold; font-size: 1.2rem; }
        .item-name { font-weight: bold; font-size: 1.1rem; }
        .notes { font-size: 0.9rem; font-style: italic; display: block; margin-top: 4px; }
        .d-flex { display: flex; justify-content: space-between; font-size: 1.1rem; }
        .table-number { font-size: 1.5rem; border: 2px solid #000; padding: 5px; display: inline-block; margin-top: 10px; }
        .source-pill { font-size: 0.85rem; border: 1px solid #000; padding: 2px 8px; border-radius: 4px; margin-top: 6px; display: inline-block; font-weight: bold; }
        .kitchen-tagline {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 12.5px;
            font-weight: 700;
            font-style: italic;
            letter-spacing: 0.5px;
            margin-top: 15px;
        }
        @media print {
            body { width: 100%; margin: 0; padding: 0; }
            @page { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        @if(isset($isAdditional) && $isAdditional)
            <div style="border: 3px solid #000; padding: 6px 10px; font-weight: bold; font-size: 1.3rem; margin: 0 0 8px 0; text-transform: uppercase;">
                PESANAN TAMBAHAN
            </div>
        @else
            <h2 style="margin:0;">STRUK DAPUR</h2>
        @endif
        <div class="table-number fw-bold">
            {{ $transaction->table_number == 'Takeaway' ? 'TAKEAWAY' : 'MEJA: ' . $transaction->table_number }}
        </div>
        <div>
            <span class="source-pill">KANAL: {{ strtoupper($transaction->order_source ?? 'POS') }}</span>
        </div>
    </div>

    <div class="border-dashed">
        <div class="d-flex"><span>Tgl</span>: {{ $transaction->created_at->format('d/m/Y H:i') }}</div>
        <div class="d-flex"><span>No. TRX</span>: {{ $transaction->transaction_number ?? '-' }}</div>
        <div class="d-flex"><span>Pelanggan</span>: <span class="fw-bold">{{ $transaction->customer_name }}</span></div>
    </div>

    <table>
        @foreach($transaction->details as $detail)
        <tr style="border-bottom: 1px solid #ddd;">
            <td class="qty-col">{{ $detail->qty_to_print }}x</td>
            <td>
                <span class="item-name">{{ $detail->product->name ?? ('Item #' . $detail->product_id) }}</span>
                @if($detail->notes)
                    <span class="notes">- Catatan: {{ $detail->notes }}</span>
                @endif
            </td>
        </tr>
        @endforeach
    </table>

    <div class="text-center" style="margin-top: 15px; border-top: 1px dashed #000; padding-top: 8px;">
        <p class="kitchen-tagline">~ Kulu Asri - Jagonya Ikan Bakar! ~</p>
    </div>
</body>
</html>
