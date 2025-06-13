<?php

namespace Tests\Unit;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase {
  use RefreshDatabase;

  public function test_get_jwt_identifier() {

    $user = User::factory()->create(['id' => 1]);

    $this->assertEquals(1, $user->getJWTIdentifier());
  }

  public function test_get_jwt_custom_claims() {

    $user = User::factory()->create();

    $this->assertEquals([], $user->getJWTCustomClaims());
  }

  public function test_recipes_relationship() {

    $user = User::factory()->create();
    Recipe::factory()->count(2)->create(['user_id' => $user->id]);

    $recipes = $user->recipes;

    $this->assertCount(2, $recipes);
    $this->assertInstanceOf(Recipe::class, $recipes->first());
  }
}