<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIAuthController;
use App\Http\Controllers\APIAdminController;
use App\Http\Controllers\APIProductosController;
use App\Http\Controllers\APIAdminProductosController;
use App\Http\Controllers\APIAdminCategoriasController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// --- PRIVATE AREA --- //
Route::middleware(['web', 'auth.session:sanctum', 'verified'])->group(function () {
  Route::post('/admin', [APIAdminController::class, 'index']);

  // --- PRODUCTS --- //
  // --- Index --- //
  Route::get('/admin/productos/lista', [APIAdminProductosController::class, 'todos']);

  // --- Create --- //
  Route::post('/admin/productos/crear', [APIAdminProductosController::class, 'crear']);
  Route::get('/admin/productos/datos-formulario', [APIAdminProductosController::class, 'datos_formulario']);

  // --- Update --- //
  Route::get('/admin/productos/editar/{id}', [APIAdminProductosController::class, 'producto']);
  Route::post('/admin/productos/editar/{id}', [APIAdminProductosController::class, 'editar']);

  // --- Delete --- //
  Route::delete('/admin/productos/eliminar/{id}', [APIAdminProductosController::class, 'eliminar']);

  // --- CATEGORIES --- //
  // --- Index --- //
  Route::get('/admin/categorias/lista', [APIAdminCategoriasController::class, 'todos']);

  // --- Create --- //
  Route::post('/admin/categorias/crear', [APIAdminCategoriasController::class, 'crear']);
  Route::get('/admin/categorias/datos-formulario', [APIAdminCategoriasController::class, 'datos_formulario']);

  // --- Update --- //
  Route::get('/admin/categorias/editar/{id}', [APIAdminCategoriasController::class, 'categoria']);
  Route::post('/admin/categorias/editar/{id}', [APIAdminCategoriasController::class, 'editar']);

  // --- Delete --- //
  Route::delete('/admin/categorias/eliminar/{id}', [APIAdminCategoriasController::class, 'eliminar']);
});

// --- PUBLIC AREA --- //

// --- AUTH --- //
// --- Register --- //
Route::post('/registro', [APIAuthController::class, 'registro'])->middleware('web');

// --- Login --- //
Route::post('/login', [APIAuthController::class, 'login'])->middleware('web');

// --- Login google --- //
Route::post('/googlelogin', [APIAuthController::class, 'google_login'])->middleware('api');

// --- Logout --- //
Route::get('/logout', [APIAuthController::class, 'logout'])->middleware('web');

// --- Forgot Password --- //
Route::post('/olvide', [APIAuthController::class, 'login']);

// --- Email Verification --- //
Route::get('/verificar/{id}/{hash}', [APIAuthController::class, 'verificar_email'])->middleware(['web', 'auth.session:sanctum', 'signed'])->name('verification.verify');

// --- Resend Verification --- //
Route::post('/verificar-reenvio', [APIAuthController::class, 'reenviar_verificar'])->middleware(['web', 'auth.session:sanctum', 'throttle:6,1'])->name('verification.send');

// --- All Products --- //
Route::get('/datos-menu', [APIProductosController::class, 'menu_datos'])->middleware('web');
