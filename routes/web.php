<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('/login', [PageController::class, 'login'])->name('login');

Route::middleware(['auth'])->group(function () {
  Route::get('/', [PageController::class, 'home'])->name('home');
  Route::get('/reports', [PageController::class, 'reports'])->name('reports');
  Route::get('/transactions', [PageController::class, 'transactions'])->name('transactions');
  Route::get('/transactions/{transactionId}', [PageController::class, 'transactionById'])->name('transaction');
});