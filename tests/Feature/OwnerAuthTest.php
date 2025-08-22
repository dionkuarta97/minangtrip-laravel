<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Users;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class OwnerAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_login_with_valid_credentials()
    {
        // Create an owner user
        $owner = Users::create([
            'username' => 'testowner',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'change_password_at' => now(),
        ]);

        $response = $this->postJson('/api/owner/login', [
            'username' => 'testowner',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'token',
                        'user' => [
                            'id',
                            'user_id',
                            'username',
                            'role'
                        ],
                        'expires_at'
                    ]
                ]);

        $this->assertTrue($response->json('success'));
    }

    public function test_owner_cannot_login_with_invalid_role()
    {
        // Create a non-owner user
        $user = Users::create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'change_password_at' => now(),
        ]);

        $response = $this->postJson('/api/owner/login', [
            'username' => 'testuser',
            'password' => 'password123'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Akses ditolak. Role tidak sesuai'
                ]);
    }

    public function test_owner_cannot_login_with_wrong_password()
    {
        // Create an owner user
        $owner = Users::create([
            'username' => 'testowner',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'change_password_at' => now(),
        ]);

        $response = $this->postJson('/api/owner/login', [
            'username' => 'testowner',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Password salah'
                ]);
    }

    public function test_owner_cannot_login_with_nonexistent_username()
    {
        $response = $this->postJson('/api/owner/login', [
            'username' => 'nonexistent',
            'password' => 'password123'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Username tidak ditemukan'
                ]);
    }

    public function test_owner_login_validation_errors()
    {
        $response = $this->postJson('/api/owner/login', [
            'username' => '',
            'password' => ''
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['username', 'password']);
    }
}