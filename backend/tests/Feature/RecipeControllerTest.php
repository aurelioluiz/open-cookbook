<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RecipeControllerTest extends TestCase {
  use RefreshDatabase;

  protected $user;
  protected $token;

  protected function setUp(): void {

    parent::setUp();
    $this->user = User::factory()->create();
    $this->token = JWTAuth::fromUser($this->user);
  }

  public function test_list_recipes() {
    
    Recipe::factory()->count(3)->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->getJson('/api/recipes');

    $response->assertStatus(200)->assertJsonCount(3);
  }

  public function test_list_recipes_with_search() {

    Recipe::factory()->create([
      'user_id' => $this->user->id,
      'title' => 'Bolo de Chocolate',
      'description' => 'Delicioso bolo com cobertura de chocolate',
    ]);
    Recipe::factory()->create([
      'user_id' => $this->user->id,
      'title' => 'Salada',
      'description' => 'Salada fresca',
    ]);

    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->getJson('/api/recipes?search=chocolate');

    $response->assertStatus(200)->assertJsonCount(1)->assertJsonFragment(['title' => 'Bolo de Chocolate']);
  }

  public function test_show_recipe() {

    $recipe = Recipe::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->getJson("/api/recipes/{$recipe->id}");

    $response->assertStatus(200)->assertJsonFragment(['title' => $recipe->title]);
  }

  public function test_show_nonexistent_recipe() {
        
    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->getJson('/api/recipes/999');

    $response->assertStatus(404)->assertJson(['error' => 'Receita não encontrada']);
  }

  public function test_show_recipe_from_another_user() {

    $otherUser = User::factory()->create();
    $recipe = Recipe::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->getJson("/api/recipes/{$recipe->id}");

    $response->assertStatus(404)->assertJson(['error' => 'Receita não encontrada']);
  }

  public function test_create_recipe_with_valid_data() {
  
    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->postJson('/api/recipes', [
        'title' => 'Bolo de Cenoura',
        'description' => 'Bolo fofinho com cobertura de chocolate',
      ]);

    $response->assertStatus(201)->assertJsonFragment(['title' => 'Bolo de Cenoura']);

    $this->assertDatabaseHas('recipes', [
      'title' => 'Bolo de Cenoura',
      'user_id' => $this->user->id,
    ]);
  }

  public function test_create_recipe_with_invalid_data() {
        
    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->postJson('/api/recipes', [
        'title' => '',
        'description' => '',
      ]);

    $response->assertStatus(422)->assertJsonStructure(['error']);
  }

  public function test_update_recipe_with_valid_data() {
        
    $recipe = Recipe::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])                  
      ->putJson("/api/recipes/{$recipe->id}", [
        'title' => 'Bolo de Laranja',
        'description' => 'Bolo cítrico e refrescante',
      ]);

    $response->assertStatus(200)->assertJsonFragment(['title' => 'Bolo de Laranja']);

    $this->assertDatabaseHas('recipes', [
      'id' => $recipe->id,
      'title' => 'Bolo de Laranja',
    ]);
  }

  public function test_update_recipe_with_invalid_data() {
    
    $recipe = Recipe::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->putJson("/api/recipes/{$recipe->id}", [
        'title' => '',
        'description' => '',
      ]);

    $response->assertStatus(422)->assertJsonStructure(['error']);
  }

  public function test_update_nonexistent_recipe() {
    
    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->putJson('/api/recipes/999', [
        'title' => 'Bolo de Laranja',
        'description' => 'Bolo cítrico',
      ]);

    $response->assertStatus(404)->assertJson(['error' => 'Receita não encontrada']);
  }

  public function test_update_recipe_from_another_user() {

    $otherUser = User::factory()->create();
    $recipe = Recipe::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->putJson("/api/recipes/{$recipe->id}", [
        'title' => 'Bolo de Laranja',
        'description' => 'Bolo cítrico',
      ]);

    $response->assertStatus(404)->assertJson(['error' => 'Receita não encontrada']);
  }

  public function test_delete_recipe() {

    $recipe = Recipe::factory()->create(['user_id' => $this->user->id]);

    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->deleteJson("/api/recipes/{$recipe->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
  }

  public function test_delete_nonexistent_recipe() {
    
    $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
      ->deleteJson('/api/recipes/999');

    $response->assertStatus(404)->assertJson(['error' => 'Receita não encontrada']);
  }

  public function test_access_recipes_without_authentication() {

    $response = $this->getJson('/api/recipes');

    $response->assertStatus(401)->assertJson(['message' => 'Unauthenticated.']);
  }
}