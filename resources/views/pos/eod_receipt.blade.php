<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Rekapitulasi (End of Day)</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 0 auto; padding: 20px 0; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 16px; font-weight: bold; }
        .header p { margin: 5px 0; }
        .line { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 0; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; }
    </style>
</head>
<body onload="window.print(); setTimeout(() => window.close(), 1000);">

    <div class="header">
        <h2>RUMAH MAKAN KULU ASRI</h2>
        <p>Jl. Raya Kulu Asri No. 123</p>
        <p>Telp: 0812-3456-7890</p>
        <div class="line"></div>
        <h2 style="font-size: 14px;">LAPORAN SHIFT (END OF DAY)</h2>
    </div>

    <table>
        <tr><td>Tanggal Cetak</td><td class="text-right">{{ $summary['date'] }}</td></tr>
        <tr><td>Nama Kasir</td><td class="text-right">{{ $summary['kasir'] }}</td></tr>
    </table>

    <div class="line"></div>

    <table>
        <tr><td>Total Transaksi</td><td class="text-right">{{ $summary['transaction_count'] }}</td></tr>
        <tr><td>Total Transaksi Batal (Void)</td><td class="text-right">{{ $summary['void_count'] }}</td></tr>
        <tr><td>Total Diskon Keluar</td><td class="text-right">Rp {{ number_format($summary['total_discount'], 0, ',', '.') }}</td></tr>
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td class="bold">UANG FISIK DI LACI (CASH)</td>
            <td class="text-right bold">Rp {{ number_format($summary['cash'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pendapatan QRIS</td>
            <td class="text-right">Rp {{ number_format($summary['qris'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pendapatan Kartu Debit</td>
            <td class="text-right">Rp {{ number_format($summary['debit'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line"></div>
    
    <table>
        <tr>
            <td class="bold" style="font-size: 14px;">TOTAL OMSET BERSIH</td>
            <td class="text-right bold" style="font-size: 14px;">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        <div class="line"></div>
        <p>Shift berhasil ditutup dan direkap.</p>
        <p>Terima kasih atas kerja keras Anda hari ini!</p>
    </div>

</body>
</html>
