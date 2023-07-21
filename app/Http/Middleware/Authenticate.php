<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware {
  /**
   * Get the path the user should be redirected to when they are not authenticated.
   */
  protected function redirectTo(Request $request): ?string {
    return $request->expectsJson() ? null : route('login');
  }

  public function handle($request, Closure $next, ...$guards) {

    try {
      $autenticado = Auth::check();
      $user = Auth::user();

      if ($autenticado || !$user || $user->is_admin === 0) {
        throw new AuthenticationException('Acceso Denegado');
      }
    } catch (\Throwable $th) {
      return response()->json(['error' => $th->getMessage()]);
    }


    return $next($request);
  }
}
