<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class APIAdminController extends Controller
{
  public function index(Request $request) {
    $user = Auth::user();

    return response()->json(['resultado' => $user]);
  }
}
