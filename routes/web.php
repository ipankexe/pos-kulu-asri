<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\PaymentWebhookController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

// --- ADMIN ROUTES ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard/chart-comparison', [\App\Http\Controllers\AdminController::class, 'getChartComparisonData'])->name('admin.dashboard.chart_comparison');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    
    // Kelola Meja & QR
    Route::resource('tables', TableController::class)->except(['create', 'show', 'edit']);
    Route::post('/tables/{id}/regenerate-qr', [TableController::class, 'regenerateQr'])->name('tables.regenerate_qr');
    Route::post('/tables/{id}/toggle-active', [TableController::class, 'toggleActive'])->name('tables.toggle_active');
    Route::get('/tables/{id}/print-qr', [TableController::class, 'printQr'])->name('tables.print_qr');
    
    // Laporan & Users
    Route::get('/reports', [\App\Http\Controllers\AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/export', [\App\Http\Controllers\AdminController::class, 'exportReports'])->name('admin.reports.export');
    Route::get('/reports/pdf', [\App\Http\Controllers\AdminController::class, 'exportPdf'])->name('admin.reports.pdf');
    Route::post('/void/{id}', [\App\Http\Controllers\AdminController::class, 'voidTransaction'])->name('admin.void');
    Route::get('/void-logs', [\App\Http\Controllers\AdminController::class, 'voidLogs'])->name('admin.voids');
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'edit', 'show']);
});

// --- POS / KASIR ROUTES ---
Route::middleware(['auth', 'role:kasir'])->prefix('pos')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('pos.index');
    Route::post('/save-order', [PosController::class, 'saveOrder'])->name('pos.save_order');
    Route::post('/pay-order/{id}', [PosController::class, 'payOrder'])->name('pos.pay_order');
    Route::get('/active-order/{table_name}', [PosController::class, 'getActiveOrder'])->name('pos.active_order');
    Route::get('/active-transactions', [PosController::class, 'getActiveTransactions'])->name('pos.active_transactions');
    Route::get('/transaction/{id}', [PosController::class, 'getTransaction'])->name('pos.transaction');
    Route::get('/receipt/{id}', [PosController::class, 'printReceipt'])->name('pos.receipt');
    Route::get('/kitchen-ticket/{id}', [PosController::class, 'printKitchenTicket'])->name('pos.kitchen_ticket');
    Route::get('/history', [PosController::class, 'history'])->name('pos.history');
    Route::get('/today-income', [PosController::class, 'getTodayIncome'])->name('pos.today_income');
    Route::post('/void/{id}', [PosController::class, 'voidTransaction'])->name('pos.void');
    Route::get('/close-store/summary', [PosController::class, 'eodSummary'])->name('pos.eod_summary');
    Route::get('/close-store/print', [PosController::class, 'printEod'])->name('pos.print_eod');
    Route::get('/tables', [PosController::class, 'getTables'])->name('pos.tables');
    Route::post('/tables/{id}/free', [PosController::class, 'freeTable'])->name('pos.tables.free');

    // QR Order Sync & Antrean Dapur
    Route::get('/incoming-qr-orders', [PosController::class, 'getIncomingQrOrders'])->name('pos.incoming_qr');
    Route::post('/orders/{id}/confirm-qr', [PosController::class, 'confirmQrOrder'])->name('pos.confirm_qr');
    Route::post('/orders/{id}/status', [PosController::class, 'updateOrderStatus'])->name('pos.update_status');
});

// --- CUSTOMER SELF-ORDER VIA QR ROUTES (Public / Guest) ---
Route::prefix('order')->group(function () {
    // Daftar semua meja + link token-nya: KHUSUS staf yang login (untuk demo/testing),
    // tidak boleh publik karena membocorkan token QR semua meja.
    Route::get('/', [CustomerOrderController::class, 'tablesList'])->middleware('auth')->name('customer.tables_list');
    Route::get('/table/{token}', [CustomerOrderController::class, 'showMenu'])->name('customer.table_order');
    Route::get('/table/{token}/menu-data', [CustomerOrderController::class, 'getMenuData'])->name('customer.menu_data');
    Route::post('/table/{token}/checkout', [CustomerOrderController::class, 'checkout'])->middleware('throttle:10,1')->name('customer.checkout');
    
    // Simulator Pembayaran (Mock Gateway Testing)
    Route::get('/payment/simulator/{transaction_number}', [CustomerOrderController::class, 'paymentSimulator'])->name('customer.mock_payment.show');
    Route::post('/payment/simulator/{transaction_number}/pay', [CustomerOrderController::class, 'processMockPayment'])->name('customer.mock_payment.process');

    // Pelacakan Status Pesanan Pelanggan
    Route::get('/status/{transaction_number}', [CustomerOrderController::class, 'orderStatus'])->name('customer.order_status');
    Route::get('/status/{transaction_number}/check', [CustomerOrderController::class, 'checkStatus'])->name('customer.check_status');
});

// --- PAYMENT GATEWAY WEBHOOK (Public, Excluded from CSRF) ---
Route::post('/payment/webhook', [PaymentWebhookController::class, 'handleWebhook'])->name('payment.webhook');

Route::get('/home', function () {
    if(auth()->check()) {
        if(auth()->user()->role === 'admin') return redirect()->route('admin.dashboard');
        if(auth()->user()->role === 'kasir') return redirect()->route('pos.index');
    }
    return redirect()->route('login');
})->name('home');
