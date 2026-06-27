<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\VoidLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $filter = $request->get('filter', 'daily');
        $customDate = $request->get('custom_date');
        
        $salesQuery = Transaction::where('status', 'paid');
        $salesQuery = $this->applyFilter($salesQuery, $filter, $customDate);
        $totalSales = $salesQuery->sum('total');
            
        $trxQuery = Transaction::where('status', 'paid');
        $trxQuery = $this->applyFilter($trxQuery, $filter, $customDate);
        $totalTransactions = $trxQuery->count();
            
        $voidQuery = VoidLog::query();
        $voidQuery = $this->applyFilter($voidQuery, $filter, $customDate);
        $totalVoid = $voidQuery->count();
        
        $lowStock = Product::whereColumn('stock', '<=', 'min_stock')->get();
        
        $topProductsQuery = TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->where('transactions.status', 'paid');
        
        // apply filter requires created_at from transactions table to avoid ambiguity
        $now = \Carbon\Carbon::now();
        if ($filter === 'custom_date' && $customDate) {
            $topProductsQuery->whereDate('transactions.created_at', $customDate);
        } else {
            switch($filter) {
                case 'daily':
                    $topProductsQuery->whereDate('transactions.created_at', $now->toDateString());
                    break;
                case 'weekly':
                    $topProductsQuery->whereBetween('transactions.created_at', [$now->copy()->startOfWeek()->toDateTimeString(), $now->copy()->endOfWeek()->toDateTimeString()]);
                    break;
                case 'monthly':
                    $topProductsQuery->whereMonth('transactions.created_at', $now->month)->whereYear('transactions.created_at', $now->year);
                    break;
                case 'yearly':
                    $topProductsQuery->whereYear('transactions.created_at', $now->year);
                    break;
            }
        }

        $topProducts = $topProductsQuery->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->with('product')
            ->get();
            
        $totalCost = 0;
        $detailsQuery = TransactionDetail::whereHas('transaction', function($q) use ($filter, $customDate) {
            $q->where('status', 'paid');
            $this->applyFilter($q, $filter, $customDate);
        })->with('product');
        
        $details = $detailsQuery->get();
        
        foreach($details as $detail) {
            if($detail->product) {
                $totalCost += $detail->product->cost_price * $detail->qty;
            }
        }
        $profit = $totalSales - $totalCost;

        // Chart Data (Last 7 Days)
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartData[] = Transaction::whereDate('created_at', $date)->where('status', 'paid')->sum('total');
        }

        // 1. Today vs Yesterday Comparison
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayTransactions = Transaction::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->get(['total', 'created_at']);

        $yesterdayTransactions = Transaction::where('status', 'paid')
            ->whereDate('created_at', $yesterday)
            ->get(['total', 'created_at']);

        $todayTotal = $todayTransactions->sum('total');
        $yesterdayTotal = $yesterdayTransactions->sum('total');
        $todayVsYesterdayDiff = $todayTotal - $yesterdayTotal;
        $todayVsYesterdayPercent = $yesterdayTotal > 0 ? round(($todayVsYesterdayDiff / $yesterdayTotal) * 100, 1) : ($todayTotal > 0 ? 100 : 0);

        // Hourly datasets (00:00 - 23:00)
        $todayHourly = array_fill(0, 24, 0);
        foreach ($todayTransactions as $trx) {
            $hour = Carbon::parse($trx->created_at)->hour;
            $todayHourly[$hour] += $trx->total;
        }

        $yesterdayHourly = array_fill(0, 24, 0);
        foreach ($yesterdayTransactions as $trx) {
            $hour = Carbon::parse($trx->created_at)->hour;
            $yesterdayHourly[$hour] += $trx->total;
        }

        // 2. This Month vs Last Month Comparison
        $thisMonthStart = Carbon::now()->startOfMonth();
        $thisMonthEnd = Carbon::now()->endOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $thisMonthTransactions = Transaction::where('status', 'paid')
            ->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])
            ->get(['total', 'created_at']);

        $lastMonthTransactions = Transaction::where('status', 'paid')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->get(['total', 'created_at']);

        $thisMonthTotal = $thisMonthTransactions->sum('total');
        $lastMonthTotal = $lastMonthTransactions->sum('total');
        $thisMonthVsLastMonthDiff = $thisMonthTotal - $lastMonthTotal;
        $thisMonthVsLastMonthPercent = $lastMonthTotal > 0 ? round(($thisMonthVsLastMonthDiff / $lastMonthTotal) * 100, 1) : ($thisMonthTotal > 0 ? 100 : 0);

        // Daily datasets (1 - maxDays)
        $daysInThisMonth = Carbon::now()->daysInMonth;
        $daysInLastMonth = Carbon::now()->subMonth()->daysInMonth;
        $maxDays = max($daysInThisMonth, $daysInLastMonth);

        $thisMonthDaily = array_fill(1, $maxDays, 0);
        foreach ($thisMonthTransactions as $trx) {
            $day = Carbon::parse($trx->created_at)->day;
            $thisMonthDaily[$day] += $trx->total;
        }

        $lastMonthDaily = array_fill(1, $maxDays, 0);
        foreach ($lastMonthTransactions as $trx) {
            $day = Carbon::parse($trx->created_at)->day;
            $lastMonthDaily[$day] += $trx->total;
        }

        $monthLabels = range(1, $maxDays);
        $thisMonthDailyValues = array_values($thisMonthDaily);
        $lastMonthDailyValues = array_values($lastMonthDaily);

        return view('admin.dashboard', compact(
            'totalSales', 'totalTransactions', 'totalVoid', 'lowStock', 'topProducts', 'profit', 
            'chartLabels', 'chartData', 'filter', 'customDate',
            'todayTotal', 'yesterdayTotal', 'todayVsYesterdayDiff', 'todayVsYesterdayPercent', 'todayHourly', 'yesterdayHourly',
            'thisMonthTotal', 'lastMonthTotal', 'thisMonthVsLastMonthDiff', 'thisMonthVsLastMonthPercent', 'thisMonthDailyValues', 'lastMonthDailyValues', 'monthLabels'
        ));
    }

    private function applyFilter($query, $filter, $customDate = null)
    {
        $now = \Carbon\Carbon::now();
        if ($filter === 'custom_date' && $customDate) {
            $query->whereDate('created_at', $customDate);
            return $query;
        }
        switch($filter) {
            case 'daily':
                $query->whereDate('created_at', $now->toDateString());
                break;
            case 'weekly':
                $query->whereBetween('created_at', [$now->copy()->startOfWeek()->toDateTimeString(), $now->copy()->endOfWeek()->toDateTimeString()]);
                break;
            case 'monthly':
                $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                break;
            case 'yearly':
                $query->whereYear('created_at', $now->year);
                break;
        }
        return $query;
    }

    public function reports(\Illuminate\Http\Request $request)
    {
        $filter = $request->get('filter', 'all');
        $customDate = $request->get('custom_date');
        $query = Transaction::with(['user', 'details.product', 'voidLog'])->orderBy('created_at', 'desc');
        $query = $this->applyFilter($query, $filter, $customDate);
        
        $transactions = $query->paginate(20)->appends(['filter' => $filter, 'custom_date' => $customDate]);
        return view('admin.reports', compact('transactions', 'filter', 'customDate'));
    }

    public function exportReports(\Illuminate\Http\Request $request)
    {
        $filter = $request->get('filter', 'all');
        $customDate = $request->get('custom_date');
        $query = Transaction::with('user')->where('status', 'paid')->orderBy('created_at', 'desc');
        $query = $this->applyFilter($query, $filter, $customDate);
        $transactions = $query->get();
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=Laporan_Transaksi_Kulu_Asri_" . date('Y-m-d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $callback = function() use($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 to make Excel happy
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

            // Force Excel to use comma as separator regardless of regional settings
            fputs($file, "sep=,\n");
            $separator = ','; 

            fputcsv($file, ['Tanggal', 'No TRX', 'Kasir', 'Pelanggan', 'Meja', 'Metode Bayar', 'Diskon', 'Total Tagihan'], $separator);
            
            $totalRevenue = 0;
            $totalDiscount = 0;

            foreach ($transactions as $trx) {
                $totalRevenue += $trx->total;
                $totalDiscount += $trx->discount;
                fputcsv($file, [
                    $trx->created_at->format('d/m/Y H:i'), // Formatting as string so it doesn't get messed up
                    $trx->transaction_number,
                    $trx->user->name ?? 'Kasir',
                    $trx->customer_name,
                    $trx->table_number,
                    $trx->payment_method,
                    $trx->discount,
                    $trx->total
                ], $separator);
            }

            fputcsv($file, ['', '', '', '', '', '', '', ''], $separator);
            fputcsv($file, ['TOTAL KESELURUHAN', '', '', '', '', '', $totalDiscount, $totalRevenue], $separator);

            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(\Illuminate\Http\Request $request)
    {
        $filter = $request->get('filter', 'all');
        $customDate = $request->get('custom_date');
        $query = Transaction::with('user')->where('status', 'paid')->orderBy('created_at', 'desc');
        $query = $this->applyFilter($query, $filter, $customDate);
        $transactions = $query->get();
        
        return view('admin.reports_pdf', compact('transactions', 'filter', 'customDate'));
    }


    public function voidLogs(\Illuminate\Http\Request $request)
    {
        $filter = $request->get('filter', 'all');
        $customDate = $request->get('custom_date');
        $query = VoidLog::with('transaction')->orderBy('created_at', 'desc');
        
        if ($filter !== 'all') {
            $query = $this->applyFilter($query, $filter, $customDate);
        }
        
        $voids = $query->paginate(20)->appends(['filter' => $filter, 'custom_date' => $customDate]);
        return view('admin.voids', compact('voids', 'filter', 'customDate'));
    }

    public function voidTransaction(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        
        DB::beginTransaction();
        try {
            $transaction = Transaction::with('details')->findOrFail($id);
            if($transaction->status === 'void') {
                throw new \Exception("Transaksi sudah di-void sebelumnya!");
            }
            
            $transaction->update(['status' => 'void']);
            
            \App\Models\VoidLog::create([
                'transaction_id' => $transaction->id,
                'reason' => $request->reason,
                'void_by' => auth()->user()->name
            ]);
            
            foreach($transaction->details as $detail) {
                $product = \App\Models\Product::where('id', $detail->product_id)->lockForUpdate()->first();
                if($product) {
                    $product->increment('stock', $detail->qty);
                    \App\Models\StockLog::create([
                        'product_id' => $product->id,
                        'qty_out' => -$detail->qty
                    ]);
                }
            }
            
            DB::commit();
            return back()->with('success', 'Transaksi berhasil di-void dan stok dikembalikan.');
        } catch(\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function getChartComparisonData(Request $request)
    {
        $mode = $request->get('mode', 'month_lastmonth');
        
        if ($mode === 'today_yesterday') {
            $selectedDateStr = $request->get('date_primary', Carbon::today()->toDateString());
            $compareDateStr = $request->get('date_secondary', Carbon::yesterday()->toDateString());
            
            try {
                $primaryDate = Carbon::parse($selectedDateStr);
            } catch (\Exception $e) {
                $primaryDate = Carbon::today();
            }
            try {
                $secondaryDate = Carbon::parse($compareDateStr);
            } catch (\Exception $e) {
                $secondaryDate = Carbon::yesterday();
            }

            $primaryTransactions = Transaction::where('status', 'paid')
                ->whereDate('created_at', $primaryDate)
                ->get(['total', 'created_at']);

            $secondaryTransactions = Transaction::where('status', 'paid')
                ->whereDate('created_at', $secondaryDate)
                ->get(['total', 'created_at']);

            $primaryTotal = $primaryTransactions->sum('total');
            $secondaryTotal = $secondaryTransactions->sum('total');
            $diff = $primaryTotal - $secondaryTotal;
            $percent = $secondaryTotal > 0 ? round(($diff / $secondaryTotal) * 100, 1) : ($primaryTotal > 0 ? 100 : 0);

            $primaryHourly = array_fill(0, 24, 0);
            foreach ($primaryTransactions as $trx) {
                $hour = Carbon::parse($trx->created_at)->hour;
                $primaryHourly[$hour] += $trx->total;
            }

            $secondaryHourly = array_fill(0, 24, 0);
            foreach ($secondaryTransactions as $trx) {
                $hour = Carbon::parse($trx->created_at)->hour;
                $secondaryHourly[$hour] += $trx->total;
            }

            return response()->json([
                'success' => true,
                'labels' => array_map(function($h) { return sprintf("%02d:00", $h); }, range(0, 23)),
                'primary_label' => $primaryDate->translatedFormat('d M Y'),
                'secondary_label' => $secondaryDate->translatedFormat('d M Y'),
                'primary_data' => $primaryHourly,
                'secondary_data' => $secondaryHourly,
                'primary_total' => $primaryTotal,
                'secondary_total' => $secondaryTotal,
                'diff' => $diff,
                'percent' => $percent,
                'formatted_primary_total' => 'Rp ' . number_format($primaryTotal, 0, ',', '.'),
                'formatted_secondary_total' => 'Rp ' . number_format($secondaryTotal, 0, ',', '.'),
                'formatted_diff' => ($diff >= 0 ? 'Surplus (+)' : 'Defisit (-)') . ' Rp ' . number_format(abs($diff), 0, ',', '.')
            ]);
        } else {
            $selectedMonthStr = $request->get('month_primary', Carbon::now()->format('Y-m'));
            $compareMonthStr = $request->get('month_secondary', Carbon::now()->subMonth()->format('Y-m'));
            
            try {
                $primaryMonth = Carbon::parse($selectedMonthStr . '-01');
            } catch (\Exception $e) {
                $primaryMonth = Carbon::now()->startOfMonth();
            }
            try {
                $secondaryMonth = Carbon::parse($compareMonthStr . '-01');
            } catch (\Exception $e) {
                $secondaryMonth = Carbon::now()->subMonth()->startOfMonth();
            }

            $primaryTransactions = Transaction::where('status', 'paid')
                ->whereBetween('created_at', [$primaryMonth->copy()->startOfMonth(), $primaryMonth->copy()->endOfMonth()])
                ->get(['total', 'created_at']);

            $secondaryTransactions = Transaction::where('status', 'paid')
                ->whereBetween('created_at', [$secondaryMonth->copy()->startOfMonth(), $secondaryMonth->copy()->endOfMonth()])
                ->get(['total', 'created_at']);

            $primaryTotal = $primaryTransactions->sum('total');
            $secondaryTotal = $secondaryTransactions->sum('total');
            $diff = $primaryTotal - $secondaryTotal;
            $percent = $secondaryTotal > 0 ? round(($diff / $secondaryTotal) * 100, 1) : ($primaryTotal > 0 ? 100 : 0);

            $daysInPrimary = $primaryMonth->daysInMonth;
            $daysInSecondary = $secondaryMonth->daysInMonth;
            $maxDays = max($daysInPrimary, $daysInSecondary);

            $primaryDaily = array_fill(1, $maxDays, 0);
            foreach ($primaryTransactions as $trx) {
                $day = Carbon::parse($trx->created_at)->day;
                $primaryDaily[$day] += $trx->total;
            }

            $secondaryDaily = array_fill(1, $maxDays, 0);
            foreach ($secondaryTransactions as $trx) {
                $day = Carbon::parse($trx->created_at)->day;
                $secondaryDaily[$day] += $trx->total;
            }

            return response()->json([
                'success' => true,
                'labels' => array_map(function($d) { return "Tgl " . $d; }, range(1, $maxDays)),
                'primary_label' => $primaryMonth->translatedFormat('F Y'),
                'secondary_label' => $secondaryMonth->translatedFormat('F Y'),
                'primary_data' => array_values($primaryDaily),
                'secondary_data' => array_values($secondaryDaily),
                'primary_total' => $primaryTotal,
                'secondary_total' => $secondaryTotal,
                'diff' => $diff,
                'percent' => $percent,
                'formatted_primary_total' => 'Rp ' . number_format($primaryTotal, 0, ',', '.'),
                'formatted_secondary_total' => 'Rp ' . number_format($secondaryTotal, 0, ',', '.'),
                'formatted_diff' => ($diff >= 0 ? 'Surplus (+)' : 'Defisit (-)') . ' Rp ' . number_format(abs($diff), 0, ',', '.')
            ]);
        }
    }
}
