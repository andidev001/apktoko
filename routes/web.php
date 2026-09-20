<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'authenticate']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Settings
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Data Barang Ajax (Accessible by all auth users for transactions page)
    Route::get('products/data', [ProductController::class, 'data'])->name('products.data');
    Route::get('products/barcode/{kode}', [ProductController::class, 'getByBarcode'])->name('products.barcode');



    // Laporan Transaksi (Accessible by kasir & admin)
    Route::get('reports/data', [ReportController::class, 'data'])->name('reports.data');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    // Transaksi & Pembayaran (Accessible by all auth users, e.g. kasir & admin)
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('transactions/{transaction}/payment', [TransactionController::class, 'paymentForm'])->name('transactions.paymentForm');
    Route::post('transactions/{transaction}/payment', [TransactionController::class, 'processPayment'])->name('transactions.processPayment');
    Route::post('transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::post('transactions/{transaction}/update-name', [TransactionController::class, 'updateName'])->name('transactions.updateName');
    Route::get('transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::get('transactions/{transaction}/invoice', [TransactionController::class, 'invoice'])->name('transactions.invoice');

    Route::middleware(['role:admin'])->group(function () {
        // Data Barang & Stok Barang
        Route::get('products/template', [ProductController::class, 'downloadTemplate'])->name('products.template');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::get('products/data_index', [ProductController::class, 'dataIndex'])->name('products.data_index');
        Route::get('products/data_stock', [ProductController::class, 'dataStock'])->name('products.data_stock');
        Route::resource('products', ProductController::class);
        Route::get('stock', [ProductController::class, 'stock'])->name('products.stock');
        Route::post('stock/{product}', [ProductController::class, 'updateStock'])->name('products.update_stock');

        // Laporan (Restricted for admin)
        Route::get('reports/rekap', [ReportController::class, 'rekap'])->name('reports.rekap');
        Route::get('reports/keuntungan/data', [ReportController::class, 'dataKeuntungan'])->name('reports.keuntungan.data');
        Route::get('reports/keuntungan', [ReportController::class, 'keuntungan'])->name('reports.keuntungan');
        Route::get('reports/keuntungan/print', [ReportController::class, 'printKeuntungan'])->name('reports.keuntungan.print');
        Route::get('reports/print', [ReportController::class, 'printRekap'])->name('reports.print');

        // Backup & Restore
        Route::get('backup', [\App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
        Route::get('backup/download', [\App\Http\Controllers\BackupController::class, 'backup'])->name('backup.download');
        Route::post('backup/restore', [\App\Http\Controllers\BackupController::class, 'restore'])->name('backup.restore');

        // Pengaturan
        Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        // User Management
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
    });
});
