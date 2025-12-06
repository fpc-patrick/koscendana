<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SaldoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;

use App\Http\Controllers\KamarController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TagihanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//route login
Route::get('/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::get('/register', [AuthController::class, 'register']);
Route::post('/register', [AuthController::class, 'process']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// route dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
Route::get('/', [DashboardController::class, 'index'])->middleware('auth');


//route saldo
// web.php



Route::get('/pengaturansaldo', [SaldoController::class, 'index']);
Route::get('/pengaturansaldo/create', [SaldoController::class, 'create']);
Route::post('/pengaturansaldo', [SaldoController::class, 'store']);
Route::get('/pengaturansaldo/{id_saldo}/edit', [SaldoController::class, 'edit']);
Route::put('/pengaturansaldo/{id_saldo}', [SaldoController::class, 'update'])->name('pengaturansaldo.update');
Route::delete('/pengaturansaldo/{id_saldo}', [SaldoController::class, 'destroy']);


Route::resource('/account', AccountController::class)->middleware('auth');

Route::resource('kamars', KamarController::class);
Route::get('/kamars/next-number/{lantai}', [KamarController::class,'nextNumber']);

Route::resource('penghunis', PenghuniController::class);
Route::get('/penghuni/{id}/kamars', [PenghuniController::class, 'getKamars']);

// Form pindah kamar
Route::get('/penghunis/{id}/pindah', [PenghuniController::class, 'formPindah'])->name('penghunis.pindah.form');

// Proses pindah kamar
Route::post('/penghunis/{id}/pindah', [PenghuniController::class, 'pindahKamar'])->name('penghunis.pindah.store');



Route::resource('transaksis', TransaksiController::class);
Route::post('/transaksi/{id}/perpanjang', [TransaksiController::class, 'perpanjang'])->name('transaksi.perpanjang');
Route::post('/transaksi/{transaksi}/batal-kamar/{nokamar}', [TransaksiController::class, 'batalKamar'])->name('transaksi.batalkamar');


Route::resource('tagihans', TagihanController::class);
Route::get('/auto-check-expired', [TagihanController::class, 'autoCheckExpired'])->name('tagihans.autoCheckExpired');


Route::resource('barangs', BarangController::class);