<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('clients', ClientController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('articles', ArticleController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::post('/purchases/{purchase}/record-payment', [PurchaseController::class, 'recordPayment'])->name('purchases.record-payment');
    Route::resource('invoices', InvoiceController::class);
    Route::resource('quotes', QuoteController::class);
    Route::post('/quotes/{quote}/convert-to-invoice', [QuoteController::class, 'convertToInvoice'])->name('quotes.convert-to-invoice');
    Route::resource('payments', PaymentController::class);
    
    Route::get('/pos', function () {
        return view('pos.index');
    })->name('pos.index');
    
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SettingController::class, 'index'])->name('index');
        Route::post('/categories', [\App\Http\Controllers\SettingController::class, 'updateCategories'])->name('update-categories');
        Route::post('/types', [\App\Http\Controllers\SettingController::class, 'updateTypes'])->name('update-types');
        Route::post('/business', [\App\Http\Controllers\SettingController::class, 'updateBusiness'])->name('update-business');
    });
    
    Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/pending', [\App\Http\Controllers\PosController::class, 'savePending'])->name('pos.pending');
    
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
