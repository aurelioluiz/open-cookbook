<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller {

  public function register(Request $request) {

    $validator = Validator::make($request->all(), [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()->first()], 422);
    }

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    $token = JWTAuth::fromUser($user);

    return response()->json([
      'token' => $token,
      'user' => $user,
    ], 201);
  }

  public function login(Request $request) {

    $validator = Validator::make($request->all(), [
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()->first()], 422);
    }

    $credentials = $request->only('email', 'password');

    if (!$token = JWTAuth::attempt($credentials)) {
      return response()->json(['error' => 'Credenciais inválidas'], 401);
    }

    $user = auth()->user();

    return response()->json([
      'token' => $token,
      'user' => $user,
    ]);
  }
}