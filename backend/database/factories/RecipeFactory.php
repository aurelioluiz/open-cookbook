<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeFactory extends Factory {

  protected $model = Recipe::class;

  public function definition() {

    return [
      'title' => fake()->sentence(3),
      'description' => fake()->paragraph(),
      'user_id' => User::factory(),
    ];
  }
}