<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kulu Asri</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; }
        
        .sidebar { 
            min-height: 100vh; 
            background: linear-gradient(180deg, #1b5e20 0%, #2e7d32 100%); 
            color: white; 
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        }
        .sidebar-brand {
            padding: 25px 20px;
            font-size: 1.5rem;
            font-weight: 800;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar a { 
            color: rgba(255,255,255,0.7); text-decoration: none; padding: 12px 25px; 
            display: block; font-weight: 500; transition: 0.3s; 
            border-left: 4px solid transparent;
        }
        .sidebar a:hover, .sidebar a.active { 
            background: rgba(255,255,255,0.1); color: white; 
            border-left: 4px solid #f57c00;
        }
        
        .main-content { padding: 40px; background: #f4f7f6; }
        
        .card-stat { 
            border: none; border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
            transition: 0.3s; background: white;
        }
        .card-stat:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
        
        .stat-icon {
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
        }
        
        .top-menu-card {
            border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    @include('admin.sidebar')

    <!-- Main Content -->
    <div class="flex-grow-1 main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold text-dark mb-0">Dashboard Overview</h2>
                <p class="text-muted">Selamat datang, {{ auth()->user()->name }}!</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center" id="filterForm">
                    <input type="date" name="custom_date" id="customDateInput" class="form-control rounded-pill border-success text-success fw-bold shadow-sm me-2 {{ ($filter ?? 'daily') == 'custom_date' ? '' : 'd-none' }}" value="{{ $customDate ?? \Carbon\Carbon::now()->toDateString() }}" onchange="document.getElementById('filterForm').submit()" style="max-width: 150px;">
                    <select name="filter" id="filterSelect" class="form-select rounded-pill border-success text-success fw-bold shadow-sm" onchange="toggleDateInput()" style="cursor: pointer; width: 160px;">
                        <option value="daily" {{ ($filter ?? 'daily') == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="weekly" {{ ($filter ?? 'daily') == 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="monthly" {{ ($filter ?? 'daily') == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="yearly" {{ ($filter ?? 'daily') == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom_date" {{ ($filter ?? 'daily') == 'custom_date' ? 'selected' : '' }}>Tanggal Spesifik</option>
                    </select>
                </form>
                <div class="bg-white px-4 py-2 rounded-pill shadow-sm fw-bold text-success d-none d-md-block">
                    <i class="bi bi-calendar3 me-2"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <a href="{{ route('admin.reports', ['filter' => $filter, 'custom_date' => $customDate]) }}" class="text-decoration-none">
                    <div class="card card-stat p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                @php
                                    $filterText = [
                                        'daily' => 'Hari Ini',
                                        'weekly' => 'Minggu Ini',
                                        'monthly' => 'Bulan Ini',
                                        'yearly' => 'Tahun Ini',
                                        'custom_date' => 'Tgl ' . ($customDate ?? \Carbon\Carbon::now()->toDateString())
                                    ][$filter ?? 'daily'];
                                @endphp
                                <div class="text-muted small fw-bold text-uppercase mb-2">Penjualan {{ $filterText }}</div>
                                <h3 class="mb-0 fw-bold text-dark">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                            </div>
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.reports', ['filter' => $filter, 'custom_date' => $customDate]) }}" class="text-decoration-none">
                    <div class="card card-stat p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small fw-bold text-uppercase mb-2">Total Transaksi</div>
                                <h3 class="mb-0 fw-bold text-dark">{{ $totalTransactions }}</h3>
                            </div>
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-receipt"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.voids', ['filter' => $filter, 'custom_date' => $customDate]) }}" class="text-decoration-none">
                    <div class="card card-stat p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small fw-bold text-uppercase mb-2">Total Void</div>
                                <h3 class="mb-0 fw-bold text-dark">{{ $totalVoid }}</h3>
                            </div>
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-x-octagon"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#lowStockModal">
                    <div class="card card-stat p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small fw-bold text-uppercase mb-2">Stok Menipis</div>
                                <h3 class="mb-0 fw-bold text-dark">{{ $lowStock->count() }} Item</h3>
                            </div>
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Comparison Stats Row -->
        <div class="row g-4 mb-5">
            <!-- Today vs Yesterday -->
            <div class="col-md-6">
                <div class="card card-stat p-4" style="border-left: 5px solid #2e7d32 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-clock-history me-1"></i> Perbandingan Harian</div>
                        <span class="badge {{ $todayVsYesterdayDiff >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2 shadow-sm">
                            <i class="bi {{ $todayVsYesterdayDiff >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }} me-1"></i>
                            {{ $todayVsYesterdayDiff >= 0 ? '+' : '' }}{{ $todayVsYesterdayPercent }}%
                        </span>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Hari Ini</small>
                            <h4 class="fw-bold text-dark mb-0">Rp {{ number_format($todayTotal, 0, ',', '.') }}</h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Kemarin</small>
                            <h5 class="fw-semibold text-secondary mb-0">Rp {{ number_format($yesterdayTotal, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center text-muted small">
                        <span>Selisih Penjualan:</span>
                        <span class="fw-bold {{ $todayVsYesterdayDiff >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $todayVsYesterdayDiff >= 0 ? 'Surplus (+)' : 'Defisit (-)' }} Rp {{ number_format(abs($todayVsYesterdayDiff), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- This Month vs Last Month -->
            <div class="col-md-6">
                <div class="card card-stat p-4" style="border-left: 5px solid #0288d1 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-calendar-range me-1"></i> Perbandingan Bulanan (MoM)</div>
                        <span class="badge {{ $thisMonthVsLastMonthDiff >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2 shadow-sm">
                            <i class="bi {{ $thisMonthVsLastMonthDiff >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }} me-1"></i>
                            {{ $thisMonthVsLastMonthDiff >= 0 ? '+' : '' }}{{ $thisMonthVsLastMonthPercent }}%
                        </span>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Bulan Ini</small>
                            <h4 class="fw-bold text-dark mb-0">Rp {{ number_format($thisMonthTotal, 0, ',', '.') }}</h4>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Bulan Kemarin</small>
                            <h5 class="fw-semibold text-secondary mb-0">Rp {{ number_format($lastMonthTotal, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center text-muted small">
                        <span>Selisih Penjualan:</span>
                        <span class="fw-bold {{ $thisMonthVsLastMonthDiff >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $thisMonthVsLastMonthDiff >= 0 ? 'Surplus (+)' : 'Defisit (-)' }} Rp {{ number_format(abs($thisMonthVsLastMonthDiff), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Interactive Sales Chart -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card top-menu-card p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                        <h5 class="fw-bold m-0"><i class="bi bi-graph-up text-primary me-2"></i> Analisis & Tren Grafik Penjualan</h5>
                        <div class="btn-group shadow-sm rounded-pill p-1 bg-light" role="group" style="border: 1px solid rgba(0,0,0,0.05);">
                            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 active" id="btnChart7Days" onclick="switchChartMode('7days')">7 Hari Terakhir</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary border-0 rounded-pill px-3" id="btnChartTodayYesterday" onclick="switchChartMode('today_yesterday')">Hari Ini vs Kemarin</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary border-0 rounded-pill px-3" id="btnChartMonthLastMonth" onclick="switchChartMode('month_lastmonth')">Bulan Ini vs Bulan Kemarin</button>
                        </div>
                    </div>
                    <div>
                        <canvas id="salesChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card top-menu-card h-100 p-2">
                    <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                        <h5 class="fw-bold m-0"><i class="bi bi-graph-up-arrow text-success me-2"></i> Laporan Laba Rugi {{ $filterText }}</h5>
                    </div>
                    <div class="card-body px-4">
                        <div class="bg-light rounded-4 p-4 mb-4">
                            <div class="d-flex justify-content-between mb-3 border-bottom border-secondary border-opacity-25 pb-3">
                                <span class="text-muted fw-semibold">Total Pemasukan (Omset)</span>
                                <span class="fw-bold fs-5">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-bottom border-secondary border-opacity-25 pb-3">
                                <span class="text-muted fw-semibold">Total Harga Pokok (HPP)</span>
                                <span class="fw-bold text-danger fs-5">- Rp {{ number_format($totalSales - $profit, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <span class="fw-bold text-success fs-5">Laba Bersih (Profit)</span>
                                <span class="fw-bold text-success display-6">Rp {{ number_format($profit, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card top-menu-card h-100 p-2">
                    <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                        <h5 class="fw-bold m-0"><i class="bi bi-star-fill text-warning me-2"></i> 5 Menu Terlaris {{ $filterText }}</h5>
                    </div>
                    <div class="card-body px-4">
                        @if($topProducts->count() > 0)
                            @foreach($topProducts as $idx => $item)
                            <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-3 rounded-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-warning text-white fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        {{ $idx+1 }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $item->product->name ?? 'Unknown' }}</h6>
                                        <small class="text-muted">{{ $item->product->category->name ?? '' }}</small>
                                    </div>
                                </div>
                                <div class="bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill">
                                    {{ $item->total_qty }} Porsi
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                Belum ada data penjualan pada periode ini.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Stok Menipis -->
<div class="modal fade" id="lowStockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-warning bg-opacity-10 rounded-top-4 p-4">
                <h5 class="modal-title fw-bold text-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i> Daftar Stok Menipis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                @if($lowStock->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Nama Produk</th>
                                    <th class="text-center">Stok Sisa</th>
                                    <th class="text-center">Min Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStock as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->name }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-danger rounded-pill px-3 py-2">{{ $item->stock }}</span>
                                    </td>
                                    <td class="text-center text-muted">{{ $item->min_stock }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle fs-1 text-success d-block mb-3"></i>
                        Stok semua produk dalam keadaan aman.
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <a href="{{ route('products.index') }}" class="btn btn-outline-warning rounded-pill px-4 fw-bold w-100">Kelola Produk</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleDateInput() {
        const select = document.getElementById('filterSelect');
        const dateInput = document.getElementById('customDateInput');
        if (select.value === 'custom_date') {
            dateInput.classList.remove('d-none');
            // Do not submit immediately, let user pick a date. They can trigger submit by changing date
        } else {
            dateInput.classList.add('d-none');
            document.getElementById('filterForm').submit();
        }
    }

    let salesChart;
    const chartDataSets = {
        '7days': {
            labels: {!! json_encode($chartLabels ?? []) !!},
            datasets: [{
                label: 'Penjualan Harian (Rp)',
                data: {!! json_encode($chartData ?? []) !!},
                borderColor: '#2e7d32',
                backgroundColor: 'rgba(46, 125, 50, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: '#f57c00',
                pointRadius: 5,
                fill: true,
                tension: 0.4
            }]
        },
        'today_yesterday': {
            labels: Array.from({length: 24}, (_, i) => `${String(i).padStart(2, '0')}:00`),
            datasets: [
                {
                    label: 'Hari Ini (Rp)',
                    data: {!! json_encode($todayHourly ?? []) !!},
                    borderColor: '#2e7d32',
                    backgroundColor: 'rgba(46, 125, 50, 0.05)',
                    borderWidth: 3,
                    pointBackgroundColor: '#f57c00',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Kemarin (Rp)',
                    data: {!! json_encode($yesterdayHourly ?? []) !!},
                    borderColor: '#0288d1',
                    backgroundColor: 'rgba(2, 136, 209, 0.05)',
                    borderWidth: 3,
                    pointBackgroundColor: '#00acc1',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        'month_lastmonth': {
            labels: {!! json_encode($monthLabels ?? []) !!}.map(d => `Tgl ${d}`),
            datasets: [
                {
                    label: 'Bulan Ini (Rp)',
                    data: {!! json_encode($thisMonthDailyValues ?? []) !!},
                    borderColor: '#2e7d32',
                    backgroundColor: 'rgba(46, 125, 50, 0.05)',
                    borderWidth: 3,
                    pointBackgroundColor: '#f57c00',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Bulan Kemarin (Rp)',
                    data: {!! json_encode($lastMonthDailyValues ?? []) !!},
                    borderColor: '#0288d1',
                    backgroundColor: 'rgba(2, 136, 209, 0.05)',
                    borderWidth: 3,
                    pointBackgroundColor: '#00acc1',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4
                }
            ]
        }
    };

    function switchChartMode(mode) {
        // Toggle active button style
        const buttons = {
            '7days': document.getElementById('btnChart7Days'),
            'today_yesterday': document.getElementById('btnChartTodayYesterday'),
            'month_lastmonth': document.getElementById('btnChartMonthLastMonth')
        };
        
        Object.keys(buttons).forEach(key => {
            const btn = buttons[key];
            if (key === mode) {
                btn.className = 'btn btn-sm btn-success rounded-pill px-3 active';
            } else {
                btn.className = 'btn btn-sm btn-outline-secondary border-0 rounded-pill px-3';
            }
        });

        // Update chart data
        salesChart.data.labels = chartDataSets[mode].labels;
        salesChart.data.datasets = chartDataSets[mode].datasets;
        
        // Show legend if we are doing comparison (multiple datasets)
        salesChart.options.plugins.legend.display = chartDataSets[mode].datasets.length > 1;
        
        salesChart.update();
    }

    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartDataSets['7days'].labels,
                datasets: chartDataSets['7days'].datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { 
                        display: false,
                        position: 'top',
                        labels: {
                            font: { family: 'Poppins', size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: { family: 'Poppins', size: 13, weight: 'bold' },
                        bodyFont: { family: 'Poppins', size: 12 },
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { family: 'Poppins' },
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: { family: 'Poppins' }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
</body>
</html>
