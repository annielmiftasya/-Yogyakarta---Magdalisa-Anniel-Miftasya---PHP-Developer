<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesaController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::get('desa', [DesaController::class, 'index']);
Route::get('desa/{id}', [DesaController::class, 'show']);
Route::post('desa', [DesaController::class, 'store']);
Route::put('desa/{id}', [DesaController::class, 'update']);
Route::delete('desa/{id}', [DesaController::class, 'destroy']);


Route::get('district', [DesaController::class, 'district']);