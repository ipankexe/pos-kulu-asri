<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - Rumah Makan Kulu Asri</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            overflow-x: hidden;
        }
        /* Rich Colors & Gradients */
        .bg-brand-green { background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%) !important; color: white; }
        .bg-brand-orange { background: linear-gradient(135deg, #f57c00 0%, #e65100 100%) !important; color: white; }
        .text-brand-green { color: #2e7d32 !important; }
        .text-brand-orange { color: #f57c00 !important; }
        
        /* Layout */
        .pos-container { height: 100vh; display: flex; flex-direction: column; }
        .pos-header { 
            height: 70px; 
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0,0,0,0.05); 
            z-index: 10; position: relative; 
        }
        .pos-body { flex: 1; display: none; overflow: hidden; } /* Hidden by default */
        .checkout-screen { display: none; flex: 1; overflow-y: auto; background: #fdfbfb; padding: 30px; }
        
        /* Welcome Screen */
        .welcome-screen {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1974&auto=format&fit=crop') center/cover no-repeat;
            position: relative;
        }
        .welcome-screen::before {
            content: '';
            position: absolute;
            top:0; left:0; right:0; bottom:0;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(5px);
        }
        .welcome-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 1200px;
            width: 100%;
            padding: 0 20px;
        }
        .action-btn {
            width: 235px;
            height: 235px;
            border-radius: 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            text-decoration: none;
        }
        .action-btn i { font-size: 4.5rem; margin-bottom: 12px; }
        .action-btn:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 25px 45px rgba(0,0,0,0.15); }
        .btn-order-menu { background: linear-gradient(135deg, #43a047 0%, #2e7d32 100%); color: white; }
        .btn-order-taker { background: linear-gradient(135deg, #ff9800 0%, #ef6c00 100%); color: white; }
        .btn-order-qr { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; }
        .btn-income-today { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); color: white; }

        /* Products Area */
        .products-area { flex: 7; padding: 25px; overflow-y: auto; background: transparent; }
        
        .category-pills .btn { 
            border-radius: 30px; margin-right: 12px; padding: 10px 25px; 
            font-weight: 600; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            background: white; border: none; color: #555;
        }
        .category-pills .btn.active, .category-pills .btn:hover { 
            background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%); 
            color: white; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(46, 125, 50, 0.3);
        }
        
        .product-card { 
            border: none; border-radius: 20px; 
            background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.04); 
            transition: all 0.3s; cursor: pointer; height: 100%;
            overflow: hidden;
        }
        .product-card:hover { 
            transform: translateY(-8px) scale(1.02); 
            box-shadow: 0 15px 35px rgba(46, 125, 50, 0.2); 
        }
        .product-img { 
            height: 140px; 
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); 
            display: flex; align-items: center; justify-content: center; 
            font-size: 3.5rem; color: #4caf50; 
        }
        .product-price { font-weight: 800; color: #f57c00; font-size: 1.25rem; }
        
        /* Cart Area */
        .cart-area { 
            flex: 3; background: #ffffff; display: flex; flex-direction: column; 
            box-shadow: -10px 0 30px rgba(0,0,0,0.05); z-index: 5;
        }
        .cart-header { padding: 25px 20px; background: linear-gradient(180deg, #fafafa 0%, #ffffff 100%); border-bottom: 1px solid #f0f0f0; }
        .cart-items { flex: 1; overflow-y: auto; padding: 0 20px; }
        .cart-item { padding: 15px 0; border-bottom: 1px dashed #e0e0e0; display: flex; justify-content: space-between; align-items: center; }
        
        .qty-input {
            width: 50px; text-align: center; font-weight: bold; border: 1px solid #ddd; border-radius: 8px; margin: 0 5px;
        }
        .qty-btn { 
            background: #f1f3f5; border: none; color: #2e7d32; font-weight: bold; 
            width: 30px; height: 30px; border-radius: 8px; cursor: pointer; transition: 0.2s;
        }
        .qty-btn:hover { background: #e2e6ea; }
        
        .cart-footer { 
            padding: 25px 20px; background: white; border-top: 1px solid #f0f0f0; 
            box-shadow: 0 -10px 20px rgba(0,0,0,0.02); 
        }
        .total-row { display: flex; justify-content: space-between; font-size: 1.6rem; font-weight: 800; color: #1b5e20; margin-bottom: 20px; }
        .btn-pay { height: 65px; font-size: 1.3rem; font-weight: 700; border-radius: 15px; box-shadow: 0 8px 20px rgba(46, 125, 50, 0.3); }
        .btn-pay:hover { box-shadow: 0 12px 25px rgba(46, 125, 50, 0.4); transform: translateY(-2px); }
        
        .form-control-custom { border-radius: 12px; border: 2px solid #eee; padding: 10px 15px; font-weight: 500; }
        .form-control-custom:focus { border-color: #2e7d32; box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.1); }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .bi-spin { display: inline-block; animation: spin 0.8s linear infinite; }
    </style>
</head>
<body>

<div class="pos-container">
    <!-- Header -->
    <div class="pos-header d-flex justify-content-between align-items-center px-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-brand-green rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="bi bi-shop fs-4 text-white"></i>
            </div>
            <div>
                <h3 class="m-0 fw-bold text-brand-green" style="letter-spacing: -0.5px; line-height: 1.1;">Kulu Asri <span class="text-brand-orange">POS</span></h3>
                <small style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; color: #b45309; font-weight: 700; font-size: 11.5px; display: block; margin-top: 2px;">Kulu Asri - Jagonya Ikan Bakar!</small>
            </div>
            
            <!-- Home button (hidden on welcome screen) -->
            <button class="btn btn-outline-secondary rounded-pill ms-3 shadow-sm d-none" id="btn-home" onclick="goHome()">
                <i class="bi bi-house-door-fill me-1"></i> Beranda
            </button>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-warning fw-bold rounded-pill px-3 shadow-sm position-relative" onclick="showQrOrdersModal()">
                <i class="bi bi-qr-code-scan me-1"></i> Pesanan QR
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="qrOrdersBadge">0</span>
            </button>
            <button class="btn btn-outline-danger fw-bold rounded-pill px-4 shadow-sm" onclick="showEodModal()">
                <i class="bi bi-door-closed me-2"></i> Tutup Toko
            </button>
            <a href="{{ route('pos.history') }}" class="btn btn-outline-success fw-bold rounded-pill px-4 shadow-sm">
                <i class="bi bi-clock-history me-2"></i> Riwayat Transaksi
            </a>
            <div class="d-flex align-items-center gap-3 border-start ps-4">
                <div class="text-end">
                    <span class="d-block fw-bold text-dark" style="font-size: 1rem;">{{ auth()->user()->name }}</span>
                    <span class="text-success fw-semibold" style="font-size: 0.8rem;"><i class="bi bi-circle-fill small text-success me-1"></i> Kasir Aktif</span>
                </div>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-light rounded-circle p-2 shadow-sm" title="Logout">
                    <i class="bi bi-power text-danger fs-5"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>
    </div>

    <!-- Welcome Screen -->
    <div class="welcome-screen" id="welcome-screen">
        <div class="welcome-content">
            <h2 class="fw-bold mb-5" style="color: #2e7d32; font-size: 2.5rem; text-shadow: 0 2px 10px rgba(0,0,0,0.05);">Pilih Aktivitas Kasir</h2>
            <div class="d-flex gap-4 justify-content-center flex-wrap">
                <button class="action-btn btn-order-menu" onclick="openMode('order')">
                    <i class="bi bi-journal-plus"></i>
                    <span>Pesanan Baru</span>
                    <small class="fs-6 fw-normal mt-2 opacity-75">Buat Pesanan Baru</small>
                </button>
                <button class="action-btn btn-order-taker" onclick="openMode('payment')">
                    <i class="bi bi-wallet2"></i>
                    <span>Kasir / Pembayaran</span>
                    <small class="fs-6 fw-normal mt-2 opacity-75">Pembayaran / Kasir</small>
                </button>
                <button class="action-btn btn-order-qr position-relative" onclick="showQrOrdersModal()">
                    <span class="position-absolute top-0 end-0 m-3 badge rounded-pill bg-danger fs-6 d-none" id="qrBadgeBig">0</span>
                    <i class="bi bi-qr-code-scan"></i>
                    <span>Pesanan QR</span>
                    <small class="fs-6 fw-normal mt-2 opacity-75">Order Masuk dari Meja</small>
                </button>
                <button class="action-btn btn-income-today position-relative" onclick="showTodayIncomeModal()">
                    <span class="position-absolute top-0 end-0 m-3 badge rounded-pill bg-white text-dark fw-bold shadow-sm" id="welcomeTodayIncomeBadge" style="font-size: 0.85rem;">Rp 0</span>
                    <i class="bi bi-cash-coin"></i>
                    <span>Pendapatan Hari Ini</span>
                    <small class="fs-6 fw-normal mt-2 opacity-75" id="welcomeTodayIncomeSub">Lacak Pemasukan Hari Ini</small>
                </button>
            </div>
        </div>
    </div>

    <!-- Body (POS Grid) -->
    <div class="pos-body" id="pos-body">
        <!-- Products Area -->
        <div class="products-area">
            <!-- Search & Categories -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light">
                <div class="category-pills d-flex overflow-auto pb-2" id="category-filters">
                    <button class="btn active" onclick="filterProducts('all', this)">Semua Menu</button>
                    @foreach($categories as $category)
                        <button class="btn" onclick="filterProducts({{ $category->id }}, this)">{{ $category->name }}</button>
                    @endforeach
                </div>
                <div class="position-relative" style="width: 350px;">
                    <i class="bi bi-search position-absolute fs-5" style="left: 15px; top: 12px; color: #aaa;"></i>
                    <input type="text" id="search-input" class="form-control form-control-custom rounded-pill ps-5" placeholder="Cari menu... (Tekan F2)" onkeyup="searchProducts(this.value)">
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row g-4" id="products-grid">
                @foreach($products as $product)
                <div class="col-md-4 col-lg-3 col-xl-2 product-item" data-category="{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}">
                    <div class="card product-card {{ $product->stock <= 0 ? 'opacity-50' : '' }}" 
                         @if($product->stock > 0) onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})" @else style="cursor:not-allowed;" @endif>
                        <div class="product-img">
                            @if($product->category->name == 'Minuman') <i class="bi bi-cup-straw text-info"></i>
                            @elseif($product->category->name == 'Snack') <i class="bi bi-basket text-warning"></i>
                            @else <i class="bi bi-egg-fried text-danger"></i> @endif
                        </div>
                        <div class="card-body text-center p-3">
                            <h6 class="card-title fw-bold text-dark mb-1 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
                            <p class="text-muted small mb-2">{{ $product->category->name }}</p>
                            <div class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            <div class="mt-2 fw-bold small {{ $product->stock <= 0 ? 'text-danger' : 'text-success' }}"><i class="bi bi-box-seam me-1"></i> Sisa: {{ $product->stock }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Cart Area -->
        <div class="cart-area">
            <div class="cart-header">
                <h4 class="fw-bold mb-3 text-dark"><i class="bi bi-basket3-fill text-brand-green me-2"></i> Keranjang</h4>
                <div class="row g-3">
                    <div class="col-12">
                        <input type="text" class="form-control form-control-custom" id="customer_name" placeholder="👤 Nama Pelanggan (Wajib)" oninput="checkValidation()" onchange="checkValidation()">
                    </div>
                    <div class="col-12">
                        <input type="text" class="form-control form-control-custom bg-white fw-bold text-primary" id="table_number" placeholder="🏷️ Meja" readonly oninput="checkValidation()" onchange="checkValidation()">
                    </div>
                </div>
            </div>
            
            <div class="cart-items" id="cart-items">
                <!-- Cart items will be rendered here by JS -->
            </div>

            <div class="cart-footer">
                <div class="d-flex justify-content-between mb-2 fs-5">
                    <span class="text-muted fw-semibold">Subtotal</span>
                    <span class="fw-bold text-dark" id="subtotal">Rp 0</span>
                </div>
                <div class="total-row mt-3 pt-3 border-top">
                    <span>TOTAL</span>
                    <span id="grand-total">Rp 0</span>
                </div>
                <div class="row g-2 mt-2">
                    <div class="col-4">
                        <button class="btn btn-light border-danger text-danger w-100 h-100 fw-bold rounded-3" onclick="goHome()">
                            <i class="bi bi-x-circle-fill mb-1 d-block"></i> Tutup
                        </button>
                    </div>
                    <div class="col-8">
                        <button class="btn bg-brand-green w-100 btn-pay text-white" id="btn-action" onclick="openPaymentModal()">
                            BAYAR (F4) <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Checkout Screen -->
<div class="checkout-screen" id="checkout-screen">
    <div class="container" style="max-width: 900px;">
        <h3 class="fw-bold text-dark mb-4"><i class="bi bi-receipt text-brand-green me-2"></i> Detail Pembayaran</h3>
        <div class="row g-4">
            <!-- Left: Order Summary -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-1">Daftar Pesanan</h5>
                        <p class="text-muted small mb-0" id="checkout-customer-info">Pelanggan: -, Meja: -</p>
                    </div>
                    <div class="card-body px-4">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <thead>
                                    <tr class="border-bottom text-muted small">
                                        <th class="fw-semibold">Item</th>
                                        <th class="text-center fw-semibold">Qty</th>
                                        <th class="text-end fw-semibold">Harga</th>
                                        <th class="text-end fw-semibold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="checkout-order-items">
                                    <!-- Items here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right: Payment form -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10 h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-success mb-4"><i class="bi bi-wallet2 me-2"></i> Pembayaran</h5>
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-semibold">Subtotal</span>
                            <span class="fw-bold fs-5 text-dark" id="checkout-subtotal">Rp 0</span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Diskon (Rp)</label>
                            <input type="number" id="checkout-diskon" class="form-control form-control-lg bg-white border-0 shadow-sm" value="0" min="0" oninput="calculateCheckout()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Metode Pembayaran</label>
                            <select id="checkout-metode" class="form-select form-select-lg bg-white border-0 shadow-sm" onchange="calculateCheckout()">
                                <option value="Cash">Tunai (Cash)</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Debit">Kartu Debit</option>
                            </select>
                        </div>
                        <hr class="border-success opacity-25">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-5">TOTAL</span>
                            <span class="fw-bold text-success fs-3" id="checkout-total">Rp 0</span>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Uang Diterima (Rp)</label>
                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-white border-0">Rp</span>
                                <input type="number" id="checkout-uang" class="form-control bg-white border-0 fw-bold fs-5" placeholder="0" oninput="calculateCheckout()">
                            </div>
                        </div>
                        <div class="bg-white p-3 rounded-4 text-center mb-4 shadow-sm">
                            <p class="text-muted mb-1 fw-semibold small">Uang Kembalian</p>
                            <h3 class="fw-bold text-primary mb-0" id="checkout-kembalian">Rp 0</h3>
                        </div>
                        
                        
                        <button class="btn bg-brand-green text-white w-100 py-3 fw-bold rounded-4 fs-5 shadow mb-3" onclick="processCheckout()">
                            BAYAR SEKARANG <i class="bi bi-check-circle-fill ms-2"></i>
                        </button>
                        <button class="btn btn-light text-secondary w-100 py-2 fw-bold rounded-4 border shadow-sm" onclick="backToCart()">
                            <i class="bi bi-plus-circle me-2"></i> Tambah Menu Lain
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal Pembayaran Dihapus, diganti dengan Checkout Screen -->
<!-- Modal Pilih Meja -->
<div class="modal fade" id="tableModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-primary bg-opacity-10 rounded-top-4 p-4">
                <h4 class="modal-title fw-bold text-primary" id="tableModalTitle"><i class="bi bi-grid-3x3-gap-fill me-2"></i> Pilih Meja</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-3" id="tables-grid">
                    <!-- Loaded via JS -->
                    <div class="col-12 text-center text-muted py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">Memuat daftar meja...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pesanan QR Masuk -->
<div class="modal fade" id="qrOrdersModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-info bg-opacity-10 rounded-top-4 p-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-qr-code-scan fs-5"></i>
                    </div>
                    <div>
                        <h4 class="modal-title fw-bold text-dark m-0">Pesanan Masuk QR Meja</h4>
                        <small class="text-muted">Pantau, proses ke dapur, dan selesaikan pesanan pelanggan self-order</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="loadQrOrders()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Segarkan
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Filter Pills -->
                <div class="d-flex gap-2 mb-3">
                    <button class="btn btn-sm btn-dark rounded-pill px-3 qr-filter active" onclick="filterQrOrdersList('all', this)">Semua (<span id="qrCountAll">0</span>)</button>
                    <button class="btn btn-sm btn-outline-warning rounded-pill px-3 qr-filter" onclick="filterQrOrdersList('confirmed', this)">Perlu Dimasak (<span id="qrCountNew">0</span>)</button>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 qr-filter" onclick="filterQrOrdersList('preparing', this)">Sedang Dimasak (<span id="qrCountCook">0</span>)</button>
                    <button class="btn btn-sm btn-outline-success rounded-pill px-3 qr-filter" onclick="filterQrOrdersList('ready', this)">Siap Saji (<span id="qrCountReady">0</span>)</button>
                </div>

                <!-- Grid of Orders -->
                <div class="row g-3" id="qrOrdersGrid" style="max-height: 60vh; overflow-y: auto;">
                    <!-- Rendered via JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pelacakan Pendapatan Masuk Hari Ini -->
<div class="modal fade" id="todayIncomeModal" tabindex="-1" aria-labelledby="todayIncomeModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header border-0 bg-success bg-opacity-10 p-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                        <i class="bi bi-cash-coin fs-4"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="modal-title fw-bold text-dark m-0" id="todayIncomeModalTitle">Pelacakan Pendapatan Masuk Hari Ini</h4>
                            <span class="badge bg-success rounded-pill px-3 py-1" id="incomeModalDate">Hari Ini</span>
                        </div>
                        <small class="text-muted" style="font-family: 'Playfair Display', Georgia, serif; font-style: italic; color: #b45309 !important; font-weight: 700;">
                            Kulu Asri - Jagonya Ikan Bakar! &bull; Rincian transaksi masuk dari POS Kasir & QR Meja
                        </small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm" onclick="fetchTodayIncome(false)" title="Segarkan Data Realtime">
                        <i class="bi bi-arrow-clockwise me-1" id="incomeRefreshIcon"></i> Segarkan
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-4 bg-light">
                <!-- Summary KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-success">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold">TOTAL OMSET HARI INI</span>
                                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1"><i class="bi bi-graph-up-arrow me-1"></i><span id="cardTotalCount">0</span> TRX</span>
                            </div>
                            <h3 class="fw-bolder text-success m-0" id="cardTotalRevenue" style="letter-spacing: -0.5px;">Rp 0</h3>
                            <small class="text-muted mt-1 d-block" style="font-size: 11px;">Semua transaksi berstatus lunas</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold">DARI POS KASIR</span>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1"><span id="cardPosCount">0</span> TRX</span>
                            </div>
                            <h4 class="fw-bold text-primary m-0" id="cardPosRevenue">Rp 0</h4>
                            <small class="text-muted mt-1 d-block" style="font-size: 11px;">Pesanan langsung via kasir</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-warning">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold">DARI QR SELF-ORDER</span>
                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-1"><span id="cardQrCount">0</span> TRX</span>
                            </div>
                            <h4 class="fw-bold text-warning-emphasis m-0" id="cardQrRevenue">Rp 0</h4>
                            <small class="text-muted mt-1 d-block" style="font-size: 11px;">Pesanan scan QR di meja pelanggan</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white border-start border-4 border-info">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold">METODE PEMBAYARAN</span>
                                <i class="bi bi-wallet2 text-info"></i>
                            </div>
                            <div class="d-flex flex-column gap-1 small mt-1">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"><i class="bi bi-cash me-1 text-success"></i> Tunai (Cash):</span>
                                    <span class="fw-bold text-dark" id="cardCashTotal">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"><i class="bi bi-qr-code me-1 text-primary"></i> QRIS/Gateway:</span>
                                    <span class="fw-bold text-dark" id="cardQrisTotal">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"><i class="bi bi-credit-card me-1 text-secondary"></i> Debit:</span>
                                    <span class="fw-bold text-dark" id="cardDebitTotal">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Controls & Search -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3 bg-white p-3 rounded-4 shadow-sm">
                    <!-- Filter Pills -->
                    <div class="d-flex flex-wrap gap-2" id="incomeFilterPills">
                        <button class="btn btn-sm btn-dark rounded-pill px-3 income-filter active" onclick="filterIncomeList('all', this)">
                            Semua Transaksi (<span id="filterCountAll">0</span>)
                        </button>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 income-filter" onclick="filterIncomeList('pos', this)">
                            <i class="bi bi-shop me-1"></i> POS Kasir (<span id="filterCountPos">0</span>)
                        </button>
                        <button class="btn btn-sm btn-outline-warning rounded-pill px-3 income-filter" onclick="filterIncomeList('qr', this)">
                            <i class="bi bi-qr-code-scan me-1"></i> QR Meja (<span id="filterCountQr">0</span>)
                        </button>
                        <button class="btn btn-sm btn-outline-success rounded-pill px-3 income-filter" onclick="filterIncomeList('Cash', this)">
                            <i class="bi bi-cash me-1"></i> Tunai (<span id="filterCountCash">0</span>)
                        </button>
                        <button class="btn btn-sm btn-outline-info rounded-pill px-3 income-filter" onclick="filterIncomeList('QRIS', this)">
                            <i class="bi bi-qr-code me-1"></i> QRIS (<span id="filterCountQris">0</span>)
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="position-relative" style="min-width: 260px;">
                        <i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 9px;"></i>
                        <input type="text" class="form-control form-control-sm rounded-pill ps-5 border-secondary-subtle" id="incomeSearchInput" placeholder="Cari No. TRX, nama, meja..." onkeyup="searchIncomeList(this.value)">
                    </div>
                </div>

                <!-- Transaction List Table / Grid -->
                <div class="bg-white rounded-4 shadow-sm overflow-hidden" style="max-height: 50vh; overflow-y: auto;">
                    <div class="table-responsive m-0">
                        <table class="table table-hover align-middle mb-0" id="incomeTransactionsTable">
                            <thead class="table-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4">Jam</th>
                                    <th>No. Transaksi</th>
                                    <th>Meja / Pelanggan</th>
                                    <th>Kanal Order</th>
                                    <th>Metode Bayar</th>
                                    <th>Detail Menu Dipesan</th>
                                    <th class="text-end">Nominal Masuk</th>
                                    <th class="text-center pe-4">Struk</th>
                                </tr>
                            </thead>
                            <tbody id="incomeTransactionsTbody">
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                                        Memuat data pendapatan masuk hari ini...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 p-3 px-4 bg-white d-flex justify-content-between">
                <a href="{{ route('pos.history') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-clock-history me-1"></i> Buka Riwayat Transaksi Lengkap (Semua Tanggal)
                </a>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tutup Toko (EOD) -->
<div class="modal fade" id="eodModal" tabindex="-1">
    <!-- EOD Modal Content (Same as before) -->
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-danger bg-opacity-10 rounded-top-4 p-4">
                <h4 class="modal-title fw-bold text-danger"><i class="bi bi-door-closed-fill me-2"></i> Konfirmasi Tutup Toko</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-muted mb-4">Berikut adalah ringkasan pendapatan shift Anda hari ini.</p>
                <div class="bg-light rounded-4 p-3 mb-3 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-bold">Total Transaksi (Sukses)</span>
                        <span class="fw-bold" id="eod_trx_count">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-bold">Uang Fisik (Cash in Drawer)</span>
                        <span class="fw-bold text-success fs-5" id="eod_cash">Rp 0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fw-bold">TOTAL OMSET BERSIH</span>
                        <span class="fw-bold text-primary fs-4" id="eod_total">Rp 0</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger rounded-pill px-5 fw-bold" onclick="printEodAndLogout()">Cetak & Log Out</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let savedCart = localStorage.getItem('pos_cart');
        if(savedCart) {
            try {
                cart = JSON.parse(savedCart);
                let trx = localStorage.getItem('pos_transaction_id');
                if(trx !== 'null') currentTransactionId = trx;
                
                if(cart.length > 0) {
                    hasUnsavedChanges = true;
                    document.getElementById('welcome-screen').style.display = 'none';
                    document.getElementById('pos-body').style.display = 'flex';
                    document.getElementById('btn-home').classList.remove('d-none');
                    
                    setTimeout(() => {
                        document.getElementById('customer_name').value = localStorage.getItem('pos_customer') || '';
                        document.getElementById('table_number').value = localStorage.getItem('pos_table') || '';
                        checkValidation();
                    }, 100);
                    
                    renderCart();
                }
            } catch(e) {}
        }
    });

    function printInIframe(url) {
        let iframe = document.getElementById('printIframe');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'printIframe';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }
        iframe.src = url;
    }

    function saveCartToLocal() {
        localStorage.setItem('pos_cart', JSON.stringify(cart));
        localStorage.setItem('pos_customer', document.getElementById('customer_name').value);
        localStorage.setItem('pos_table', document.getElementById('table_number').value);
        localStorage.setItem('pos_transaction_id', currentTransactionId);
    }

    function checkValidation() {
        const btn = document.getElementById('btn-action');
        if(!btn) return;
        let customer = document.getElementById('customer_name').value;
        let table = document.getElementById('table_number').value;
        if(!customer || !table) {
            btn.disabled = true;
            btn.classList.add('opacity-50');
        } else {
            btn.disabled = false;
            btn.classList.remove('opacity-50');
        }
        saveCartToLocal();
    }

    async function freeTableManual(id, name) {
        if(confirm(`Yakin ingin mengosongkan meja ${name}?`)) {
            try {
                let res = await fetch(`/pos/tables/${id}/free`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                let data = await res.json();
                if(data.success) showTableModal(currentMode);
            } catch(e) {
                alert('Gagal mengosongkan meja');
            }
        }
    }

    // State
    let cart = [];
    let currentTotal = 0;
    let currentTransactionId = null;
    let hasUnsavedChanges = false;
    let currentMode = ''; // 'order' or 'payment'
    
    const tableModal = new bootstrap.Modal(document.getElementById('tableModal'));

    // Format Rupiah
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    // Keamanan (anti-XSS): data dari server/pelanggan (nama, catatan, dll) WAJIB di-escape
    // sebelum dimasukkan ke innerHTML / template literal.
    function esc(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Untuk argumen string di dalam atribut onclick="fn(...)": hasilnya sudah termasuk tanda kutip.
    function jsArg(value) {
        return esc(JSON.stringify(String(value ?? '')));
    }

    // --- NEW NAVIGATION LOGIC ---
    function openMode(mode) {
        currentMode = mode;
        showTableModal(mode);
    }

    function goHome() {
        if(hasUnsavedChanges) {
            Swal.fire({
                title: 'Ada pesanan belum disimpan!',
                text: "Yakin ingin kembali ke Beranda?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Kembali',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeGoHome();
                }
            });
            return;
        }
        executeGoHome();
    }

    function executeGoHome() {
        document.getElementById('pos-body').style.display = 'none';
        document.getElementById('checkout-screen').style.display = 'none';
        document.getElementById('welcome-screen').style.display = 'flex';
        document.getElementById('btn-home').classList.add('d-none');
        
        cart = [];
        currentTransactionId = null;
        hasUnsavedChanges = false;
        document.getElementById('customer_name').value = '';
        document.getElementById('table_number').value = '';
    }

    // Table Management Logic
    async function showTableModal(mode) {
        try {
            let res = await fetch('{{ route("pos.tables") }}');
            let tables = await res.json();
            
            let html = '';
            
            if(mode === 'order') {
                document.getElementById('tableModalTitle').innerHTML = '<i class="bi bi-journal-plus me-2"></i> Pilih Meja (Pesanan Baru)';
                
                // Add Takeaway option first
                html += `
                <div class="col-md-3 col-4">
                    <button class="btn btn-primary w-100 py-3 fw-bold rounded-4 shadow-sm" onclick="selectTable('Takeaway', false, 'order')">
                        <i class="bi bi-bag-check-fill d-block fs-2 mb-2"></i>
                        Takeaway<br><small>Bawa Pulang</small>
                    </button>
                </div>`;

                tables.forEach(t => {
                    if(t.status === 'available') {
                        html += `
                        <div class="col-md-3 col-4">
                            <button class="btn btn-outline-success w-100 py-3 fw-bold rounded-4 shadow-sm" onclick="selectTable(${jsArg(t.name)}, false, 'order')">
                                <i class="bi bi-check-circle d-block fs-2 mb-2"></i>
                                ${esc(t.name)}<br><small>Tersedia</small>
                            </button>
                        </div>`;
                    } else {
                        html += `
                        <div class="col-md-3 col-4 position-relative">
                            <button class="btn btn-warning w-100 py-3 fw-bold rounded-4 shadow-sm text-dark" onclick="selectTable(${jsArg(t.name)}, true, 'order')">
                                <i class="bi bi-person-fill d-block fs-2 mb-2"></i>
                                ${esc(t.name)}<br><small>Terisi (Tambah)</small>
                            </button>
                            <button class="btn btn-danger btn-sm position-absolute rounded-circle shadow" style="top: 5px; right: 20px;" onclick="event.stopPropagation(); freeTableManual(${Number(t.id)}, ${jsArg(t.name)})" title="Kosongkan Meja">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>`;
                    }
                });
            } else if (mode === 'payment') {
                document.getElementById('tableModalTitle').innerHTML = '<i class="bi bi-wallet2 me-2"></i> Pilih Antrean (Pembayaran)';
                
                try {
                    let activeRes = await fetch('{{ route("pos.active_transactions") }}');
                    let activeTransactions = await activeRes.json();
                    
                    if (activeTransactions.length === 0) {
                        html = '<div class="col-12 text-center py-5"><i class="bi bi-check2-circle text-success fs-1"></i><h5 class="mt-3">Tidak ada antrean pesanan yang belum dibayar.</h5></div>';
                    } else {
                        activeTransactions.forEach(t => {
                            let displayName = t.table_number === 'Takeaway' ? `Takeaway<br><small>${esc(t.customer_name)}</small>` : `Meja ${esc(t.table_number)}`;
                            let isQr = t.order_source === 'qr';
                            let isPaid = t.payment_status === 'paid' || t.status === 'paid';
                            let btnClass = isPaid ? 'btn-success' : 'btn-danger';
                            let badgeText = isQr ? (isPaid ? 'QR LUNAS' : 'QR PENDING') : 'POS KASIR';
                            let badgeClass = isPaid ? 'bg-light text-success' : 'bg-dark text-white';
                            let icon = isPaid ? 'bi-check2-circle' : 'bi-currency-dollar';

                            html += `
                            <div class="col-md-3 col-4">
                                <button class="btn ${btnClass} w-100 py-3 fw-bold rounded-4 shadow-sm text-white position-relative" onclick="selectTransaction(${Number(t.id)}, ${jsArg(t.table_number)})">
                                    <span class="badge ${badgeClass} position-absolute top-0 end-0 m-2 fw-bold" style="font-size: 10px;">${badgeText}</span>
                                    <i class="bi ${icon} d-block fs-2 mb-2"></i>
                                    ${displayName}
                                </button>
                            </div>`;
                        });
                    }
                } catch(e) {
                    Swal.fire('Error', 'Gagal mengambil data pesanan aktif.', 'error');
                }
            }
            
            document.getElementById('tables-grid').innerHTML = html;
            tableModal.show();
        } catch (e) {
            Swal.fire('Error', 'Gagal memuat data.', 'error');
        }
    }

    async function selectTransaction(trxId, tableName) {
        document.getElementById('table_number').value = tableName;
        tableModal.hide();

        try {
            let res = await fetch(`/pos/transaction/${trxId}`);
            let data = await res.json();
            if(data.success) {
                currentTransactionId = data.transaction.id;
                document.getElementById('customer_name').value = data.transaction.customer_name;
                
                cart = [];
                data.transaction.details.forEach(d => {
                    cart.push({
                        id: d.product_id,
                        name: d.product.name,
                        price: parseFloat(d.price),
                        qty: d.qty,
                        notes: d.notes || '',
                        is_saved: true
                    });
                });
                hasUnsavedChanges = false;
                
                document.getElementById('welcome-screen').style.display = 'none';
                document.getElementById('pos-body').style.display = 'none';
                document.getElementById('checkout-screen').style.display = 'block';
                document.getElementById('btn-home').classList.remove('d-none');
                
                renderCheckoutItems();
                
                document.getElementById('checkout-diskon').value = 0;
                
                if (data.transaction.status === 'paid' || data.transaction.payment_status === 'paid') {
                    document.getElementById('checkout-metode').value = data.transaction.payment_method || 'QRIS';
                    document.getElementById('checkout-uang').value = data.transaction.total;
                    document.getElementById('checkout-uang').disabled = true;
                    document.getElementById('checkout-kembalian').innerText = 'LUNAS (ONLINE)';
                    document.getElementById('checkout-kembalian').classList.remove('text-danger');
                    document.getElementById('checkout-kembalian').classList.add('text-success');
                } else {
                    document.getElementById('checkout-metode').value = 'Cash';
                    document.getElementById('checkout-uang').value = '';
                    document.getElementById('checkout-uang').disabled = false;
                    document.getElementById('checkout-kembalian').innerText = 'Rp 0';
                    document.getElementById('checkout-kembalian').classList.remove('text-danger');
                    document.getElementById('checkout-kembalian').classList.add('text-primary');
                    setTimeout(() => document.getElementById('checkout-uang').focus(), 500);
                }
            }
        } catch(e) {
            Swal.fire('Error', 'Gagal mengambil detail pesanan.', 'error');
        }
    }

    async function selectTable(name, isOccupied, mode) {
        document.getElementById('table_number').value = name;
        tableModal.hide();

        if (isOccupied && name !== 'Takeaway') {
            try {
                let res = await fetch(`/pos/active-order/${encodeURIComponent(name)}`);
                let data = await res.json();
                if(data.success) {
                    currentTransactionId = data.transaction.id;
                    document.getElementById('customer_name').value = data.transaction.customer_name;
                    
                    cart = [];
                    data.transaction.details.forEach(d => {
                        cart.push({
                            id: d.product_id,
                            name: d.product.name,
                            price: parseFloat(d.price),
                            qty: d.qty,
                            notes: d.notes || '',
                            is_saved: true
                        });
                    });
                    hasUnsavedChanges = false;
                }
            } catch(e) {
                Swal.fire('Error', 'Gagal mengambil pesanan meja.', 'error');
                return;
            }
        } else {
            cart = [];
            currentTransactionId = null;
            document.getElementById('customer_name').value = '';
            hasUnsavedChanges = false;
        }

        // Show POS Body
        document.getElementById('welcome-screen').style.display = 'none';
        document.getElementById('pos-body').style.display = 'flex';
        document.getElementById('btn-home').classList.remove('d-none');
        
        renderCart();

        if (!isOccupied) {
            setTimeout(() => document.getElementById('customer_name').focus(), 300);
        }
    }

    // --- POS LOGIC (Cart, Filter, Search) ---

    function filterProducts(categoryId, btnElement) {
        document.querySelectorAll('.category-pills .btn').forEach(btn => btn.classList.remove('active'));
        btnElement.classList.add('active');

        const items = document.querySelectorAll('.product-item');
        items.forEach(item => {
            if(categoryId === 'all' || item.getAttribute('data-category') == categoryId) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
        document.getElementById('search-input').value = '';
    }

    function searchProducts(keyword) {
        const query = keyword.toLowerCase();
        const items = document.querySelectorAll('.product-item');
        document.querySelectorAll('.category-pills .btn').forEach(btn => btn.classList.remove('active'));
        
        items.forEach(item => {
            if(item.getAttribute('data-name').includes(query)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function addToCart(id, name, price) {
        new Audio('data:audio/mp3;base64,//OExAAAAANIAAAAAExBTUUzLjEwMKqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq/').play().catch(e => {});

        let existing = cart.find(item => item.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id, name, price, qty: 1, notes: '', is_saved: false });
        }
        hasUnsavedChanges = true;
        if (document.getElementById('checkout-screen').style.display === 'block') {
            renderCheckoutItems();
        } else {
            renderCart();
        }
    }

    function updateQty(id, delta) {
        let item = cart.find(item => item.id === id);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) {
                cart = cart.filter(i => i !== item);
            }
            hasUnsavedChanges = true;
            if (document.getElementById('checkout-screen').style.display === 'block') {
                renderCheckoutItems();
            } else {
                renderCart();
            }
        }
    }

    function setQty(id, value) {
        let qty = parseInt(value);
        let item = cart.find(item => item.id === id);
        if (item) {
            if(isNaN(qty) || qty <= 0) {
                cart = cart.filter(i => i !== item);
            } else {
                item.qty = qty;
            }
            hasUnsavedChanges = true;
            if (document.getElementById('checkout-screen').style.display === 'block') {
                renderCheckoutItems();
            } else {
                renderCart();
            }
        }
    }

    function setNotes(id, value) {
        let item = cart.find(item => item.id === id);
        if (item) {
            item.notes = value;
            hasUnsavedChanges = true;
            
            if (document.getElementById('checkout-screen').style.display === 'block') {
                // If notes edited from checkout screen, maybe just update quietly
            } else {
                const btn = document.getElementById('btn-action');
                if (btn && currentMode !== 'payment') {
                    btn.innerHTML = 'SIMPAN PERUBAHAN <i class="bi bi-save-fill ms-2"></i>';
                }
            }
        }
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        let html = '';
        currentTotal = 0;
        let hasNewItems = false;
        let hasSavedItems = false;

        if (cart.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted mt-5 pt-5">
                    <i class="bi bi-cart-x fs-1 mb-3 d-block opacity-25"></i>
                    <h5 class="fw-bold opacity-50">Keranjang Kosong</h5>
                    <p class="small opacity-50">Silakan pilih menu di sebelah kiri.</p>
                </div>
            `;
            const btn = document.getElementById('btn-action');
            btn.innerHTML = 'KIRIM PESANAN <i class="bi bi-send-fill ms-2"></i>';
            btn.onclick = processOrder;
            btn.className = 'btn bg-brand-orange w-100 btn-pay text-white';
        } else {
            cart.forEach(item => {
                let sub = item.price * item.qty;
                currentTotal += sub;
                
                if (item.is_saved) hasSavedItems = true;
                else hasNewItems = true;
                
                let qtyControls = `<div class="qty-controls mx-2 shadow-sm border">
                        <button type="button" class="qty-btn" onclick="updateQty(${item.id}, -1)"><i class="bi bi-dash"></i></button>
                        <input type="number" class="qty-input" value="${item.qty}" min="1" onchange="setQty(${item.id}, this.value)">
                        <button type="button" class="qty-btn" onclick="updateQty(${item.id}, 1)"><i class="bi bi-plus"></i></button>
                       </div>`;

                let notesControl = `<div class="mt-1"><input type="text" class="form-control form-control-sm" placeholder="Catatan (opsional)" value="${esc(item.notes || '')}" onchange="setNotes(${item.id}, this.value)"></div>`;

                html += `
                    <div class="cart-item ${item.is_saved ? 'bg-light rounded-3 p-2 mb-2 border-0' : ''}" style="flex-direction: column; align-items: stretch;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div style="flex: 1;">
                                <h6 class="mb-1 fw-bold ${item.is_saved ? 'text-muted' : 'text-dark'}">${esc(item.name)}</h6>
                                <div class="text-brand-orange fw-semibold small">${formatRupiah(item.price)}</div>
                            </div>
                            ${qtyControls}
                            <div class="fw-bold text-end ${item.is_saved ? 'text-muted' : 'text-dark'}" style="width: 90px; font-size: 1.1rem;">
                                ${formatRupiah(sub).replace('Rp', '')}
                            </div>
                        </div>
                        ${notesControl}
                    </div>
                `;
            });
            container.innerHTML = html;

            const btn = document.getElementById('btn-action');
            // Allow payment as long as everything is saved and cart is not empty
            if (!hasUnsavedChanges && !hasNewItems) {
                btn.innerHTML = 'BAYAR SEKARANG <i class="bi bi-wallet-fill ms-2"></i>';
                btn.onclick = openCheckoutScreen;
                btn.className = 'btn bg-brand-green w-100 btn-pay text-white';
            } else {
                btn.innerHTML = (hasSavedItems ? 'SIMPAN PERUBAHAN <i class="bi bi-save-fill ms-2"></i>' : 'KIRIM PESANAN <i class="bi bi-send-fill ms-2"></i>');
                btn.onclick = processOrder;
                btn.className = 'btn bg-brand-orange w-100 btn-pay text-white';
            }
        }

        document.getElementById('subtotal').innerText = formatRupiah(currentTotal);
        document.getElementById('grand-total').innerText = formatRupiah(currentTotal);

        checkValidation();
        saveCartToLocal();
    }

    async function processOrder() {
        if(cart.length === 0) {
            if (currentTransactionId) {
                Swal.fire('Informasi', 'Keranjang kosong. Jika Anda ingin membatalkan pesanan, silakan gunakan fitur "Kosongkan Meja" pada pilihan Antrean.', 'info');
            }
            return;
        }
        let customer = document.getElementById('customer_name').value;
        let table = document.getElementById('table_number').value;
        
        if(!customer || !table) {
            Swal.fire('Perhatian', 'Nama pelanggan dan Nomor Meja wajib diisi!', 'warning');
            document.getElementById('customer_name').focus();
            return;
        }

        try {
            let response = await fetch('{{ route("pos.save_order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    customer_name: customer,
                    table_number: table,
                    cart: cart,
                    transaction_id: currentTransactionId
                })
            });
            let data = await response.json();
            
            if(data.success) {
                new Audio('data:audio/mp3;base64,//OExAAAAANIAAAAAExBTUUzLjEwMKqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq/').play().catch(e => {});
                
                cart.forEach(item => item.is_saved = true);
                currentTransactionId = data.transaction_id;
                hasUnsavedChanges = false;
                renderCart();
                
                Swal.fire({
                    title: 'Tersimpan!',
                    text: 'Pesanan berhasil disimpan. Ingin cetak tiket dapur?',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#2e7d32',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-printer"></i> Ya, Cetak',
                    cancelButtonText: 'Tidak Perlu'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let printUrl = '/pos/kitchen-ticket/' + data.transaction_id;
                        if (data.print_items && data.print_items.length > 0) {
                            printUrl += '?items=' + encodeURIComponent(JSON.stringify(data.print_items));
                        }
                        if (data.is_additional) {
                            printUrl += (printUrl.includes('?') ? '&' : '?') + 'additional=1';
                        }
                        printInIframe(printUrl);
                    }
                });
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
        }
    }

    function backToCart() {
        document.getElementById('checkout-screen').style.display = 'none';
        document.getElementById('pos-body').style.display = 'flex';
        renderCart();
    }

    function renderCheckoutItems() {
        let customer = document.getElementById('customer_name').value;
        let table = document.getElementById('table_number').value;
        document.getElementById('checkout-customer-info').innerText = `Pelanggan: ${customer} | Meja: ${table}`;
        
        let orderItemsHtml = '';
        currentTotal = 0;
        cart.forEach(item => {
            let sub = item.price * item.qty;
            currentTotal += sub;
            orderItemsHtml += `
                <tr>
                    <td>
                        <div class="fw-bold text-dark">${esc(item.name)}</div>
                        ${item.notes ? `<div class="text-muted small">Catatan: ${esc(item.notes)}</div>` : ''}
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center justify-content-center">
                            <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="updateQty(${item.id}, -1)">-</button>
                            <span class="mx-2 fw-bold">${item.qty}</span>
                            <button class="btn btn-sm btn-outline-secondary px-2 py-0" onclick="updateQty(${item.id}, 1)">+</button>
                        </div>
                    </td>
                    <td class="text-end align-middle">${formatRupiah(item.price)}</td>
                    <td class="text-end align-middle fw-bold text-dark">
                        ${formatRupiah(sub)}
                        <button class="btn btn-sm btn-link text-danger p-0 ms-2" title="Hapus" onclick="updateQty(${item.id}, -${item.qty})"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        });

        if (cart.length === 0) {
            orderItemsHtml = `<tr><td colspan="4" class="text-center py-4 text-muted">Keranjang kosong. Tambah pesanan terlebih dahulu.</td></tr>`;
        }

        document.getElementById('checkout-order-items').innerHTML = orderItemsHtml;
        document.getElementById('checkout-subtotal').innerText = formatRupiah(currentTotal);
        calculateCheckout();
    }

    function openCheckoutScreen() {
        if(cart.length === 0) {
            Swal.fire('Kosong', 'Keranjang belanja kosong!', 'warning'); return;
        }

        document.getElementById('checkout-diskon').value = 0;
        document.getElementById('checkout-metode').value = 'Cash';
        document.getElementById('checkout-uang').value = '';
        document.getElementById('checkout-uang').disabled = false;
        
        document.getElementById('checkout-kembalian').innerText = 'Rp 0';
        document.getElementById('checkout-kembalian').classList.remove('text-danger');
        document.getElementById('checkout-kembalian').classList.add('text-primary');

        renderCheckoutItems();

        document.getElementById('pos-body').style.display = 'none';
        document.getElementById('checkout-screen').style.display = 'block';

        setTimeout(() => document.getElementById('checkout-uang').focus(), 500);
    }

    function calculateCheckout() {
        let diskon = parseInt(document.getElementById('checkout-diskon').value) || 0;
        let totalTagihan = Math.max(0, currentTotal - diskon);
        document.getElementById('checkout-total').innerText = formatRupiah(totalTagihan);

        let metode = document.getElementById('checkout-metode').value;
        let inputUang = document.getElementById('checkout-uang');

        if(metode !== 'Cash') {
            inputUang.value = totalTagihan;
            inputUang.disabled = true;
        } else {
            inputUang.disabled = false;
        }

        let uangBayar = parseInt(inputUang.value) || 0;
        let sisa = uangBayar - totalTagihan;
        let kembalianEl = document.getElementById('checkout-kembalian');

        if(sisa < 0) {
            kembalianEl.innerText = "- " + formatRupiah(Math.abs(sisa));
            kembalianEl.classList.add('text-danger');
            kembalianEl.classList.remove('text-primary');
        } else {
            kembalianEl.innerText = formatRupiah(sisa);
            kembalianEl.classList.remove('text-danger');
            kembalianEl.classList.add('text-primary');
        }
    }

    async function processCheckout() {
        if(cart.length === 0) {
            Swal.fire('Perhatian', 'Keranjang kosong, tambahkan pesanan terlebih dahulu.', 'warning');
            return;
        }

        let diskon = parseInt(document.getElementById('checkout-diskon').value) || 0;
        let totalTagihan = Math.max(0, currentTotal - diskon);
        let uangBayar = parseInt(document.getElementById('checkout-uang').value) || 0;
        let metode = document.getElementById('checkout-metode').value;
        
        if(uangBayar < totalTagihan) {
            Swal.fire('Kurang', 'Uang pembayaran kurang!', 'error');
            return;
        }

        // SAVE ORDER FIRST IF HAS UNSAVED CHANGES
        if (hasUnsavedChanges) {
            let customer = document.getElementById('customer_name').value;
            let table = document.getElementById('table_number').value;
            
            try {
                let saveRes = await fetch('{{ route("pos.save_order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        customer_name: customer,
                        table_number: table,
                        cart: cart,
                        transaction_id: currentTransactionId
                    })
                });
                let saveData = await saveRes.json();
                
                if(saveData.success) {
                    currentTransactionId = saveData.transaction_id;
                    cart.forEach(item => item.is_saved = true);
                    hasUnsavedChanges = false;
                } else {
                    Swal.fire('Gagal', 'Gagal menyimpan perubahan pesanan: ' + saveData.message, 'error');
                    return;
                }
            } catch (e) {
                Swal.fire('Error', 'Terjadi kesalahan saat menyimpan perubahan.', 'error');
                return;
            }
        }

        // PROCESS PAYMENT
        try {
            let response = await fetch('/pos/pay-order/' + currentTransactionId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    payment: uangBayar,
                    payment_method: metode,
                    discount: diskon
                })
            });
            let data = await response.json();
            
            if(data.success) {
                new Audio('data:audio/mp3;base64,//OExAAAAANIAAAAAExBTUUzLjEwMKqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq/').play().catch(e => {});
                
                printInIframe('/pos/receipt/' + data.transaction_id);
                
                fetchTodayIncome(false);
                goHome(); // Back to main screen
            } else {
                Swal.fire('Gagal', data.message, 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
        }
    }

    async function showEodModal() {
        try {
            let res = await fetch('{{ route("pos.eod_summary") }}');
            let data = await res.json();
            
            document.getElementById('eod_trx_count').innerText = data.transaction_count;
            document.getElementById('eod_cash').innerText = formatRupiah(data.cash);
            document.getElementById('eod_total').innerText = formatRupiah(data.total_sales);
            
            new bootstrap.Modal(document.getElementById('eodModal')).show();
        } catch(e) {
            Swal.fire('Error', 'Gagal mengambil data rekap.', 'error');
        }
    }

    function printEodAndLogout() {
        window.open('{{ route("pos.print_eod") }}', '_blank', 'width=400,height=600');
        setTimeout(() => {
            document.getElementById('logout-form').submit();
        }, 1500);
    }

    // Hotkeys
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F2') {
            if (document.getElementById('pos-body').style.display === 'flex') {
                e.preventDefault();
                document.getElementById('search-input').focus();
            }
        }
        if (e.key === 'F4') {
            if (document.getElementById('pos-body').style.display === 'flex') {
                e.preventDefault();
                const btn = document.getElementById('btn-action');
                if (btn) btn.click();
            }
        }
    });

    // --- QR ORDER MANAGEMENT & LIVE SYNC ---
    const qrOrdersModal = new bootstrap.Modal(document.getElementById('qrOrdersModal'));
    let currentQrOrdersList = [];
    let lastQrOrderCount = 0;

    function showQrOrdersModal() {
        qrOrdersModal.show();
        loadQrOrders();
    }

    async function loadQrOrders() {
        try {
            const res = await fetch('{{ route("pos.incoming_qr") }}');
            const data = await res.json();
            if (data.success) {
                currentQrOrdersList = data.orders;
                renderQrOrdersGrid('all');
                updateQrBadges(data.count);
            }
        } catch (e) {
            console.error("Gagal memuat pesanan QR:", e);
        }
    }

    function updateQrBadges(count) {
        const badgeHeader = document.getElementById('qrOrdersBadge');
        const badgeBig = document.getElementById('qrBadgeBig');

        if (count > 0) {
            if (badgeHeader) {
                badgeHeader.innerText = count;
                badgeHeader.classList.remove('d-none');
            }
            if (badgeBig) {
                badgeBig.innerText = count + ' Baru';
                badgeBig.classList.remove('d-none');
            }
        } else {
            if (badgeHeader) badgeHeader.classList.add('d-none');
            if (badgeBig) badgeBig.classList.add('d-none');
        }

        const countNew = currentQrOrdersList.filter(o => o.order_status === 'confirmed').length;
        const countCook = currentQrOrdersList.filter(o => o.order_status === 'preparing').length;
        const countReady = currentQrOrdersList.filter(o => o.order_status === 'ready').length;

        const elAll = document.getElementById('qrCountAll');
        const elNew = document.getElementById('qrCountNew');
        const elCook = document.getElementById('qrCountCook');
        const elReady = document.getElementById('qrCountReady');

        if (elAll) elAll.innerText = currentQrOrdersList.length;
        if (elNew) elNew.innerText = countNew;
        if (elCook) elCook.innerText = countCook;
        if (elReady) elReady.innerText = countReady;
    }

    function filterQrOrdersList(status, btn) {
        document.querySelectorAll('.qr-filter').forEach(b => {
            b.classList.remove('active', 'btn-dark');
            b.classList.add('btn-outline-secondary');
        });
        btn.classList.add('active', 'btn-dark');
        btn.classList.remove('btn-outline-secondary');

        renderQrOrdersGrid(status);
    }

    function renderQrOrdersGrid(filterStatus) {
        const grid = document.getElementById('qrOrdersGrid');
        if (!grid) return;

        let filtered = currentQrOrdersList;
        if (filterStatus !== 'all') {
            filtered = currentQrOrdersList.filter(o => o.order_status === filterStatus);
        }

        if (filtered.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                    <h6>Tidak ada pesanan QR untuk kategori ini.</h6>
                </div>
            `;
            return;
        }

        let html = '';
        filtered.forEach(order => {
            let statusBadge = '';
            let actionButtons = '';

            if (order.order_status === 'confirmed') {
                statusBadge = '<span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i> Baru (Perlu Dimasak)</span>';
                actionButtons = `
                    <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" onclick="updateOrderStatusPos(${order.id}, 'preparing', true)">
                        <i class="bi bi-fire me-1"></i> Mulai Masak & Cetak Dapur
                    </button>
                `;
            } else if (order.order_status === 'preparing') {
                statusBadge = '<span class="badge bg-info text-white"><i class="bi bi-fire me-1"></i> Sedang Dimasak</span>';
                actionButtons = `
                    <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold" onclick="updateOrderStatusPos(${order.id}, 'ready')">
                        <i class="bi bi-bell-fill me-1"></i> Tandai Siap Saji
                    </button>
                `;
            } else if (order.order_status === 'ready') {
                statusBadge = '<span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i> Siap Disajikan</span>';
                actionButtons = `
                    <button class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold" onclick="updateOrderStatusPos(${order.id}, 'completed')">
                        <i class="bi bi-check-lg me-1"></i> Selesaikan Pesanan
                    </button>
                `;
            }

            let itemsHtml = '';
            order.details.forEach(d => {
                itemsHtml += `
                    <div class="d-flex justify-content-between py-1 border-bottom border-light small">
                        <div>
                            <strong>${Number(d.qty)}x</strong> ${esc(d.product ? d.product.name : 'Menu #' + d.product_id)}
                            ${d.notes ? `<div class="badge bg-warning bg-opacity-25 text-dark ms-1 small">Ket: ${esc(d.notes)}</div>` : ''}
                        </div>
                        <div class="text-muted">${formatRupiah(d.price * d.qty)}</div>
                    </div>
                `;
            });

            html += `
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-1">
                                    <i class="bi bi-geo-alt-fill"></i> Meja ${esc(order.table_number)}
                                </span>
                                <h6 class="fw-bold m-0 text-dark">#${esc(order.transaction_number)}</h6>
                                <small class="text-muted">Pemesan: ${esc(order.customer_name || 'Pelanggan')}</small>
                            </div>
                            <div class="text-end">
                                ${statusBadge}
                                <div class="badge bg-light text-success border mt-1 d-block">
                                    <i class="bi bi-shield-check"></i> LUNAS (${esc(order.payment_method)})
                                </div>
                            </div>
                        </div>

                        <div class="p-2 bg-light rounded-3 my-2" style="max-height: 140px; overflow-y: auto;">
                            ${itemsHtml}
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">Total Tagihan:</span>
                            <span class="fw-bold fs-6 text-primary">${formatRupiah(order.total)}</span>
                        </div>

                        <div class="d-flex gap-2 flex-wrap justify-content-between pt-2 border-top">
                            <div class="d-flex gap-1">
                                <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="printInIframe('/pos/kitchen-ticket/' + ${order.id})" title="Cetak Karcis Dapur">
                                    <i class="bi bi-printer me-1"></i> Dapur
                                </button>
                                <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="printInIframe('/pos/receipt/' + ${order.id})" title="Cetak Struk">
                                    <i class="bi bi-receipt"></i>
                                </button>
                            </div>
                            <div>
                                ${actionButtons}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;
    }

    async function updateOrderStatusPos(id, status, printKitchen = false) {
        try {
            const res = await fetch(`/pos/orders/${id}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order_status: status })
            });
            const data = await res.json();
            if (data.success) {
                if (printKitchen) {
                    printInIframe('/pos/kitchen-ticket/' + id);
                }
                loadQrOrders();
            }
        } catch (e) {
            Swal.fire('Error', 'Gagal memperbarui status order.', 'error');
        }
    }

    // Audio Chime Synthesizer
    function playAudioChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime);
            osc.frequency.setValueAtTime(880, ctx.currentTime + 0.15);
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
            osc.start();
            osc.stop(ctx.currentTime + 0.5);
        } catch(e) {}
    }

    // Polling Pesanan QR Otomatis Setiap 6 Detik
    setInterval(async () => {
        try {
            const res = await fetch('{{ route("pos.incoming_qr") }}');
            const data = await res.json();
            if (data.success) {
                currentQrOrdersList = data.orders;
                updateQrBadges(data.count);

                if (data.count > lastQrOrderCount) {
                    playAudioChime();
                    const toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    toast.fire({
                        icon: 'info',
                        title: `Pesanan QR Baru Masuk (${data.count} Total)`
                    });

                    if (document.getElementById('qrOrdersModal').classList.contains('show')) {
                        renderQrOrdersGrid('all');
                    }
                }
                lastQrOrderCount = data.count;
            }
            // Realtime update header today income badge
            fetchTodayIncome(false);
        } catch (e) {}
    }, 6000);

    setTimeout(loadQrOrders, 1000);
    setTimeout(() => fetchTodayIncome(false), 500);

    // --- PELACAKAN PENDAPATAN HARI INI ---
    let allTodayTransactions = [];
    let currentIncomeFilter = 'all';
    let currentIncomeSearch = '';

    async function fetchTodayIncome(openModalAfterFetch = false) {
        try {
            const refreshIcon = document.getElementById('incomeRefreshIcon');
            if (refreshIcon) refreshIcon.classList.add('bi-spin');

            const res = await fetch('{{ route("pos.today_income") }}');
            const data = await res.json();
            
            if (refreshIcon) refreshIcon.classList.remove('bi-spin');

            if (data.success) {
                // Update Welcome Screen Badge
                const welcomeBadge = document.getElementById('welcomeTodayIncomeBadge');
                if (welcomeBadge) {
                    welcomeBadge.innerText = data.summary.total_revenue_formatted;
                }
                const welcomeSub = document.getElementById('welcomeTodayIncomeSub');
                if (welcomeSub) {
                    welcomeSub.innerText = data.summary.count > 0 
                        ? `${data.summary.count} Transaksi Lunas` 
                        : 'Lacak Pemasukan Hari Ini';
                }

                // Update Header Badge (if present)
                const headerBadge = document.getElementById('headerTodayIncome');
                if (headerBadge) {
                    headerBadge.innerText = data.summary.total_revenue_formatted;
                }

                // Update Modal Cards
                const modalDate = document.getElementById('incomeModalDate');
                if (modalDate) modalDate.innerText = data.summary.date_formatted;
                
                const cardTotalRev = document.getElementById('cardTotalRevenue');
                if (cardTotalRev) cardTotalRev.innerText = data.summary.total_revenue_formatted;
                
                const cardTotalCnt = document.getElementById('cardTotalCount');
                if (cardTotalCnt) cardTotalCnt.innerText = data.summary.count;
                
                const cardPosRev = document.getElementById('cardPosRevenue');
                if (cardPosRev) cardPosRev.innerText = formatRupiah(data.summary.pos_revenue);
                
                const cardPosCnt = document.getElementById('cardPosCount');
                if (cardPosCnt) cardPosCnt.innerText = data.summary.pos_count;
                
                const cardQrRev = document.getElementById('cardQrRevenue');
                if (cardQrRev) cardQrRev.innerText = formatRupiah(data.summary.qr_revenue);
                
                const cardQrCnt = document.getElementById('cardQrCount');
                if (cardQrCnt) cardQrCnt.innerText = data.summary.qr_count;
                
                const cardCash = document.getElementById('cardCashTotal');
                if (cardCash) cardCash.innerText = formatRupiah(data.summary.cash_total);
                
                const cardQris = document.getElementById('cardQrisTotal');
                if (cardQris) cardQris.innerText = formatRupiah(data.summary.qris_total);
                
                const cardDebit = document.getElementById('cardDebitTotal');
                if (cardDebit) cardDebit.innerText = formatRupiah(data.summary.debit_total);

                // Update Filter Badge Counts
                const fAll = document.getElementById('filterCountAll');
                if (fAll) fAll.innerText = data.summary.count;
                const fPos = document.getElementById('filterCountPos');
                if (fPos) fPos.innerText = data.summary.pos_count;
                const fQr = document.getElementById('filterCountQr');
                if (fQr) fQr.innerText = data.summary.qr_count;
                const fCash = document.getElementById('filterCountCash');
                if (fCash) fCash.innerText = data.summary.cash_count;
                const fQris = document.getElementById('filterCountQris');
                if (fQris) fQris.innerText = data.summary.qris_count;

                allTodayTransactions = data.transactions;
                renderTodayIncomeList();

                if (openModalAfterFetch) {
                    const modalEl = document.getElementById('todayIncomeModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modalInstance.show();
                }
            }
        } catch (e) {
            console.error('Gagal mengambil data pendapatan hari ini:', e);
        }
    }

    function showTodayIncomeModal() {
        const modalEl = document.getElementById('todayIncomeModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalInstance.show();
        fetchTodayIncome(false);
    }

    function filterIncomeList(filter, btn) {
        currentIncomeFilter = filter;
        document.querySelectorAll('.income-filter').forEach(b => {
            b.classList.remove('active', 'btn-dark');
        });
        btn.classList.add('active', 'btn-dark');
        renderTodayIncomeList();
    }

    function searchIncomeList(keyword) {
        currentIncomeSearch = keyword.toLowerCase().trim();
        renderTodayIncomeList();
    }

    function renderTodayIncomeList() {
        const tbody = document.getElementById('incomeTransactionsTbody');
        if (!tbody) return;

        let filtered = allTodayTransactions.filter(trx => {
            if (currentIncomeFilter === 'pos' && trx.order_source !== 'pos') return false;
            if (currentIncomeFilter === 'qr' && trx.order_source !== 'qr') return false;
            if (currentIncomeFilter === 'Cash' && trx.payment_method !== 'Cash') return false;
            if (currentIncomeFilter === 'QRIS' && !['QRIS', 'Mock Gateway'].includes(trx.payment_method)) return false;

            if (currentIncomeSearch) {
                const searchStr = `${trx.transaction_number} ${trx.customer_name} ${trx.table_number} ${trx.items_summary}`.toLowerCase();
                if (!searchStr.includes(currentIncomeSearch)) return false;
            }

            return true;
        });

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-wallet2 fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <h6 class="fw-bold text-dark">Tidak Ada Transaksi Masuk Ditemukan</h6>
                            <small class="text-muted">Belum ada transaksi pembayaran lunas yang sesuai dengan filter ini.</small>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        filtered.forEach(trx => {
            const isQr = trx.order_source === 'qr';
            const channelBadge = isQr 
                ? `<span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="bi bi-qr-code-scan me-1"></i> QR Meja</span>` 
                : `<span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="bi bi-shop me-1"></i> POS Kasir</span>`;
            
            let methodBadgeClass = 'bg-secondary';
            if (trx.payment_method === 'Cash') methodBadgeClass = 'bg-success text-white';
            else if (['QRIS', 'Mock Gateway'].includes(trx.payment_method)) methodBadgeClass = 'bg-info text-dark';
            else if (trx.payment_method === 'Debit') methodBadgeClass = 'bg-secondary text-white';

            const methodBadge = `<span class="badge rounded-pill ${methodBadgeClass} px-2 py-1">${esc(trx.payment_method)}</span>`;

            html += `
                <tr>
                    <td class="ps-4 fw-semibold text-muted" style="white-space: nowrap;">
                        <i class="bi bi-clock me-1 small"></i>${esc(trx.time)}
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border font-monospace px-2 py-1">${esc(trx.transaction_number)}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">${esc(trx.table_number)}</div>
                        <small class="text-muted"><i class="bi bi-person me-1"></i>${esc(trx.customer_name)}</small>
                    </td>
                    <td>${channelBadge}</td>
                    <td>${methodBadge}</td>
                    <td>
                        <div class="text-truncate" style="max-width: 280px;" title="${esc(trx.items_summary)}">
                            <span class="badge bg-secondary-subtle text-secondary me-1">${Number(trx.items_count)} item</span>
                            <span class="small text-dark">${esc(trx.items_summary)}</span>
                        </div>
                    </td>
                    <td class="text-end fw-bolder text-success fs-6" style="white-space: nowrap;">
                        ${formatRupiah(trx.total)}
                    </td>
                    <td class="text-center pe-4" style="white-space: nowrap;">
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1 shadow-sm" onclick="printInIframe('/pos/receipt/' + ${trx.id})" title="Cetak Ulang Struk">
                            <i class="bi bi-receipt me-1"></i> Struk
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }
</script>
</body>
</html>
