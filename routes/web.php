<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/payment', [PaymentController::class, 'pay'])->name('payment');
Route::get('/payment/callback', [PaymentController::class, 'payment_callback'])->name('payment.callback');
Route::post('/process_payment', [PaymentController::class, 'process_payment'])->name('process_payment');
require __DIR__.'/auth.php';
