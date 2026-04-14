<?php

use App\Modules\IAM\ProfileController;
use App\Modules\Organization\BranchController;
use App\Modules\Catalog\ProductController;
use App\Modules\Operations\TransactionController;
use App\Modules\Analytics\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('branches', BranchController::class);
    Route::resource('products', ProductController::class);
    Route::resource('transactions', TransactionController::class);
});

require __DIR__.'/auth.php';
