<?php

namespace App\Http\Controllers;

use App\Models\Edad;
use App\Models\Color;
use App\Models\Marca;
use App\Models\Talle;
use App\Models\Genero;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class APIAdminProductosController extends Controller {

  public function todos() {

    try {
      $productos = Producto::join('colores', 'productos.colores_id', '=', 'colores.id')
        ->join('talles', 'productos.talles_id', '=', 'talles.id')
        ->join('marcas', 'productos.marcas_id', '=', 'marcas.id')
        ->join('categorias', 'marcas.categorias_id', '=', 'categorias.id')
        ->join('generos', 'categorias.generos_id', '=', 'generos.id')
        ->join('edades', 'generos.edades_id', '=', 'edades.id')
        ->select('productos.id', 'productos.nombre', 'productos.imagen', 'productos.precio', 'productos.descuento', 'productos.descripcion', 'marcas.nombre as marcas_nombre', 'colores.nombre as colores_nombre', 'talles.nombre as talles_nombre', 'generos.nombre as generos_nombre', 'edades.nombre as edades_nombre', 'categorias.nombre as categorias_nombre')
        ->get();

      return response()->json(['resultado' => $productos]);
    } catch (\Throwable $th) {
      return response()->json($th->getMessage(), 500);
    }
  }

  public function datos_formulario() {

    try {
      $edades = Edad::select('id', 'nombre')->get();

      $generos = Genero::select('id', 'nombre', 'edades_id')->get();

      $categorias = Categoria::join('talle_tipos', 'categorias.talle_tipos_id', '=', 'talle_tipos.id')->select('categorias.id', 'categorias.nombre', 'categorias.generos_id', 'categorias.talle_tipos_id', 'talle_tipos.nombre as clase_talle')->get();

      $marcas = Marca::select('id', 'nombre', 'categorias_id')->get();

      $talles = Talle::select('id', 'nombre', 'talle_tipos_id')->get();

      $colores = Color::select('id', 'nombre', 'valor')->get();

      $datos = [
        'categorias' => $categorias,
        'marcas' => $marcas,
        'colores' => $colores,
        'talles' => $talles,
        'generos' => $generos,
        'edades' => $edades,
      ];

      return response()->json(['datos_form' => $datos]);
    } catch (\Throwable $th) {
      return response()->json(['error' => $th->getMessage()], 500);
    }
  }

  public function crear(Request $request) {

    try {

      $validado = $request->validate(
        [
          'nombre' => ['min:8', 'max:100', 'required', 'unique:productos,nombre', 'string'],
          'precio' => ['numeric', 'required', 'max_digits:10'],
          'descuento' => ['numeric', 'required', 'regex:/^\d{1,2}(\.\d{1,2})?$/', 'decimal:0,2'],
          'imagen' => ['array', 'required', 'min:1', 'max:3'],
          'imagen.*' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
          'marcas_id' => ['integer', 'required'],
          'talles_id' => ['integer', 'required'],
          'colores_id' => ['integer', 'required'],
          'descripcion' => ['required', 'string'],
        ],
        [
          'descuento.regex' => 'El campo numero debe ir desde 1 hasta 99.99',
          'imagen.required' => 'El campo imagen es obligatorio',
          'imagen.array' => 'El campo imagen es inválido',
          'imagen.*.image' => 'El archivo en :attribute debe ser una imagen',
          'imagen.*.mimes' => 'El archivo en :attribute debe ser de tipo JPEG o PNG',
          'imagen.*.max' => 'El archivo en :attribute no debe superar los 2048 KB',
          'edades_id.required' => 'El campo edad es obligatorio',
          'edades_id.integer' => 'El campo edad debe ser un numero',
          'generos_id.required' => 'El campo genero es obligatorio',
          'categorias_id.required' => 'El campo categoria es obligatorio',
          'marcas_id.required' => 'El campo marca es obligatorio',
          'talles_id.required' => 'El campo talle es obligatorio',
          'colores_id.required' => 'El campo color es obligatorio',
        ]

      );
      // --- IMAGENES --- //
      // --- Nombre de imagenes database --- //
      $nombre_imagenes = '';

      // --- Iterar imagenes cargadas --- //
      $imagenes = $request->file('imagen');

      foreach ($imagenes as $index => $imagen) {

        // --- Nombre imagen --- //
        $nombre_imagen = md5(uniqid(strval(rand()), true));

        // --- Nombre para DB --- //
        if($index + 1 === count($imagenes)) {
          $nombre_imagenes = $nombre_imagenes . $nombre_imagen;
        } else {
          $nombre_imagenes = $nombre_imagenes . $nombre_imagen . ';';
        }

        // --- Nombres guardados en DB --- //
        $nombre_png = $nombre_imagen . '.' . 'png';
        $nombre_webp = $nombre_imagen . '.' . 'webp';

        // --- Imagenes png y webp, y resize --- //
        $imagen_png = Image::make($imagen)->fit(800, 800)->encode('png', 80);
        $imagen_webp = Image::make($imagen)->fit(800, 800)->encode('webp', 80);

        // --- Almacenamiento de Servidor --- //
        $imagen_png->save(storage_path('app/public/imagenes/' . $nombre_png));
        $imagen_webp->save(storage_path('app/public/imagenes/' . $nombre_webp));
      }
      // --- Almacenar nombres de imagenes --- //
      $validado['imagen'] = $nombre_imagenes;

      Producto::create($validado);

      return response()->json(['resultado' => 'Producto Creado Exitosamente']);
    } catch (\Exception $exception) {
      $errors = [];

      if ($exception instanceof \Illuminate\Validation\ValidationException) {
        $errors = $exception->errors();

        $imagen_cargada = array_key_exists('imagen.0', $errors);

        if ($imagen_cargada) {
          foreach ($errors as $key => $value) {

            if (strpos($key, 'imagen.') !== false) {
              unset($errors[$key]);
              foreach ($value as $cadauno) {
                $errors['imagen'][] = $cadauno;
              }
            }
          };
        }
      } else if ($exception instanceof \Illuminate\Database\QueryException) {
        $errors[] = $exception->getMessage();
      } else {
        $errors[] = $exception->getMessage();
      }

      return response()->json(['errores' => $errors], 500);
    }
  }

  public function producto(Request $request, $id) {

    try {

      $producto = Producto::join('colores', 'productos.colores_id', '=', 'colores.id')
        ->join('talles', 'productos.talles_id', '=', 'talles.id')
        ->join('marcas', 'productos.marcas_id', '=', 'marcas.id')
        ->join('categorias', 'marcas.categorias_id', '=', 'categorias.id')
        ->join('generos', 'categorias.generos_id', '=', 'generos.id')
        ->join('edades', 'generos.edades_id', '=', 'edades.id')
        ->select(
          'productos.id',
          'productos.nombre',
          'productos.precio',
          'productos.descuento',
          'productos.imagen',
          'productos.descripcion',

          'edades.id as edades_id',
          'edades.nombre as edades_nombre',

          'generos.id as generos_id',
          'generos.nombre as generos_nombre',

          'categorias.id as categorias_id',
          'categorias.nombre as categorias_nombre',
          'categorias.talle_tipos_id as categorias_tipo_talles',
          'categorias.generos_id as generos_categoria_id',

          'marcas.id as marcas_id',
          'marcas.nombre as marcas_nombre',

          'talles.id as talles_id',
          'talles.nombre as talles_nombre',

          'colores.id as colores_id',
          'colores.nombre as colores_nombre',
          'colores.valor as colores_valor'
        )
        ->where('productos.id', $id)->first();

      $array_imagenes = explode(';', $producto->imagen);

      array_pop($array_imagenes);

      $producto->imagen = $array_imagenes;

      return response()->json(['registro' => $producto]);
    } catch (\Throwable $th) {
      return response()->json(['error' => $th->getMessage()]);
    }
  }

  public function editar(Request $request, $id) {

    try {
      $producto = Producto::findOrFail($id);

      $validado = $request->validate(
        [
          'nombre' => ['min:8', 'max:100', 'string'],
          'precio' => ['numeric', 'max_digits:10'],
          'descuento' => ['numeric', 'regex:/^\d{1,2}(\.\d{1,2})?$/'],
          'imagen' => ['array', 'min:1', 'max:3'],
          'edades_id' => ['integer', 'required'],
          'generos_id' => ['integer', 'required'],
          'categorias_id' => ['integer', 'required'],
          'marcas_id' => ['integer', 'required'],
          'talles_id' => ['integer', 'required'],
          'colores_id' => ['integer', 'required'],
          'descripcion' => ['required', 'string'],
        ],
        [
          'descuento.regex' => 'El campo numero debe ir desde 1 hasta 99.99',
          'imagen.required' => 'El campo imagen es obligatorio',
          'imagen.array' => 'El campo imagen es inválido',
          'edades_id.required' => 'El campo edad es obligatorio',
          'edades_id.integer' => 'El campo edad debe ser un numero',
          'generos_id.required' => 'El campo genero es obligatorio',
          'categorias_id.required' => 'El campo categoria es obligatorio',
          'marcas_id.required' => 'El campo marca es obligatorio',
          'talles_id.required' => 'El campo talle es obligatorio',
          'colores_id.required' => 'El campo color es obligatorio',
        ]
      );

      // --- Borrar imagenes previas --- //
      $imagenes_cargadas = $request->file('imagen');

      $imagenes_actuales = explode(';', $producto->imagen);

      array_pop($imagenes_actuales);

      // --- Borrar imagen previa --- //
      $nombre_imagenes = '';

      $para_borrar = []; // Array para guardar imagenes que se borraran si luego no existen errores de verificacion de imagen

      // --- VERIFICAR CUAL BORRAR Y CUAL NO --- //
      foreach ($imagenes_actuales as $imagen_actual) {

        $borrar = true;

        foreach ($imagenes_cargadas as $inx => $img_cargada) {

          $img_carg_nombre = $img_cargada->getClientOriginalName();

          if ($img_carg_nombre === $imagen_actual) {
            $borrar = false;

            unset($imagenes_cargadas[$inx]);

            $nombre_sin_ext = explode('.', $img_carg_nombre)[0];

            if (!strpos($nombre_imagenes, $nombre_sin_ext)) {
              $nombre_imagenes .= $nombre_sin_ext . ';';
            }
            break;
          }
        }

        if ($borrar) {
          $para_borrar[] = $imagen_actual;
        }
      }
      // --- Validacion de imagenes que si fueron cargadas --- //
      Validator::make(
        ['files', $imagenes_cargadas],
        [
          'files.*' => ['mimes:jpeg,png,jpg', 'max:2048']
        ],
        [
          'imagen.*.image' => 'El archivo en :attribute debe ser una imagen',
          'imagen.*.mimes' => 'El archivo en :attribute debe ser de tipo JPEG o PNG',
          'imagen.*.max' => 'El archivo en :attribute no debe superar los 2048 KB',
        ]
      );

      foreach ($para_borrar as $imagen) {
        Storage::disk('public')->delete("imagenes/$imagen.png");
        Storage::disk('public')->delete("imagenes/$imagen.webp");
      }

      // --- AGREGAR --- //
      foreach ($imagenes_cargadas as $imagen) {

        // --- Nombre imagen --- //
        $nombre_imagen = md5(uniqid(strval(rand()), true));

        // --- Nombre para DB --- //
        $nombre_imagenes .= $nombre_imagen . ';';

        // --- Nombres guardados en DB --- //
        $nombre_png = $nombre_imagen . '.' . 'png';
        $nombre_webp = $nombre_imagen . '.' . 'webp';

        // --- Imagenes png y webp, y resize --- //
        $imagen_png = Image::make($imagen)->fit(800, 800)->encode('png', 80);
        $imagen_webp = Image::make($imagen)->fit(800, 800)->encode('webp', 80);

        // --- Almacenamiento de Servidor --- //
        $imagen_png->save(storage_path('app/public/imagenes/' . $nombre_png));
        $imagen_webp->save(storage_path('app/public/imagenes/' . $nombre_webp));
      }
      // --- Almacenar nombres de imagenes --- //
      $validado['imagen'] = $nombre_imagenes;

      $producto->update($validado);

      return response()->json(['resultado' => 'Editado Correctamente']);

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
      $producto = Producto::findOrFail($id);

      $imagenes_actuales = explode(';', $producto->imagen);

      foreach ($imagenes_actuales as $imagen) {
        Storage::disk('public')->delete("imagenes/$imagen.png");
        Storage::disk('public')->delete("imagenes/$imagen.webp");
      }

      $producto->delete();

      return response()->json(['resultado' => 'Eliminado Correctamente']);

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
