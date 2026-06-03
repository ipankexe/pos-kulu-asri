<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TableController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('tables', TableController::class)->except(['create', 'show', 'edit']);
    
    // Laporan & Users
    Route::get('/reports', [\App\Http\Controllers\AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/export', [\App\Http\Controllers\AdminController::class, 'exportReports'])->name('admin.reports.export');
    Route::get('/reports/pdf', [\App\Http\Controllers\AdminController::class, 'exportPdf'])->name('admin.reports.pdf');
    Route::post('/void/{id}', [\App\Http\Controllers\AdminController::class, 'voidTransaction'])->name('admin.void');
    Route::get('/void-logs', [\App\Http\Controllers\AdminController::class, 'voidLogs'])->name('admin.voids');
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'edit', 'show']);
});

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
    Route::post('/void/{id}', [PosController::class, 'voidTransaction'])->name('pos.void');
    Route::get('/close-store/summary', [PosController::class, 'eodSummary'])->name('pos.eod_summary');
    Route::get('/close-store/print', [PosController::class, 'printEod'])->name('pos.print_eod');
    Route::get('/tables', [PosController::class, 'getTables'])->name('pos.tables');
    Route::post('/tables/{id}/free', [PosController::class, 'freeTable'])->name('pos.tables.free');
});

Route::get('/home', function () {
    if(auth()->check()) {
        if(auth()->user()->role === 'admin') return redirect()->route('admin.dashboard');
        if(auth()->user()->role === 'kasir') return redirect()->route('pos.index');
    }
    return redirect()->route('login');
})->name('home');
