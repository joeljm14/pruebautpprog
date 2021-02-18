<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Listado;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/fetch', [Listado::class,'action'])->name('Listado.action');
Route::get('/detalle', [Listado::class,'detalle'])->name('Listado.detalle');
Route::post('/import', [Listado::class,'import'])->name('Listado.import');