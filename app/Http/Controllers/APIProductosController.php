<?php

namespace App\Http\Controllers;

use App\Models\Edad;
use App\Models\Genero;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class APIProductosController extends Controller {

  public function menu_datos(Request $request) {
    $edades = Edad::get();
    $generos = Genero::get();
    $categorias = Categoria::get();

    $auth = Auth::check();

    return response()->json([
      'datosEdades' => $edades,
      'datosGeneros' => $generos,
      'datosCat' => $categorias,
      'auth' => $auth
    ]);
  }
}
