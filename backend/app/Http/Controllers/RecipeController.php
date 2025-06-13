<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller {

  public function index(Request $request) {

    $user = auth()->user();
    $search = $request->query('search');

    $query = Recipe::where('user_id', $user->id);

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
      });
    }

    $recipes = $query->get();

    return response()->json($recipes);
  }

  public function show($id) {
    
    $user = auth()->user();
    $recipe = Recipe::where('id', $id)->where('user_id', $user->id)->first();

    if (!$recipe) {
      return response()->json(['error' => 'Receita não encontrada'], 404);
    }

    return response()->json($recipe);
  }

  public function store(Request $request) {

    $validator = Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'description' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()->first()], 422);
    }

    $user = auth()->user();

    $recipe = Recipe::create([
      'title' => $request->title,
      'description' => $request->description,
      'user_id' => $user->id,
    ]);

    return response()->json($recipe, 201);
  }

  public function update(Request $request, $id) {

    $user = auth()->user();
    $recipe = Recipe::where('id', $id)->where('user_id', $user->id)->first();

    if (!$recipe) {
      return response()->json(['error' => 'Receita não encontrada'], 404);
    }

    $validator = Validator::make($request->all(), [
      'title' => 'required|string|max:255',
      'description' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()->first()], 422);
    }

    $recipe->update([
      'title' => $request->title,
      'description' => $request->description,
    ]);

    return response()->json($recipe);
  }

  public function destroy($id) {

    $user = auth()->user();
    $recipe = Recipe::where('id', $id)->where('user_id', $user->id)->first();

    if (!$recipe) {
      return response()->json(['error' => 'Receita não encontrada'], 404);
    }

    $recipe->delete();

    return response()->json(null, 204);
  }
}