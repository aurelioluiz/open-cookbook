<?php

namespace Tests\Unit;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeTest extends TestCase {
  use RefreshDatabase;

  public function test_user_relationship() {
    
    $user = User::factory()->create();
    $recipe = Recipe::factory()->create(['user_id' => $user->id]);

    $relatedUser = $recipe->user;

    $this->assertInstanceOf(User::class, $relatedUser);
    $this->assertEquals($user->id, $relatedUser->id);
  }
}