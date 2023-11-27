<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() :void
    {
        parent::setUp();

        User::factory()->create([
            'email' => 'johndoe@example.org',
            'password' => Hash::make('testpassword'),
            'phone' => '09987654321',
            'photo' => 'user_photos/photo1.jpg'
        ]);
    }

    public function test_require_email_and_login()
    {
        $response = $this->json('POST', 'api/user/login', [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_login_successfully()
    {
        $user = ['email' => 'johndoe@example.org', 'password' => 'testpassword'];
        $response = $this->json('POST', 'api/user/login', $user);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'response' => [
                    'status',
                    'message',
                ],
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'photo',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                    'token',
                    'token_type',
                    'expires_in'
                ]
            ]);
    }

    public function test_logout_successfully()
    {
        $user = User::first();
        $token = JWTAuth::fromUser($user);

        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('GET', 'api/user/logout', [], $headers);
        $response->assertStatus(200)->assertJsonStructure([
            'response' => [
                'status',
                'message',
            ],
            'data' => []
        ]);
    }

    public function test_refresh_token_successfully()
    {
        $user = User::first();
        $token = JWTAuth::fromUser($user);

        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('GET', 'api/user/refresh-token', [], $headers);
        $response->assertStatus(200)->assertJsonStructure([
            'response' => [
                'status',
                'message',
            ],
            'data' => [
                'token',
                'token_type',
                'expires_in'
            ]
        ]);
    }

    public function test_change_password_successfully()
    {
        $user = User::first();
        $token = JWTAuth::fromUser($user);

        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('POST', 'api/user/change-password', [
            'current_password' => 'testpassword',
            'new_password' => 'newpassword',
            'new_password_confirmation' => 'newpassword'
        ], $headers);

        $response->assertStatus(200)->assertJsonStructure([
            'response' => [
                'status',
                'message',
            ],
            'data' => []
        ]);
    }

    public function test_forgot_password_request()
    {
        $user = User::first();
        $response = $this->json('POST', 'api/user/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'response' => [
                'status',
                'message',
            ],
            'data' => []
        ]);
    }

    public function test_forgot_password_request_with_invalid_email()
    {
        $response = $this->json('POST', 'api/user/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(401)->assertJsonStructure([
            'response' => [
                'status',
                'message',
            ],
            'data' => []
        ]);
    }

    public function test_verify_reset_password_token()
    {
        $user = User::first();
        $user->update([
            'reset_password_token' => 'test_token',
            'reset_password_is_verified' => 1,
        ]);

        $response = $this->json('GET', "api/user/verify-reset-password-token/{$user->reset_password_token}");

        $response->assertStatus(200)->assertJsonStructure([
            'response' => [
                'status',
                'message',
            ],
            'data' => [
                'email',
            ]
        ]);
    }

    public function test_verify_reset_password_token_with_invalid_token()
    {
        $response = $this->json('GET', 'api/user/verify-reset-password-token/invalid_token');

        $response->assertStatus(401)->assertJsonStructure([
            'response' => [
                'status',
                'message',
            ],
            'data' => []
        ]);
    }

    public function test_reset_password()
    {
        $user = User::first();
        $user->update([
            'reset_password_token' => 'valid_token',
            'reset_password_is_verified' => 1,
        ]);

        $response = $this->json('POST', 'api/user/reset-password', [
            'email' => $user->email,
            'token' => 'valid_token',
            'password' => 'new_password',
            'confirm_password' => 'new_password'
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'response' => [
                'status',
                'message',
            ],
            'data' => []
        ]);
    }

    public function test_reset_password_with_invalid_token()
    {
        $user = User::first();
        $user->update([
            'reset_password_token' => 'valid_token',
            'reset_password_is_verified' => 1,
        ]);

        $response = $this->json('POST', 'api/user/reset-password', [
            'email' => $user->email,
            'token' => 'different_token',
            'password' => 'new_password',
            'confirm_password' => 'new_password'
        ]);

        $response->assertStatus(401)->assertJsonStructure([
            'response' => [
                'status',
                'message',
            ],
            'data' => []
        ]);
    }
}
