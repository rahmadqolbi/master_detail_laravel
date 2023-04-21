<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LatihanController; 
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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/tampil', [LatihanController::class, 'tampil'])->name('tampil');
//NOTE - Routing mengatur proses view dan controller
Route::get('/master', [LatihanController::class, 'index'])->name('index');
//NOTE - index menampilkan data LatihanController::class, 'method'
Route::get('/', [LatihanController::class, 'header'])->name('header');
//NOTE - Request get menampilkan elemen html

Route::get('/detail/{no_invoice}', [LatihanController::class, 'detail'])->name('detail');
// detail Untuk merequest data select row

// Route::get('/detail2/{no_invoice}', [LatihanController::class, 'detail2'])->name('detail2');

Route::get('/tambah', [LatihanController::class, 'tambah']);
Route::post('/simpan', [LatihanController::class, 'simpan']);

Route::get('/edit/{no_invoice}', [LatihanController::class, 'edit']);
Route::post('/simpan_edit', [LatihanController::class, 'simpan_edit']);

Route::get('/delete/{no_invoice}', [LatihanController::class, 'delete']);
Route::post('/proses_delete', [LatihanController::class, 'proses_delete']);

Route::get('/reports', [LatihanController::class, 'reports']);
Route::get('/export', [LatihanController::class, 'export']);

Route::post('/getPosition/{no_invoice}', [LatihanController::class, 'getPosition'])->name('getPosition.post');


// Route::get('/detail{no_invoice}', [LatihanController::class, 'detail']);

// Route::get('/penjualan_detail/{no_invoice}', [LatihanController::class, 'showDetail']);












// Route::get('/delete/{no_invoice}', [LatihanController::class, 'delete']);
// Route::post('/proses_delete', [LatihanController::class, 'proses_delete']);
