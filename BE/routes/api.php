<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LaboranController;
use App\Http\Controllers\MakulController;
use App\Http\Controllers\PeriodeTahunController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\WaktuPerkuliahanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('jadwal', JadwalController::class);
Route::apiResource('dosen', DosenController::class);
Route::apiResource('laboran', LaboranController::class);
Route::apiResource('makul', MakulController::class);
Route::apiResource('prodi', ProdiController::class);
Route::apiResource('ruangan', RuanganController::class);
Route::apiResource('periode', PeriodeTahunController::class);
Route::apiResource('waktu', WaktuPerkuliahanController::class);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');