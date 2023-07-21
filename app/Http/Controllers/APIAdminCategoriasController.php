<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Edad;
use App\Models\Genero;
use App\Models\TalleTipo;
use Illuminate\Http\Request;

class APIAdminCategoriasController extends Controller {

  public function todos() {

    try {
      $categorias = Categoria::join('talle_tipos', 'categorias.talle_tipos_id', '=', 'talle_tipos.id')
        ->join('generos', 'categorias.generos_id', '=', 'generos.id')
        ->join('edades', 'categorias.edades_id', '=', 'edades.id')
        ->select('categorias.id', 'categorias.nombre', 'generos.nombre as generos_nombre', 'edades.nombre as edades_nombre', 'talle_tipos.nombre as tipo_talle')->get();

      return response()->json(['resultado' => $categorias]);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), 500);
    }
  }

  public function datos_formulario() {
    try {
      $edades = Edad::select('id', 'nombre')->get();
      $generos = Genero::select('id', 'nombre', 'edades_id')->get();
      $talle_tipos = TalleTipo::select('id', 'nombre')->get();

      $datos = [
        'generos' => $generos,
        'edades' => $edades,
        'talle_tipos' => $talle_tipos
      ];

      return response()->json(['resultado' => $datos]);
    } catch (\Throwable $th) {
      return response()->json(['error' => $th->getMessage()], 500);
    }
  }

  public function crear(Request $request) {

    try {
      $validado = $request->validate(
        [
          'nombre' => ['min:8', 'max:100', 'required', 'string'],
          'edades_id' => ['integer', 'required'],
          'generos_id' => ['integer', 'required'],
          'talle_tipos_id' => ['integer', 'required']
        ],
        [
          // 'edades_id.required' => 'El campo edad es obligatorio',
          // 'edades_id.numeric' => 'El campo edad debe ser un numero',
          // 'generos_id.required' => 'El campo genero es obligatorio',
          // 'categorias_id.required' => 'El campo categoria es obligatorio',
          // 'marcas_id.required' => 'El campo marca es obligatorio',
          // 'talles_id.required' => 'El campo talle es obligatorio',
          // 'colores_id.required' => 'El campo color es obligatorio',
        ]

      );

      Categoria::create($validado);

      return response()->json(['resultado' => 'Categoria Creado Exitosamente']);
    } catch (\Exception $exception) {
      $errors = [];

      if ($exception instanceof \Illuminate\Validation\ValidationException) {
        $errors = $exception->errors();
      } else if ($exception instanceof \Illuminate\Database\QueryException) {
        $errors[] = $exception->getMessage();
      } else {
        $errors[] = $exception->getMessage();
      }

      return response()->json(['errores' => $errors], 500);
    }
  }

  public function categoria(Request $request, $id) {

    try {
      $categoria = Categoria::join('edades', 'categorias.edades_id', '=', 'edades.id')
        ->join('generos', 'categorias.generos_id', '=', 'generos.id')
        ->join('talle_tipos', 'categorias.talle_tipos_id', '=', 'talle_tipos.id')
        ->select('categorias.id', 'categorias.nombre', 'categorias.imagen', 'edades.id as edades_id', 'edades.nombre as edades_nombre', 'generos.id as generos_id', 'generos.nombre as generos_nombre', 'categorias.talle_tipos_id as talle_tipos_id', 'talle_tipos.nombre as talle_tipos_nombre')
        ->where('categorias.id', $id)->first();

      return response()->json(['resultado' => $categoria]);
    } catch (\Exception $exception) {
      $errors = [];

      if ($exception instanceof \Illuminate\Validation\ValidationException) {
        $errors = $exception->errors();
      } else if ($exception instanceof \Illuminate\Database\QueryException) {
        $errors[] = $exception->getMessage();
      } else {
        $errors[] = $exception->getMessage();
      }

      return response()->json(['errores' => $errors], 500);
    }
  }

  public function editar(Request $request, $id) {

    try {
      $categoria = Categoria::findOrFail($id);

      $validado = $request->validate([
        'nombre' => ['min:8', 'max:100', 'required', 'string'],
        'edades_id' => ['integer', 'required'],
        'generos_id' => ['integer', 'required'],
        'talle_tipos_id' => ['integer', 'required'],
      ]);

      $categoria->update($validado);

      return response()->json(['resultado' => $categoria]);
    } catch (\Exception $exception) {
      $errors = [];

      if ($exception instanceof \Illuminate\Validation\ValidationException) {
        $errors = $exception->errors();
      } else if ($exception instanceof \Illuminate\Database\QueryException) {
        $errors[] = $exception->getMessage();
      } else {
        $errors[] = $exception->getMessage();
      }

      return response()->json(['errores' => $errors], 500);
    }
  }

  public function eliminar(Request $request, $id) {

    try {
      $categoria = Categoria::findOrFail($id);

      $borrado = $categoria->delete();

      if ($borrado) {
        return response()->json(['resultado' => $borrado]);
      }

    } catch (\Exception $exception) {
      $errors = [];

      if ($exception instanceof \Illuminate\Validation\ValidationException) {
        $errors = $exception->errors();
      } else if ($exception instanceof \Illuminate\Database\QueryException) {
        $errors[] = $exception->getMessage();
      } else {
        $errors[] = $exception->getMessage();
      }

      return response()->json(['errores' => $errors], 500);
    }
  }
}
