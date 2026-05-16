<?php
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MapController::class, 'index']);
Route::post('/nodes', [MapController::class, 'store'])->name('nodes.store');

Route::get('/cable/{id}', [MapController::class, 'show'])->name('cable.show');

Route::post('/cable/{id}/update', [MapController::class, 'updateConnection'])->name('cable.update');
// Rute untuk mengelola detail ODP dan Port Splitter Pelanggan
Route::get('/odp/{id}', [MapController::class, 'showOdp'])->name('odp.show');
Route::post('/odp-port/{id}/update', [MapController::class, 'updatePort'])->name('odp.port.update');
