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
// --- ZONA PRIVADA --- //
Route::middleware(['web', 'auth.session:sanctum', 'verified'])->group(function () {
  Route::post('/admin', [APIAdminController::class, 'index']);

  // --- PRODUCTOS --- //
  // --- Index --- //
  Route::get('/admin/productos/lista', [APIAdminProductosController::class, 'todos']);
  // --- Crear --- //
  Route::post('/admin/productos/crear', [APIAdminProductosController::class, 'crear']);
  Route::get('/admin/productos/datos-formulario', [APIAdminProductosController::class, 'datos_formulario']);
  // --- Editar --- //
  Route::get('/admin/productos/editar/{id}', [APIAdminProductosController::class, 'producto']);

  Route::post('/admin/productos/editar/{id}', [APIAdminProductosController::class, 'editar']);

  Route::delete('/admin/productos/eliminar/{id}', [APIAdminProductosController::class, 'eliminar']);

  // --- CATEGORIAS --- //
  // --- Index --- //
  Route::get('/admin/categorias/lista', [APIAdminCategoriasController::class, 'todos']);
  // --- Crear --- //
  Route::post('/admin/categorias/crear', [APIAdminCategoriasController::class, 'crear']);
  Route::get('/admin/categorias/datos-formulario', [APIAdminCategoriasController::class, 'datos_formulario']);
  // --- Editar --- //
  Route::get('/admin/categorias/editar/{id}', [APIAdminCategoriasController::class, 'categoria']);
  Route::post('/admin/categorias/editar/{id}', [APIAdminCategoriasController::class, 'editar']);

  Route::delete('/admin/categorias/eliminar/{id}', [APIAdminCategoriasController::class, 'eliminar']);
});

// --- AUTH --- //
// --- Registro --- //
Route::post('/registro', [APIAuthController::class, 'registro'])->middleware('web');

// --- Login --- //
Route::post('/login', [APIAuthController::class, 'login'])->middleware('web');

// --- Login google --- //
Route::post('/googlelogin', [APIAuthController::class, 'google_login'])->middleware('web');

// --- Olvidaste Contraseña --- //
Route::post('/olvide', [APIAuthController::class, 'login']);

// --- Verificar Email --- //
Route::get('/verificar/{id}/{hash}', [APIAuthController::class, 'verificar_email'])->middleware(['web', 'auth.session:sanctum', 'signed'])->name('verification.verify');

// --- Reenvio Verificar --- //
Route::post('/verificar-reenvio', [APIAuthController::class, 'reenviar_verificar'])->middleware(['web', 'auth.session:sanctum', 'throttle:6,1'])->name('verification.send');

// --- PRODUCTOS --- //
Route::get('/datos-menu', [APIProductosController::class, 'menu_datos'])->middleware('web');
