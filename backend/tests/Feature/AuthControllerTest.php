<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase {
  use RefreshDatabase;

  public function test_register_with_valid_data() {

    $response = $this->postJson('/api/register', [
      'name' => 'Test User',
      'email' => 'test@example.com',
      'password' => 'password123',
    ]);

    $response->assertStatus(201) ->assertJsonStructure([
      'token',
      'user' => ['id', 'name', 'email'],
    ]);

    $this->assertDatabaseHas('users', [
      'email' => 'test@example.com',
      'name' => 'Test User',
    ]);
  }

  public function test_register_with_duplicate_email() {

    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->postJson('/api/register', [
      'name' => 'Test User',
      'email' => 'test@example.com',
      'password' => 'password123',
    ]);

    $response->assertStatus(422)->assertJson(['error' => 'The email has already been taken.']);
  }

  public function test_register_with_invalid_data() {
    
    $response = $this->postJson('/api/register', [
      'name' => '',
      'email' => 'invalid-email',
      'password' => 'short',
    ]);

    $response->assertStatus(422) ->assertJsonStructure(['error']);
  }

  public function test_login_with_valid_credentials() {
    
    $user = User::factory()->create([
      'email' => 'test@example.com',
      'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
      'email' => 'test@example.com',
      'password' => 'password123',
    ]);

    $response->assertStatus(200)->assertJsonStructure([
      'token',
      'user' => ['id', 'name', 'email'],
    ]);
  }

  public function test_login_with_invalid_credentials() {
    
    $user = User::factory()->create([
      'email' => 'test@example.com',
      'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
      'email' => 'test@example.com',
      'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)->assertJson(['error' => 'Credenciais inválidas']);
  }

  public function test_login_with_invalid_data() {
    
    $response = $this->postJson('/api/login', [
      'email' => 'invalid-email',
      'password' => '',
    ]);

    $response->assertStatus(422)->assertJsonStructure(['error']);
  }
}