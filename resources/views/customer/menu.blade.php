<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Menu Meja {{ $table->name }} - Rumah Makan Kulu Asri</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts & Bootstrap 5 & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Playfair+Display:ital,wght@0,700;1,700;1,800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #1b5e20;
            --primary-light: #2e7d32;
            --primary-soft: #e8f5e9;
            --accent: #d97706;
            --accent-soft: #fef3c7;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-page: #f8faf9;
            --card-border: #e2ece5;
        }

        * { -webkit-tap-highlight-color: transparent; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            padding-bottom: 120px; /* Space for floating cart */
            overflow-x: hidden;
        }

        .brand-tagline-stylish {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        /* App Bar */
        .app-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 12px 18px;
        }

        .table-badge {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 13px;
            box-shadow: 0 4px 12px rgba(27, 94, 32, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Category Horizontal Scroll */
        .category-scroll {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: none;
            padding: 4px 16px 12px 16px;
        }
        .category-scroll::-webkit-scrollbar { display: none; }

        .cat-chip {
            white-space: nowrap;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid var(--card-border);
            background: white;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .cat-chip.active, .cat-chip:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(27, 94, 32, 0.2);
        }

        /* Product Card */
        .product-card {
            background: white;
            border-radius: 20px;
            border: 1px solid var(--card-border);
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .product-card:active { transform: scale(0.98); }

        .product-media {
            height: 140px;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .product-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-media .fallback-icon {
            font-size: 3.5rem;
            color: var(--primary-light);
            opacity: 0.8;
        }

        .stock-tag {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(4px);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 30px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        .product-body {
            padding: 14px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .product-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 42px;
        }
        .product-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }
        .product-price {
            font-size: 15px;
            font-weight: 800;
            color: var(--accent);
            margin-top: auto;
        }

        /* Button Add / Stepper */
        .btn-add {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 14px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        .btn-add:hover { background: var(--primary-light); }
        .btn-add:disabled { background: #cbd5e1; cursor: not-allowed; }

        .qty-stepper {
            display: flex;
            align-items: center;
            background: #f1f5f3;
            border-radius: 12px;
            padding: 3px;
        }
        .qty-stepper button {
            width: 28px;
            height: 28px;
            border: none;
            background: white;
            border-radius: 8px;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .qty-stepper span {
            width: 28px;
            text-align: center;
            font-weight: 700;
            font-size: 13px;
        }

        /* Floating Cart Bar */
        .floating-cart {
            position: fixed;
            bottom: 20px;
            left: 16px;
            right: 16px;
            background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
            color: white;
            border-radius: 20px;
            padding: 14px 20px;
            box-shadow: 0 12px 30px rgba(27, 94, 32, 0.4);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1020 !important; /* Stays below offcanvas */
            cursor: pointer;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease;
            transform: translateY(120%);
        }
        .floating-cart.show {
            transform: translateY(0);
        }

        /* Bottom Sheet Cart */
        .offcanvas-bottom {
            height: auto !important;
            max-height: 88vh !important;
            border-radius: 28px 28px 0 0 !important;
            border-top: none !important;
            z-index: 1065 !important;
            box-shadow: 0 -15px 40px rgba(0, 0, 0, 0.2) !important;
        }
        .offcanvas-backdrop {
            z-index: 1060 !important;
        }
        .offcanvas-bottom .offcanvas-body {
            max-height: calc(88vh - 75px);
            overflow-y: auto !important;
            padding-bottom: 50px !important;
            -webkit-overflow-scrolling: touch;
        }
        .drag-handle {
            width: 44px;
            height: 5px;
            background: #cbd5e1;
            border-radius: 10px;
            margin: 10px auto 16px;
        }

        /* Search input */
        .search-box {
            position: relative;
            padding: 0 16px 8px;
        }
        .search-box input {
            border-radius: 50px;
            border: 1px solid var(--card-border);
            padding: 10px 18px 10px 42px;
            font-size: 14px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .search-box i {
            position: absolute;
            left: 30px;
            top: 11px;
            color: var(--text-muted);
            font-size: 16px;
        }

        /* Banner active order */
        .active-order-banner {
            margin: 0 16px 12px;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fed7aa;
            border-radius: 16px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: #9a3412;
            box-shadow: 0 4px 12px rgba(245, 124, 0, 0.08);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="app-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 38px; height: 38px; background: var(--primary); color: white;">
                <i class="bi bi-shop fs-5"></i>
            </div>
            <div>
                <h6 class="m-0 fw-bold" style="color: var(--primary); line-height: 1.1;">Kulu Asri</h6>
                <small class="brand-tagline-stylish" style="font-size: 11px; color: #b45309;">Jagonya Ikan Bakar!</small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="table-badge">
                <i class="bi bi-geo-alt-fill"></i> {{ $table->name }}
            </div>
        </div>
    </header>

    <!-- Banner Jika Meja Memiliki Pesanan Aktif -->
    @if($activeOrder)
    <div class="mt-3">
        <a href="{{ route('customer.order_status', $activeOrder->transaction_number) }}" class="active-order-banner">
            <div class="d-flex align-items-center gap-3">
                <div class="spinner-grow spinner-grow-sm text-warning" role="status"></div>
                <div>
                    <div class="fw-bold" style="font-size: 13px;">Pesanan Meja Sedang Diproses</div>
                    <small class="opacity-75" style="font-size: 11px;">#{{ $activeOrder->transaction_number }} • Total {{ number_format($activeOrder->total, 0, ',', '.') }}</small>
                </div>
            </div>
            <i class="bi bi-chevron-right fw-bold"></i>
        </a>
    </div>
    @endif

    <!-- Hero / Greeting -->
    <div class="px-3 pt-3 pb-2">
        <div class="d-inline-flex align-items-center gap-1 px-3 py-1 rounded-pill mb-2 shadow-sm" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fcd34d;">
            <i class="bi bi-fire text-danger" style="font-size: 13px;"></i>
            <span class="brand-tagline-stylish" style="font-size: 12px; color: #92400e;">Kulu Asri - Jagonya Ikan Bakar!</span>
        </div>
        <h5 class="fw-bold m-0" style="color: var(--text-main);">Selamat Menikmati 🌿</h5>
        <p class="text-muted small m-0">Pilih menu favorit Anda langsung dari meja ini</p>
    </div>

    <!-- Search Bar -->
    <div class="search-box mt-2">
        <i class="bi bi-search"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Cari makanan atau minuman..." oninput="handleSearch(this.value)">
    </div>

    <!-- Categories Filter -->
    <div class="category-scroll mt-1">
        <button class="cat-chip active" onclick="filterCategory('all', this)">
            <i class="bi bi-grid-fill me-1"></i> Semua
        </button>
        @foreach($categories as $category)
        <button class="cat-chip" onclick="filterCategory({{ $category->id }}, this)">
            @if(stripos($category->name, 'minum') !== false)
                <i class="bi bi-cup-straw me-1"></i>
            @elseif(stripos($category->name, 'snack') !== false)
                <i class="bi bi-basket me-1"></i>
            @else
                <i class="bi bi-egg-fried me-1"></i>
            @endif
            {{ $category->name }}
        </button>
        @endforeach
    </div>

    <!-- Menu Grid -->
    <div class="container-fluid px-3 mt-2">
        <div class="row g-3" id="productGrid">
            @foreach($allProducts as $product)
            <div class="col-6 col-md-4 col-lg-3 product-item" 
                 data-category="{{ $product->category_id }}" 
                 data-name="{{ strtolower($product->name) }}"
                 id="product-card-{{ $product->id }}">
                <div class="product-card">
                    <div class="product-media">
                        @if($product->image)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <div class="fallback-icon">
                                @if(stripos($product->category->name ?? '', 'minum') !== false)
                                    <i class="bi bi-cup-hot text-info"></i>
                                @elseif(stripos($product->category->name ?? '', 'snack') !== false)
                                    <i class="bi bi-basket text-warning"></i>
                                @else
                                    <i class="bi bi-egg-fried text-success"></i>
                                @endif
                            </div>
                        @endif

                        @if($product->stock <= 0)
                            <div class="stock-tag text-danger bg-white">
                                <i class="bi bi-x-circle-fill"></i> Habis
                            </div>
                        @elseif($product->stock <= 5)
                            <div class="stock-tag text-warning bg-white">
                                Sisa {{ $product->stock }}
                            </div>
                        @endif
                    </div>

                    <div class="product-body">
                        <span class="badge bg-light text-secondary mb-1 align-self-start" style="font-size: 10px; font-weight: 500;">
                            {{ $product->category->name ?? 'Menu' }}
                        </span>
                        <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
                        @if($product->description)
                            <div class="product-desc">{{ $product->description }}</div>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <div class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>

                            <div id="btn-container-{{ $product->id }}">
                                @if($product->stock > 0)
                                    <button class="btn-add" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }})">
                                        <i class="bi bi-plus-lg"></i> Tambah
                                    </button>
                                @else
                                    <button class="btn-add" disabled>Habis</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div id="emptyNotice" class="text-center py-5 d-none">
            <i class="bi bi-search fs-1 text-muted"></i>
            <h6 class="fw-bold mt-3 text-secondary">Menu tidak ditemukan</h6>
            <p class="text-muted small">Coba cari dengan kata kunci lain.</p>
        </div>

        <!-- Brand Footer -->
        <div class="text-center py-5 mt-4 border-top" style="margin-bottom: 20px;">
            <div class="d-inline-flex align-items-center justify-content-center gap-2 mb-2">
                <span style="height: 1px; width: 35px; background: #cbd5e1;"></span>
                <i class="bi bi-shop fs-5" style="color: var(--primary);"></i>
                <span style="height: 1px; width: 35px; background: #cbd5e1;"></span>
            </div>
            <div class="brand-tagline-stylish fs-5" style="color: var(--primary);">
                Kulu Asri <span style="color: #d97706;">-</span> Jagonya Ikan Bakar!
            </div>
            <p class="text-muted small mt-2 mb-0" style="font-size: 11.5px;">
                Rumah Makan Kulu Asri • Cita Rasa Nusantara Tradisional<br>
                Jl. Singosari No. 7 Karanganyar, Pekalongan
            </p>
        </div>
    </div>

    <!-- Floating Sticky Cart Bar -->
    <div class="floating-cart" id="floatingCart" onclick="openCartSheet()">
        <div class="d-flex align-items-center gap-3">
            <div class="position-relative">
                <i class="bi bi-basket3-fill fs-4"></i>
                <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle rounded-pill" id="cartBadge">0</span>
            </div>
            <div>
                <div class="fw-bold" id="cartItemSummary" style="font-size: 14px;">0 Item</div>
                <small class="opacity-75" style="font-size: 11px;">Meja {{ $table->name }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold fs-6" id="cartTotalSummary">Rp 0</span>
            <i class="bi bi-arrow-right-circle-fill fs-5"></i>
        </div>
    </div>

    <!-- Offcanvas Bottom Sheet: Keranjang & Checkout -->
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="cartSheet" aria-labelledby="cartSheetLabel">
        <div class="drag-handle"></div>
        <div class="offcanvas-header pt-0 px-4 pb-2 border-bottom">
            <h5 class="offcanvas-title fw-bold text-dark" id="cartSheetLabel">
                <i class="bi bi-receipt-cutoff text-success me-2"></i> Keranjang Meja {{ $table->name }}
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body px-4 pt-3 pb-4">
            <!-- Customer Name Input -->
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary mb-1">
                    <i class="bi bi-person-circle me-1"></i> Nama Pemesan (Opsional)
                </label>
                <input type="text" id="customerName" class="form-control rounded-3" placeholder="Contoh: Budi / Keluarga Rahma" maxlength="50">
            </div>

            <!-- Item List -->
            <div id="cartItemsList" class="mb-3" style="max-height: 240px; overflow-y: auto;">
                <!-- Rendered by JS -->
            </div>

            <!-- Notes Modal/Input Shortcut inside item -->

            <!-- Ringkasan Pembayaran -->
            <div class="bg-light p-3 rounded-4 mb-3 border">
                <div class="d-flex justify-content-between mb-1 small text-muted">
                    <span>Subtotal</span>
                    <span id="sheetSubtotal" class="fw-semibold text-dark">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between mb-1 small text-muted">
                    <span>Biaya Layanan & Meja</span>
                    <span class="text-success fw-bold">Gratis</span>
                </div>
                <div class="d-flex justify-content-between pt-2 border-top fs-5 fw-bold text-dark">
                    <span>Total Pembayaran</span>
                    <span id="sheetTotal" style="color: var(--primary);">Rp 0</span>
                </div>
            </div>

            <!-- Metode Pembayaran Selector -->
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary mb-2">
                    <i class="bi bi-wallet2 me-1"></i> Metode Pembayaran
                </label>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="payMethod" id="payQris" value="QRIS" checked>
                        <label class="btn btn-outline-success w-100 py-2 rounded-3 text-start small fw-bold d-flex align-items-center gap-2" for="payQris">
                            <i class="bi bi-qr-code-scan fs-5"></i> QRIS Instant
                        </label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="payMethod" id="payEwallet" value="E-Wallet">
                        <label class="btn btn-outline-success w-100 py-2 rounded-3 text-start small fw-bold d-flex align-items-center gap-2" for="payEwallet">
                            <i class="bi bi-phone fs-5"></i> GoPay / OVO
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <button class="btn btn-success w-100 py-3 rounded-pill fw-bold fs-6 shadow-sm" id="btnCheckout" onclick="processCheckout()">
                Bayar Sekarang <i class="bi bi-arrow-right-circle-fill ms-2"></i>
            </button>
            <div class="text-center mt-3 pt-2 border-top">
                <span class="brand-tagline-stylish text-muted" style="font-size: 11.5px;">Kulu Asri - Jagonya Ikan Bakar!</span>
            </div>
        </div>
    </div>

    <!-- Note Modal -->
    <div class="modal fade" id="noteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 bg-light rounded-top-4">
                    <h6 class="modal-title fw-bold text-dark" id="noteModalTitle">Catatan Pesanan</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <textarea id="noteInput" class="form-control rounded-3" rows="3" placeholder="Contoh: Pedas sedang, es sedikit, jangan pakai daun bawang..." maxlength="200"></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" onclick="saveNote()">Simpan Catatan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const tableToken = "{{ $table->qr_token }}";
        const tableName = "{{ $table->name }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // State Cart
        let cart = [];
        let editingProductId = null;

        const cartSheet = new bootstrap.Offcanvas(document.getElementById('cartSheet'));
        const noteModal = new bootstrap.Modal(document.getElementById('noteModal'));

        // Load local cart if any
        document.addEventListener('DOMContentLoaded', () => {
            const cartSheetEl = document.getElementById('cartSheet');
            if (cartSheetEl) {
                cartSheetEl.addEventListener('show.bs.offcanvas', () => {
                    const floatBar = document.getElementById('floatingCart');
                    if (floatBar) {
                        floatBar.classList.remove('show');
                        floatBar.style.display = 'none';
                    }
                });
                cartSheetEl.addEventListener('hidden.bs.offcanvas', () => {
                    const floatBar = document.getElementById('floatingCart');
                    if (floatBar && cart.length > 0) {
                        floatBar.style.display = 'flex';
                        void floatBar.offsetWidth;
                        floatBar.classList.add('show');
                    }
                });
            }

            const savedCart = localStorage.getItem(`cart_${tableToken}`);
            if (savedCart) {
                try {
                    cart = JSON.parse(savedCart);
                    updateCartUI();
                } catch(e) {}
            }
        });

        function saveCart() {
            localStorage.setItem(`cart_${tableToken}`, JSON.stringify(cart));
            updateCartUI();
        }

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
        }

        // Add to Cart
        function addToCart(id, name, price, stock) {
            let existing = cart.find(item => item.id === id);
            if (existing) {
                if (existing.qty < stock) {
                    existing.qty += 1;
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Batas Stok',
                        text: `Maksimal pemesanan ${stock} porsi sesuai stok yang ada.`,
                        confirmButtonColor: '#1b5e20'
                    });
                    return;
                }
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    qty: 1,
                    stock: stock,
                    notes: ''
                });
            }
            saveCart();
            triggerHapticFeedback();
        }

        function updateQty(id, delta) {
            let item = cart.find(i => i.id === id);
            if (!item) return;

            item.qty += delta;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== id);
            } else if (item.qty > item.stock) {
                item.qty = item.stock;
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Stok',
                    text: `Maksimal stok tersedia hanya ${item.stock}`,
                    confirmButtonColor: '#1b5e20'
                });
            }
            saveCart();
        }

        function openNoteModal(id) {
            editingProductId = id;
            let item = cart.find(i => i.id === id);
            if (!item) return;

            document.getElementById('noteModalTitle').innerText = `Catatan: ${item.name}`;
            document.getElementById('noteInput').value = item.notes || '';
            noteModal.show();
        }

        function saveNote() {
            if (!editingProductId) return;
            let item = cart.find(i => i.id === editingProductId);
            if (item) {
                item.notes = document.getElementById('noteInput').value.trim();
                saveCart();
            }
            noteModal.hide();
        }

        function triggerHapticFeedback() {
            if (navigator.vibrate) {
                navigator.vibrate(25);
            }
        }

        // Update UI (Card Steppers & Floating Cart)
        function updateCartUI() {
            const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
            const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

            // Floating Cart Bar
            const floatBar = document.getElementById('floatingCart');
            const cartSheetEl = document.getElementById('cartSheet');
            const isSheetOpen = cartSheetEl && cartSheetEl.classList.contains('show');

            if (totalQty > 0 && !isSheetOpen) {
                floatBar.style.display = 'flex';
                void floatBar.offsetWidth;
                floatBar.classList.add('show');
                document.getElementById('cartBadge').innerText = totalQty;
                document.getElementById('cartItemSummary').innerText = `${totalQty} Item Pesanan`;
                document.getElementById('cartTotalSummary').innerText = formatRupiah(totalPrice);
            } else if (isSheetOpen) {
                floatBar.classList.remove('show');
                floatBar.style.display = 'none';
            } else {
                floatBar.classList.remove('show');
                setTimeout(() => {
                    if (floatBar && !floatBar.classList.contains('show')) {
                        floatBar.style.display = 'none';
                    }
                }, 300);
            }

            // Sync Card Steppers on Product Grid
            @foreach($allProducts as $product)
                const container{{ $product->id }} = document.getElementById('btn-container-{{ $product->id }}');
                const inCart{{ $product->id }} = cart.find(i => i.id === {{ $product->id }});

                if (container{{ $product->id }}) {
                    @if($product->stock > 0)
                        if (inCart{{ $product->id }}) {
                            container{{ $product->id }}.innerHTML = `
                                <div class="qty-stepper">
                                    <button onclick="updateQty({{ $product->id }}, -1)">−</button>
                                    <span>${inCart{{ $product->id }}.qty}</span>
                                    <button onclick="updateQty({{ $product->id }}, 1)">+</button>
                                </div>
                            `;
                        } else {
                            container{{ $product->id }}.innerHTML = `
                                <button class="btn-add" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->stock }})">
                                    <i class="bi bi-plus-lg"></i> Tambah
                                </button>
                            `;
                        }
                    @endif
                }
            @endforeach

            // Render Cart Sheet Items
            renderCartSheet(totalPrice);
        }

        function renderCartSheet(totalPrice) {
            const list = document.getElementById('cartItemsList');
            if (cart.length === 0) {
                list.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-basket3 fs-2 text-muted"></i>
                        <p class="small mt-2 mb-0">Keranjang masih kosong.</p>
                    </div>
                `;
                document.getElementById('btnCheckout').disabled = true;
            } else {
                document.getElementById('btnCheckout').disabled = false;
                let html = '';
                cart.forEach(item => {
                    html += `
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div style="max-width: 60%;">
                                <div class="fw-bold small text-dark">${item.name}</div>
                                <div class="text-muted small">${formatRupiah(item.price)}</div>
                                ${item.notes ? `<div class="badge bg-light text-dark border small mt-1"><i class="bi bi-pencil me-1"></i>${item.notes}</div>` : ''}
                                <div class="mt-1">
                                    <a href="javascript:void(0)" onclick="openNoteModal(${item.id})" class="text-success small fw-semibold text-decoration-none" style="font-size: 11px;">
                                        <i class="bi bi-chat-dots me-1"></i>${item.notes ? 'Ubah Catatan' : '+ Tambah Catatan'}
                                    </a>
                                </div>
                            </div>
                            <div class="qty-stepper">
                                <button onclick="updateQty(${item.id}, -1)">−</button>
                                <span>${item.qty}</span>
                                <button onclick="updateQty(${item.id}, 1)">+</button>
                            </div>
                        </div>
                    `;
                });
                list.innerHTML = html;
            }

            document.getElementById('sheetSubtotal').innerText = formatRupiah(totalPrice);
            document.getElementById('sheetTotal').innerText = formatRupiah(totalPrice);
        }

        function openCartSheet() {
            const floatBar = document.getElementById('floatingCart');
            if (floatBar) {
                floatBar.classList.remove('show');
                floatBar.style.display = 'none';
            }
            cartSheet.show();
        }

        // Search & Filter
        function handleSearch(term) {
            term = term.toLowerCase().trim();
            const items = document.querySelectorAll('.product-item');
            let visibleCount = 0;

            items.forEach(el => {
                const name = el.getAttribute('data-name');
                if (name.includes(term)) {
                    el.classList.remove('d-none');
                    visibleCount++;
                } else {
                    el.classList.add('d-none');
                }
            });

            const notice = document.getElementById('emptyNotice');
            if (visibleCount === 0) notice.classList.remove('d-none');
            else notice.classList.add('d-none');
        }

        function filterCategory(catId, btn) {
            document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');

            const items = document.querySelectorAll('.product-item');
            let visibleCount = 0;

            items.forEach(el => {
                const itemCat = el.getAttribute('data-category');
                if (catId === 'all' || itemCat == catId) {
                    el.classList.remove('d-none');
                    visibleCount++;
                } else {
                    el.classList.add('d-none');
                }
            });

            const notice = document.getElementById('emptyNotice');
            if (visibleCount === 0) notice.classList.remove('d-none');
            else notice.classList.add('d-none');
        }

        // Checkout Process
        async function processCheckout() {
            if (cart.length === 0) return;

            const btn = document.getElementById('btnCheckout');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Memproses Pembayaran...`;

            const customerName = document.getElementById('customerName').value.trim();
            const selectedPayMethod = document.querySelector('input[name="payMethod"]:checked').value;

            try {
                const response = await fetch(`/order/table/${tableToken}/checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_name: customerName,
                        payment_method: selectedPayMethod,
                        cart: cart.map(item => ({
                            id: item.id,
                            qty: item.qty,
                            notes: item.notes
                        }))
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Clear local cart
                    localStorage.removeItem(`cart_${tableToken}`);
                    cart = [];

                    // Redirect to payment URL (Mock simulator or Midtrans Snap)
                    if (data.payment_url) {
                        window.location.href = data.payment_url;
                    } else {
                        window.location.href = data.status_url;
                    }
                } else {
                    throw new Error(data.message || 'Gagal memproses pesanan');
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Checkout',
                    text: err.message,
                    confirmButtonColor: '#1b5e20'
                });
                btn.disabled = false;
                btn.innerHTML = `Bayar Sekarang <i class="bi bi-arrow-right-circle-fill ms-2"></i>`;
            }
        }
    </script>
</body>
</html>
