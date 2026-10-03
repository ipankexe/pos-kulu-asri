<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - POS Kulu Asri</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 13px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2e7d32;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #1b5e20;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }
        .report-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #2e7d32;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            color: #333;
            font-weight: 600;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #e8f5e9;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .no-print {
                display: none;
            }
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 1000);">

    <div class="header">
        <h1>RUMAH MAKAN KULU ASRI</h1>
        <p style="font-family: 'Georgia', serif; font-style: italic; font-weight: bold; color: #d97706; font-size: 13px; margin: 3px 0;">Kulu Asri - Jagonya Ikan Bakar!</p>
        <p>Jl.Singosari No.7 Kecamatan Kranganyar Kab.Pekalongan | Telp: +62 856-4184-7054</p>
    </div>

    <div class="report-info">
        <div class="report-title">
            LAPORAN PENJUALAN TRANSAKSI
        </div>
        <div>
            Periode: 
            @switch($filter)
                @case('daily')
                    Hari Ini ({{ \Carbon\Carbon::now()->format('d/m/Y') }})
                    @break
                @case('weekly')
                    Minggu Ini
                    @break
                @case('monthly')
                    Bulan Ini ({{ \Carbon\Carbon::now()->format('F Y') }})
                    @break
                @case('yearly')
                    Tahun Ini ({{ \Carbon\Carbon::now()->format('Y') }})
                    @break
                @case('custom_date')
                    Tanggal: {{ \Carbon\Carbon::parse($customDate)->format('d/m/Y') }}
                    @break
                @default
                    Semua Waktu
            @endswitch
            @if(isset($source) && $source !== 'all')
                | Kanal: <strong>{{ strtoupper($source) }}</strong>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="35" class="text-center">No</th>
                <th width="110">Tanggal/Waktu</th>
                <th>No. TRX</th>
                <th width="60" class="text-center">Kanal</th>
                <th>Kasir</th>
                <th>Pelanggan</th>
                <th width="50" class="text-center">Meja</th>
                <th width="90">Metode Bayar</th>
                <th class="text-right">Diskon</th>
                <th class="text-right">Total Belanja</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalRevenue = 0;
                $totalDiscount = 0;
                $i = 1;
            @endphp
            @forelse($transactions as $trx)
                @php
                    $totalRevenue += $trx->total;
                    $totalDiscount += $trx->discount;
                @endphp
                <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td><code>{{ $trx->transaction_number }}</code></td>
                    <td class="text-center">
                        @if($trx->isQr())
                            <span class="badge" style="background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">QR</span>
                        @else
                            <span class="badge badge-success">POS</span>
                        @endif
                    </td>
                    <td>{{ $trx->user->name ?? ($trx->isQr() ? 'Self-Order (QR)' : 'Kasir') }}</td>
                    <td>{{ $trx->customer_name }}</td>
                    <td class="text-center">{{ $trx->table_number }}</td>
                    <td>{{ $trx->payment_method }}</td>
                    <td class="text-right">Rp {{ number_format($trx->discount, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: 500;">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #777;">Belum ada transaksi terekam.</td>
                </tr>
            @endforelse
            
            @if(count($transactions) > 0)
                <tr class="total-row">
                    <td colspan="8" class="text-right">TOTAL KESELURUHAN</td>
                    <td class="text-right">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="report-info" style="margin-top: 50px; justify-content: flex-end;">
        <div style="text-align: center; width: 200px;">
            <p>Pekalongan, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p style="margin-top: 60px; font-weight: bold; border-top: 1px solid #333; padding-top: 5px;">Manager Kulu Asri</p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 25px; border-top: 1px dashed #ccc; padding-top: 8px; font-family: 'Georgia', serif; font-style: italic; font-size: 11px; color: #666;">
        Kulu Asri - Jagonya Ikan Bakar!
    </div>

</body>
</html>
