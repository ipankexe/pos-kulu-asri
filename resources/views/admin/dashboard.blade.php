<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Rumah Makan Kulu Asri</title>

    <!-- Design Tokens & Google Fonts -->
    <link rel="stylesheet" href="{{ asset('css/design-tokens.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS & Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body { 
            font-family: var(--font-family-sans); 
            background: var(--color-background);
            color: var(--color-text-main);
            overflow-x: hidden;
        }

        .main-content { 
            padding: 36px 42px; 
            background: var(--color-background); 
            min-height: 100vh;
            width: 100%;
            box-sizing: border-box;
        }

        /* Top Header Styling */
        .dashboard-header-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: var(--ka-slate-900);
            margin-bottom: 4px;
        }

        .dashboard-header-subtitle {
            color: var(--ka-slate-500);
            font-size: 0.92rem;
            margin: 0;
        }

        /* Modern Filter Bar */
        .filter-select {
            background-color: #ffffff;
            border: 1.5px solid var(--ka-slate-200);
            color: var(--ka-emerald-800);
            font-weight: 700;
            font-size: 0.88rem;
            padding: 8px 16px;
            border-radius: var(--radius-full);
            cursor: pointer;
            box-shadow: var(--shadow-xs);
            transition: all var(--transition-fast);
        }
        .filter-select:focus {
            border-color: var(--ka-emerald-600);
            box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.12);
        }

        .date-badge {
            background: #ffffff;
            border: 1px solid var(--ka-slate-200);
            padding: 8px 16px;
            border-radius: var(--radius-full);
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--ka-slate-700);
            box-shadow: var(--shadow-xs);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Stat Cards */
        .card-stat { 
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg); 
            box-shadow: var(--shadow-sm); 
            transition: all var(--transition-normal); 
            background: #ffffff;
            position: relative;
            overflow: hidden;
        }
        .card-stat:hover { 
            transform: translateY(-3px); 
            box-shadow: var(--shadow-md); 
            border-color: rgba(4, 120, 87, 0.25);
        }

        .stat-icon {
            width: 44px; 
            height: 44px; 
            border-radius: var(--radius-md);
            display: flex; 
            align-items: center; 
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: var(--ka-slate-500);
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--ka-slate-900);
            letter-spacing: -0.02em;
            line-height: 1.2;
            font-variant-numeric: tabular-nums;
        }

        .stat-unit {
            font-size: 13px;
            font-weight: 500;
            color: var(--ka-slate-500);
        }

        /* Channel Performance Cards */
        .card-channel {
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            background: #ffffff;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
        }
        .card-channel::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }
        .card-channel.channel-pos::before { background: var(--ka-emerald-600); }
        .card-channel.channel-qr::before { background: var(--ka-amber-500); }
        .card-channel.channel-pending::before { background: #f59e0b; }
        .card-channel.channel-void::before { background: var(--ka-slate-400); }

        .card-channel:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        /* Comparison Widget Cards */
        .card-comparison {
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            background: #ffffff;
            box-shadow: var(--shadow-sm);
            position: relative;
        }

        /* Section Cards */
        .top-menu-card {
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg); 
            box-shadow: var(--shadow-sm);
            background: #ffffff;
        }

        .chart-toggle-scroll-wrap {
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 2px;
        }
        .chart-toggle-scroll-wrap::-webkit-scrollbar {
            display: none;
        }
        .chart-toggle-pills {
            white-space: nowrap;
            flex-shrink: 0;
        }

        .chart-toggle-btn {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: var(--radius-full);
            transition: all var(--transition-fast);
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }
        .chart-toggle-btn.active {
            background: var(--ka-emerald-700);
            color: white;
            box-shadow: 0 2px 8px rgba(4, 120, 87, 0.3);
        }
        .chart-toggle-btn:not(.active) {
            background: transparent;
            color: var(--ka-slate-600);
        }
        .chart-toggle-btn:not(.active):hover {
            background: rgba(0,0,0,0.05);
            color: var(--ka-slate-900);
        }

        .chart-canvas-container {
            position: relative;
            height: 320px;
        }

        .rank-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 12px;
            flex-shrink: 0;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .main-content {
                padding: 22px 18px !important;
                min-height: calc(100vh - 60px);
                width: 100% !important;
                max-width: 100vw !important;
            }
            .dashboard-header-title {
                font-size: 1.45rem;
            }
            .dashboard-header-subtitle {
                font-size: 0.85rem;
            }
            .chart-canvas-container {
                height: 270px;
            }
        }

        @media (max-width: 575.98px) {
            .main-content {
                padding: 14px 12px !important;
            }
            .dashboard-header-title {
                font-size: 1.3rem;
            }
            .dashboard-header-subtitle {
                font-size: 0.8rem;
            }
            .stat-icon {
                width: 34px;
                height: 34px;
                font-size: 1rem;
            }
            .stat-label {
                font-size: 0.68rem;
                margin-bottom: 2px;
                letter-spacing: 0.2px;
            }
            .stat-value {
                font-size: 1.15rem;
            }
            .stat-unit {
                font-size: 11px;
            }
            .filter-select {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
            .chart-toggle-btn {
                padding: 5px 11px;
                font-size: 0.74rem;
            }
            .chart-canvas-container {
                height: 220px;
            }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar Component -->
    @include('admin.sidebar')

    <!-- Main Content Canvas -->
    <div class="flex-grow-1 main-content">
        <!-- Top App Bar & Filters -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill" style="font-size: 11px; font-weight: 700;">
                        <i class="bi bi-circle-fill" style="font-size: 7px;"></i> POS Enterprise Live
                    </span>
                </div>
                <h1 class="dashboard-header-title">Ringkasan Operasional & Penjualan</h1>
                <p class="dashboard-header-subtitle">Selamat datang kembali, <strong>{{ auth()->user()->name }}</strong>! Berikut performa restoran Kulu Asri hari ini.</p>
            </div>

            <!-- Date & Period Filter Form -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center gap-2" id="filterForm">
                    <input type="date" name="custom_date" id="customDateInput" class="form-control form-control-sm rounded-pill border-success text-success fw-bold shadow-sm {{ ($filter ?? 'daily') == 'custom_date' ? '' : 'd-none' }}" value="{{ $customDate ?? \Carbon\Carbon::now()->toDateString() }}" onchange="document.getElementById('filterForm').submit()" style="max-width: 140px; font-size: 12px;">
                    
                    <select name="filter" id="filterSelect" class="form-select filter-select" onchange="toggleDateInput()">
                        <option value="daily" {{ ($filter ?? 'daily') == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="weekly" {{ ($filter ?? 'daily') == 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="monthly" {{ ($filter ?? 'daily') == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="yearly" {{ ($filter ?? 'daily') == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom_date" {{ ($filter ?? 'daily') == 'custom_date' ? 'selected' : '' }}>Tanggal Spesifik</option>
                    </select>
                </form>

                <div class="date-badge d-none d-lg-inline-flex">
                    <i class="bi bi-calendar-event text-success"></i>
                    <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>

        @php
            $filterText = [
                'daily' => 'Hari Ini',
                'weekly' => 'Minggu Ini',
                'monthly' => 'Bulan Ini',
                'yearly' => 'Tahun Ini',
                'custom_date' => 'Tgl ' . ($customDate ?? \Carbon\Carbon::now()->toDateString())
            ][$filter ?? 'daily'];
        @endphp

        <!-- Key Financial Metrics (Row 1: 2x2 Grid on Mobile, 4-col on Desktop) -->
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <!-- Penjualan -->
            <div class="col-6 col-lg-3">
                <a href="{{ route('admin.reports', ['filter' => $filter, 'custom_date' => $customDate]) }}" class="text-decoration-none">
                    <div class="card card-stat p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-1">
                            <div class="overflow-hidden">
                                <div class="stat-label text-truncate">Omset {{ $filterText }}</div>
                                <div class="stat-value text-success text-truncate">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                            </div>
                            <div class="stat-icon" style="background: var(--ka-emerald-50); color: var(--ka-emerald-700); border: 1px solid var(--ka-emerald-200);">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Transaksi -->
            <div class="col-6 col-lg-3">
                <a href="{{ route('admin.reports', ['filter' => $filter, 'custom_date' => $customDate]) }}" class="text-decoration-none">
                    <div class="card card-stat p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-1">
                            <div class="overflow-hidden">
                                <div class="stat-label text-truncate">Total Transaksi</div>
                                <div class="stat-value text-truncate">{{ $totalTransactions }} <span class="stat-unit">Struk</span></div>
                            </div>
                            <div class="stat-icon" style="background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd;">
                                <i class="bi bi-receipt-cutoff"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Void -->
            <div class="col-6 col-lg-3">
                <a href="{{ route('admin.voids', ['filter' => $filter, 'custom_date' => $customDate]) }}" class="text-decoration-none">
                    <div class="card card-stat p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-1">
                            <div class="overflow-hidden">
                                <div class="stat-label text-truncate">Audit Void</div>
                                <div class="stat-value text-danger text-truncate">{{ $totalVoid }} <span class="stat-unit">Item</span></div>
                            </div>
                            <div class="stat-icon" style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;">
                                <i class="bi bi-shield-x"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Stok Menipis -->
            <div class="col-6 col-lg-3">
                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#lowStockModal">
                    <div class="card card-stat p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-1">
                            <div class="overflow-hidden">
                                <div class="stat-label text-truncate">Peringatan Stok</div>
                                <div class="stat-value text-warning text-truncate">{{ $lowStock->count() }} <span class="stat-unit">Menu</span></div>
                            </div>
                            <div class="stat-icon" style="background: var(--ka-amber-50); color: var(--ka-amber-700); border: 1px solid var(--ka-amber-200);">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Channel Performance Stats Row (2x2 Grid on Mobile, 4-col on Desktop) -->
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <div class="col-6 col-lg-3">
                <a href="{{ route('admin.reports', ['source' => 'pos', 'filter' => $filter, 'custom_date' => $customDate]) }}" class="text-decoration-none">
                    <div class="card card-channel channel-pos p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-1">
                            <div class="overflow-hidden">
                                <div class="stat-label text-truncate d-flex align-items-center gap-1">
                                    <i class="bi bi-display"></i> Kasir POS
                                </div>
                                <div class="stat-value text-dark text-truncate">{{ $posTrxCount }} <span class="stat-unit">Trx</span></div>
                                <div class="fw-bold text-truncate" style="color: var(--ka-emerald-700); font-size: 13px;">
                                    Rp {{ number_format($posRevenue, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="stat-icon d-none d-sm-flex" style="background: var(--ka-emerald-50); color: var(--ka-emerald-700);">
                                <i class="bi bi-person-workspace"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <a href="{{ route('admin.reports', ['source' => 'qr', 'filter' => $filter, 'custom_date' => $customDate]) }}" class="text-decoration-none">
                    <div class="card card-channel channel-qr p-3 p-md-4 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-1">
                            <div class="overflow-hidden">
                                <div class="stat-label text-truncate d-flex align-items-center gap-1">
                                    <i class="bi bi-qr-code-scan"></i> QR Meja
                                </div>
                                <div class="stat-value text-dark text-truncate">{{ $qrTrxCount }} <span class="stat-unit">Trx</span></div>
                                <div class="fw-bold text-truncate" style="color: var(--ka-amber-600); font-size: 13px;">
                                    Rp {{ number_format($qrRevenue, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="stat-icon d-none d-sm-flex" style="background: var(--ka-amber-50); color: var(--ka-amber-600);">
                                <i class="bi bi-phone"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card card-channel channel-pending p-3 p-md-4 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-1">
                        <div class="overflow-hidden">
                            <div class="stat-label text-truncate d-flex align-items-center gap-1">
                                <i class="bi bi-hourglass-split"></i> Pending
                            </div>
                            <div class="stat-value text-dark text-truncate">{{ $totalPending }} <span class="stat-unit">Trx</span></div>
                            <small class="text-warning fw-semibold text-truncate d-block" style="font-size: 12px;">Menunggu Bayar</small>
                        </div>
                        <div class="stat-icon d-none d-sm-flex" style="background: #fffbeb; color: #d97706;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card card-channel channel-void p-3 p-md-4 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-1">
                        <div class="overflow-hidden">
                            <div class="stat-label text-truncate d-flex align-items-center gap-1">
                                <i class="bi bi-x-circle"></i> Batal/Void
                            </div>
                            <div class="stat-value text-dark text-truncate">{{ $totalFailed }} <span class="stat-unit">Trx</span></div>
                            <small class="text-muted text-truncate d-block" style="font-size: 12px;">Tidak Terproses</small>
                        </div>
                        <div class="stat-icon d-none d-sm-flex" style="background: var(--ka-slate-100); color: var(--ka-slate-500);">
                            <i class="bi bi-slash-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Comparison Cards (Row 3) -->
        <div class="row g-2 g-md-3 mb-3 mb-md-4">
            <!-- Daily Comparison -->
            <div class="col-12 col-lg-6">
                <div class="card-comparison p-3 p-md-4" style="border-top: 3px solid var(--ka-emerald-600);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                            <i class="bi bi-calendar2-day text-success"></i> Perbandingan Harian
                        </div>
                        <span id="dailyGrowthBadge" class="badge {{ $todayVsYesterdayDiff >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill px-2.5 py-1 fw-bold shadow-sm" style="font-size: 11px;">
                            <i id="dailyGrowthIcon" class="bi {{ $todayVsYesterdayDiff >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }} me-1"></i>
                            <span id="dailyGrowthPercent">{{ $todayVsYesterdayDiff >= 0 ? '+' : '' }}{{ $todayVsYesterdayPercent }}%</span>
                        </span>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-6 border-end">
                            <small id="dailyPrimaryLabel" class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.72rem;">Hari Ini</small>
                            <h4 id="dailyPrimaryTotal" class="fw-bold text-dark mb-0 tabular-nums fs-5 fs-md-4">Rp {{ number_format($todayTotal, 0, ',', '.') }}</h4>
                        </div>
                        <div class="col-6 ps-3">
                            <small id="dailySecondaryLabel" class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.72rem;">Kemarin</small>
                            <h5 id="dailySecondaryTotal" class="fw-semibold text-secondary mb-0 tabular-nums fs-6 fs-md-5">Rp {{ number_format($yesterdayTotal, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center text-muted small" style="font-size: 12px;">
                        <span>Selisih Penjualan:</span>
                        <span id="dailyDiffText" class="fw-bold {{ $todayVsYesterdayDiff >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $todayVsYesterdayDiff >= 0 ? 'Surplus (+)' : 'Defisit (-)' }} Rp {{ number_format(abs($todayVsYesterdayDiff), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Monthly Comparison -->
            <div class="col-12 col-lg-6">
                <div class="card-comparison p-3 p-md-4" style="border-top: 3px solid var(--ka-amber-600);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                            <i class="bi bi-calendar3-range text-warning"></i> Perbandingan Bulanan (MoM)
                        </div>
                        <span id="monthlyGrowthBadge" class="badge {{ $thisMonthVsLastMonthDiff >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill px-2.5 py-1 fw-bold shadow-sm" style="font-size: 11px;">
                            <i id="monthlyGrowthIcon" class="bi {{ $thisMonthVsLastMonthDiff >= 0 ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }} me-1"></i>
                            <span id="monthlyGrowthPercent">{{ $thisMonthVsLastMonthDiff >= 0 ? '+' : '' }}{{ $thisMonthVsLastMonthPercent }}%</span>
                        </span>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-6 border-end">
                            <small id="monthlyPrimaryLabel" class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.72rem;">Bulan Ini</small>
                            <h4 id="monthlyPrimaryTotal" class="fw-bold text-dark mb-0 tabular-nums fs-5 fs-md-4">Rp {{ number_format($thisMonthTotal, 0, ',', '.') }}</h4>
                        </div>
                        <div class="col-6 ps-3">
                            <small id="monthlySecondaryLabel" class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.72rem;">Bulan Kemarin</small>
                            <h5 id="monthlySecondaryTotal" class="fw-semibold text-secondary mb-0 tabular-nums fs-6 fs-md-5">Rp {{ number_format($lastMonthTotal, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center text-muted small" style="font-size: 12px;">
                        <span>Selisih Penjualan:</span>
                        <span id="monthlyDiffText" class="fw-bold {{ $thisMonthVsLastMonthDiff >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $thisMonthVsLastMonthDiff >= 0 ? 'Surplus (+)' : 'Defisit (-)' }} Rp {{ number_format(abs($thisMonthVsLastMonthDiff), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Chart Row -->
        <div class="row mb-3 mb-md-4">
            <div class="col-12">
                <div class="card top-menu-card p-3 p-md-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold m-0 d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                                <i class="bi bi-graph-up text-success"></i> Analisis & Tren Grafik Penjualan
                            </h5>
                            <small class="text-muted d-block" style="font-size: 0.8rem;">Grafik tren pendapatan berkala untuk evaluasi performa restoran.</small>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap w-100 w-md-auto justify-content-start justify-content-md-end">
                            <!-- Date Selectors -->
                            <input type="date" id="chartDatePrimary" class="form-control form-control-sm rounded-pill shadow-sm border-success text-success fw-bold d-none" style="width: 130px; font-size: 12px;" onchange="loadComparisonData()" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                            <span id="comparisonSeparatorDate" class="text-muted small fw-bold d-none">vs</span>
                            <input type="date" id="chartDateSecondary" class="form-control form-control-sm rounded-pill shadow-sm border-info text-info fw-bold d-none" style="width: 130px; font-size: 12px;" onchange="loadComparisonData()" value="{{ \Carbon\Carbon::now()->subDay()->toDateString() }}">

                            <!-- Month Selectors -->
                            <input type="month" id="chartMonthPrimary" class="form-control form-control-sm rounded-pill shadow-sm border-success text-success fw-bold d-none" style="width: 130px; font-size: 12px;" onchange="loadComparisonData()" value="{{ \Carbon\Carbon::now()->format('Y-m') }}">
                            <span id="comparisonSeparatorMonth" class="text-muted small fw-bold d-none">vs</span>
                            <input type="month" id="chartMonthSecondary" class="form-control form-control-sm rounded-pill shadow-sm border-info text-info fw-bold d-none" style="width: 130px; font-size: 12px;" onchange="loadComparisonData()" value="{{ \Carbon\Carbon::now()->subMonth()->format('Y-m') }}">

                            <!-- Toggle Buttons with Touch Scroll Container -->
                            <div class="chart-toggle-scroll-wrap">
                                <div class="bg-light p-1 rounded-pill d-inline-flex border chart-toggle-pills" style="background: var(--ka-slate-100);">
                                    <button type="button" class="chart-toggle-btn active" id="btnChart7Days" onclick="switchChartMode('7days')">7 Hari Terakhir</button>
                                    <button type="button" class="chart-toggle-btn" id="btnChartTodayYesterday" onclick="switchChartMode('today_yesterday')">Hari Ini vs Kemarin</button>
                                    <button type="button" class="chart-toggle-btn" id="btnChartMonthLastMonth" onclick="switchChartMode('month_lastmonth')">Bulan Ini vs Lalu</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chart-canvas-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit / Loss & Top 5 Menu (Row 5) -->
        <div class="row g-3 g-md-4">
            <!-- Laba Bersih -->
            <div class="col-12 col-lg-6">
                <div class="card top-menu-card h-100 p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
                        <h5 class="fw-bold m-0 d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                            <i class="bi bi-pie-chart text-success"></i> Estimasi Laba Rugi {{ $filterText }}
                        </h5>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1" style="font-size: 11px;">Margin Sehat</span>
                    </div>

                    <div class="p-3 p-md-4 rounded-3 mb-3" style="background: var(--ka-canvas-bg); border: 1px solid var(--ka-slate-200);">
                        <div class="d-flex justify-content-between mb-2 mb-md-3 border-bottom pb-2 pb-md-3">
                            <span class="text-muted fw-semibold" style="font-size: 0.85rem;">Total Pemasukan (Omset)</span>
                            <span class="fw-bold fs-6 fs-md-5 tabular-nums">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 mb-md-3 border-bottom pb-2 pb-md-3">
                            <span class="text-muted fw-semibold" style="font-size: 0.85rem;">Estimasi HPP (Modal Pokok)</span>
                            <span class="fw-bold text-danger fs-6 fs-md-5 tabular-nums">- Rp {{ number_format($totalSales - $profit, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 flex-wrap gap-2">
                            <div>
                                <span class="fw-bold fs-6 fs-md-5 d-block" style="color: var(--ka-emerald-800);">Laba Bersih (Net Profit)</span>
                                <small class="text-muted" style="font-size: 0.75rem;">Sebelum beban operasional non-menu</small>
                            </div>
                            <span class="fw-bolder fs-4 fs-md-3 tabular-nums" style="color: var(--ka-emerald-700);">
                                Rp {{ number_format($profit, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top 5 Menu Terlaris -->
            <div class="col-12 col-lg-6">
                <div class="card top-menu-card h-100 p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4">
                        <h5 class="fw-bold m-0 d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                            <i class="bi bi-award-fill text-warning"></i> 5 Menu Terlaris {{ $filterText }}
                        </h5>
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 11px;">Favorit Pelanggan</span>
                    </div>

                    @if($topProducts->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($topProducts as $idx => $item)
                            <div class="d-flex justify-content-between align-items-center p-2.5 p-md-3 rounded-3" style="background: var(--ka-canvas-bg); border: 1px solid var(--ka-slate-100);">
                                <div class="d-flex align-items-center gap-2 gap-md-3 overflow-hidden">
                                    <div class="rank-circle {{ $idx == 0 ? 'bg-warning text-dark' : ($idx == 1 ? 'bg-secondary text-white' : 'bg-light text-dark border') }}">
                                        {{ $idx + 1 }}
                                    </div>
                                    <div class="text-truncate">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 13.5px;">{{ $item->product->name ?? 'Unknown Menu' }}</h6>
                                        <small class="text-muted" style="font-size: 11px;">{{ $item->product->category->name ?? 'Kategori' }}</small>
                                    </div>
                                </div>
                                <div class="badge rounded-pill px-2.5 py-1.5 fw-bold flex-shrink-0" style="background: var(--ka-emerald-50); color: var(--ka-emerald-800); border: 1px solid var(--ka-emerald-200); font-size: 11.5px;">
                                    {{ $item->total_qty }} Porsi
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                            <span style="font-size: 13px;">Belum ada riwayat penjualan pada filter periode ini.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Stok Menipis -->
<div class="modal fade" id="lowStockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 p-4" style="background: var(--ka-amber-50); border-bottom: 1px solid var(--ka-amber-200) !important;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                    <h5 class="modal-title fw-bold text-dark mb-0">Peringatan Stok Menipis</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                @if($lowStock->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted small text-uppercase">
                                    <th>Nama Menu</th>
                                    <th class="text-center">Sisa Stok</th>
                                    <th class="text-center">Batas Min</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStock as $item)
                                <tr>
                                    <td class="fw-bold">{{ $item->name }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-danger rounded-pill px-3 py-1">{{ $item->stock }}</span>
                                    </td>
                                    <td class="text-center text-muted fw-semibold">{{ $item->min_stock }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle-fill fs-1 text-success d-block mb-3"></i>
                        Semua stok bahan & menu saat ini dalam batas aman.
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <a href="{{ route('products.index') }}" class="btn btn-ka-primary w-100 text-center">Kelola Inventori Produk &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function toggleDateInput() {
        const select = document.getElementById('filterSelect');
        const dateInput = document.getElementById('customDateInput');
        if (select.value === 'custom_date') {
            dateInput.classList.remove('d-none');
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
                borderColor: '#047857',
                backgroundColor: 'rgba(4, 120, 87, 0.08)',
                borderWidth: 3,
                pointBackgroundColor: '#d97706',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                fill: true,
                tension: 0.35
            }]
        }
    };

    let currentMode = '7days';

    function switchChartMode(mode) {
        currentMode = mode;

        const buttons = {
            '7days': document.getElementById('btnChart7Days'),
            'today_yesterday': document.getElementById('btnChartTodayYesterday'),
            'month_lastmonth': document.getElementById('btnChartMonthLastMonth')
        };
        
        Object.keys(buttons).forEach(key => {
            const btn = buttons[key];
            if (key === mode) {
                btn.className = 'chart-toggle-btn active';
            } else {
                btn.className = 'chart-toggle-btn';
            }
        });

        const datePrimary = document.getElementById('chartDatePrimary');
        const dateSecondary = document.getElementById('chartDateSecondary');
        const separatorDate = document.getElementById('comparisonSeparatorDate');
        
        const monthPrimary = document.getElementById('chartMonthPrimary');
        const monthSecondary = document.getElementById('chartMonthSecondary');
        const separatorMonth = document.getElementById('comparisonSeparatorMonth');

        if (mode === '7days') {
            datePrimary.classList.add('d-none');
            dateSecondary.classList.add('d-none');
            separatorDate.classList.add('d-none');
            monthPrimary.classList.add('d-none');
            monthSecondary.classList.add('d-none');
            separatorMonth.classList.add('d-none');

            salesChart.data.labels = chartDataSets['7days'].labels;
            salesChart.data.datasets = chartDataSets['7days'].datasets;
            salesChart.options.plugins.legend.display = false;
            salesChart.update();
        } else if (mode === 'today_yesterday') {
            datePrimary.classList.remove('d-none');
            dateSecondary.classList.remove('d-none');
            separatorDate.classList.remove('d-none');
            monthPrimary.classList.add('d-none');
            monthSecondary.classList.add('d-none');
            separatorMonth.classList.add('d-none');

            loadComparisonData();
        } else if (mode === 'month_lastmonth') {
            datePrimary.classList.add('d-none');
            dateSecondary.classList.add('d-none');
            separatorDate.classList.add('d-none');
            monthPrimary.classList.remove('d-none');
            monthSecondary.classList.remove('d-none');
            separatorMonth.classList.remove('d-none');

            loadComparisonData();
        }
    }

    async function loadComparisonData() {
        if (currentMode === '7days') return;

        let url = '{{ route("admin.dashboard.chart_comparison") }}?mode=' + currentMode;

        if (currentMode === 'today_yesterday') {
            const datePrim = document.getElementById('chartDatePrimary').value;
            const dateSec = document.getElementById('chartDateSecondary').value;
            url += '&date_primary=' + datePrim + '&date_secondary=' + dateSec;
        } else if (currentMode === 'month_lastmonth') {
            const monthPrim = document.getElementById('chartMonthPrimary').value;
            const monthSec = document.getElementById('chartMonthSecondary').value;
            url += '&month_primary=' + monthPrim + '&month_secondary=' + monthSec;
        }

        const canvas = document.getElementById('salesChart');
        canvas.style.opacity = '0.5';

        try {
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success) {
                salesChart.data.labels = data.labels;
                salesChart.data.datasets = [
                    {
                        label: data.primary_label + ' (Rp)',
                        data: data.primary_data,
                        borderColor: '#047857',
                        backgroundColor: 'rgba(4, 120, 87, 0.08)',
                        borderWidth: 3,
                        pointBackgroundColor: '#d97706',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        fill: true,
                        tension: 0.35
                    },
                    {
                        label: data.secondary_label + ' (Rp)',
                        data: data.secondary_data,
                        borderColor: '#0284c7',
                        backgroundColor: 'rgba(2, 132, 199, 0.05)',
                        borderWidth: 3,
                        pointBackgroundColor: '#0284c7',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        fill: true,
                        tension: 0.35
                    }
                ];
                salesChart.options.plugins.legend.display = true;
                salesChart.update();

                if (currentMode === 'today_yesterday') {
                    const badge = document.getElementById('dailyGrowthBadge');
                    const icon = document.getElementById('dailyGrowthIcon');
                    const percentText = document.getElementById('dailyGrowthPercent');
                    
                    percentText.innerText = (data.diff >= 0 ? '+' : '') + data.percent + '%';
                    
                    if (data.diff >= 0) {
                        badge.className = 'badge bg-success rounded-pill px-3 py-1 fw-bold shadow-sm';
                        icon.className = 'bi bi-graph-up-arrow me-1';
                    } else {
                        badge.className = 'badge bg-danger rounded-pill px-3 py-1 fw-bold shadow-sm';
                        icon.className = 'bi bi-graph-down-arrow me-1';
                    }

                    document.getElementById('dailyPrimaryLabel').innerText = data.primary_label;
                    document.getElementById('dailyPrimaryTotal').innerText = data.formatted_primary_total;
                    document.getElementById('dailySecondaryLabel').innerText = data.secondary_label;
                    document.getElementById('dailySecondaryTotal').innerText = data.formatted_secondary_total;
                    
                    const diffText = document.getElementById('dailyDiffText');
                    diffText.innerText = data.formatted_diff;
                    diffText.className = data.diff >= 0 ? 'fw-bold text-success' : 'fw-bold text-danger';
                } else if (currentMode === 'month_lastmonth') {
                    const badge = document.getElementById('monthlyGrowthBadge');
                    const icon = document.getElementById('monthlyGrowthIcon');
                    const percentText = document.getElementById('monthlyGrowthPercent');
                    
                    percentText.innerText = (data.diff >= 0 ? '+' : '') + data.percent + '%';
                    
                    if (data.diff >= 0) {
                        badge.className = 'badge bg-success rounded-pill px-3 py-1 fw-bold shadow-sm';
                        icon.className = 'bi bi-graph-up-arrow me-1';
                    } else {
                        badge.className = 'badge bg-danger rounded-pill px-3 py-1 fw-bold shadow-sm';
                        icon.className = 'bi bi-graph-down-arrow me-1';
                    }

                    document.getElementById('monthlyPrimaryLabel').innerText = data.primary_label;
                    document.getElementById('monthlyPrimaryTotal').innerText = data.formatted_primary_total;
                    document.getElementById('monthlySecondaryLabel').innerText = data.secondary_label;
                    document.getElementById('monthlySecondaryTotal').innerText = data.formatted_secondary_total;
                    
                    const diffText = document.getElementById('monthlyDiffText');
                    diffText.innerText = data.formatted_diff;
                    diffText.className = data.diff >= 0 ? 'fw-bold text-success' : 'fw-bold text-danger';
                }
            }
        } catch (error) {
            console.error("AJAX Error: ", error);
        } finally {
            canvas.style.opacity = '1';
        }
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
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: false,
                        position: 'top',
                        labels: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 13, weight: 'bold' },
                        bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12 },
                        padding: 12,
                        cornerRadius: 8,
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
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#64748b',
                            callback: function(value) {
                                return 'Rp ' + (value >= 1000 ? (value/1000).toLocaleString('id-ID') + 'k' : value);
                            }
                        },
                        grid: {
                            color: 'rgba(226, 232, 240, 0.6)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            color: '#64748b'
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
