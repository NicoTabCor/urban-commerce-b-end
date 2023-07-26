<?php

namespace App\Http\Controllers;

use Exception;
use Google_Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class APIAuthController extends Controller {

  public function login(Request $request) {

    try {
      // --- Validation of Login --- //
      $validado = $request->validate([
        'email' => ['required', 'email'],
        'password' => [
          'required',
          Password::min(8)
            ->uncompromised()
        ]
      ]);

      // --- User exists? --- //
      $user = User::where('email', $request->input('email'))->first();

      if ($user) {
        // --- Logging in --- //
        $logueado = Auth::attempt($validado);

        if ($logueado) {
          $request->session()->regenerate();
          return response()->json(['resultado' => $user->is_admin]);
        }
      }
      throw new Exception('Usuario o Contraseña Incorrecta', 500);

      // return response()->json($error->getMessage());

    } catch (Exception $e) {
      $errors = [];

      if ($e instanceof \Illuminate\Validation\ValidationException) {
        $errors = $e->errors();
      } else {
        $errors['password'][] = $e->getMessage();
      }
      return response()->json(['errores' => $errors], 500);
    }
  }

  public function logout(Request $request): JsonResponse {
    try {
      Auth::logout();

      $request->session()->invalidate();

      $request->session()->regenerateToken();

      return response()->json(['resultado' => true]);
    } catch (\Throwable $th) {

      return response()->json([
        'resultado' => false,
        'error' => $th->getMessage(),
      ]);
    }
  }

  public function registro(Request $request) {

    try {
      $validado = $request->validate([
        'name' => ['required', 'string', 'min:3', 'unique:users,name'],
        'last_name' => ['required', 'string', 'min:3', 'unique:users,last_name'],
        'email' => ['required', 'email', 'max:100', 'unique:users,email'],
        'password' => [
          'required',
          'confirmed',
          Password::min(8)
            ->uncompromised()
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
        ]
      ]);

      $validado['password'] = Hash::make($validado['password']);

      $user = User::create($validado);

      event(new Registered($user));

      $logueado = Auth::attempt([
        'email' => $request->input('email'),
        'password' => $request->input('password')
      ]);

      if ($logueado) {
        $request->session()->regenerate();

        return response()->json(['resultado' => 'registrado correctamente']);
      }

      return response()->json(['resultado' => 'error al tratar de loguear luego de registrar']);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json($e->errors());
    }
  }

  // --- Forgot Password --- //
  public function olvide(Request $request) {

    $validado = $request->validate([
      'email' => ['required', 'email']
    ]);
  }

  public function google_login(Request $request) {

    $id_token = $request->input('token');
    try {

      $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);

      $payload = $client->verifyIdToken($id_token);
      if ($payload) {
        // --- Datos --- //
        $email = $payload['email'];
        $google_id = $payload['sub'];
        $name = $payload['given_name'];
        $last_name = $payload['family_name'];

        $existe = User::where('email', $email)->first();

        // --- Ya existe email --- //
        if ($existe) {
          // --- Verificar que user se haya registrado su google_id --- //
          if ($existe->google_id == 'NULL') {
            $existe->google_id = $google_id;
            $existe->save();
          }

          $token = $existe->createToken('api');

          return response()->json(['token' => $token->plainTextToken]);
        } else {
          // --- Crear Nuevo Usuario sino existe --- //
          $user = User::create([
            'name' => $name,
            'last_name' => $last_name,
            'google_id' => $google_id,
            'email' => $email,
            'email_verified_at' => date('Y-m-d H:i:s')
          ]);

          $token = $user->createToken('api');

          return response()->json(['token' => $token->plainTextToken]);
        }
      }
    } catch (\Throwable $th) {
      return response()->json(['error' => $th->getMessage()], 500);
    }
  }

  // --- Verify Email --- //
  public function verificar_email(EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['resultado' => true]);
  }

  // --- Resend Email --- //
  public function reenviar_verificar(Request $request) {
    $user = Auth::user();
    // $request->user()->sendEmailVerificationNotification();

    return response()->json(['resultado' => $user]);
  }
}
