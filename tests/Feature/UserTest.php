<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_successfully()
    {
        $register = [
            'name' => 'UserTest',
            'phone' => '09987654321',
            'photo' => 'data:image/jpeg;base64,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',
            'email' => 'user@test.com',
            'password' => 'testpass',
            'password_confirmation' => 'testpass'
        ];

        $response = $this->json('POST', 'api/user/register', $register);
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
                    'created_at',
                    'updated_at'
                ]
                ]);
        $this->assertDatabaseHas('users', [
                'name' => 'UserTest',
                'phone' => '09987654321',
                'email' => 'user@test.com',
            ]);

        $filenameFromResponse = $response->json('data.photo');
        $pathSegments = explode('/', $filenameFromResponse);

        $filename = array_pop($pathSegments);
        $directory = array_pop($pathSegments);

        $filePath = implode(DIRECTORY_SEPARATOR, [$directory, $filename]);
        Storage::disk('local')->delete($filePath);
    }

    public function test_require_name_phone_photo_email_and_password()
    {
        $response = $this->json('POST', '/api/user/register');

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'phone', 'photo', 'email', 'password']);
    }

    public function test_require_password_confirmation()
    {
        $register = [
            'name' => 'UserTest',
            'phone' => '09987654321',
            'photo' => 'data:image/jpeg;base64,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',
            'email' => 'user@test.com',
            'password' => 'testpass',
        ];

        $response = $this->json('POST', '/api/user/register', $register);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['password_confirmation']);
    }

    public function test_user_list_successfully()
    {
        $response = $this->json('GET', 'api/user/list');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'response' => [
                    'status',
                    'message',
                ],
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'photo',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'total',
                    'per_page',
                    'current_page',
                    'total_pages',
                ],
            ]);
    }

    public function test_update_successfully()
    {
        User::factory()->create([
            'name' => 'JohnDoe',
            'email' => 'johndoe@example.org',
            'password' => Hash::make('testpassword'),
            'phone' => '09987654321',
            'photo' => 'user_photos/photo1.jpg'
        ]);

        $updated_data = [
            'name' => 'UserTest New name',
        ];

        $user = User::first();
        $token = JWTAuth::fromUser($user);
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('POST', "api/user/update/$user->id", $updated_data, $headers);
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
                    'created_at',
                    'updated_at'
                ]
            ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'UserTest New name',
        ]);
    }

    public function test_delete_successfully()
    {
        User::factory()->create([
            'name' => 'JohnDoe',
            'email' => 'johndoe@example.org',
            'password' => Hash::make('testpassword'),
            'phone' => '09987654321',
            'photo' => 'user_photos/photo1.jpg'
        ]);

        $user = User::first();
        $token = JWTAuth::fromUser($user);
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('DELETE', "api/user/delete/$user->id", [], $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'response' => [
                    'status',
                    'message',
                ],
                'data' => []
            ]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_find_successfully()
    {
        User::factory()->create([
            'name' => 'JohnDoe',
            'email' => 'johndoe@example.org',
            'password' => Hash::make('testpassword'),
            'phone' => '09987654321',
            'photo' => 'user_photos/photo1.jpg'
        ]);

        $user = User::first();

        $response = $this->json('GET', "api/user/$user->id");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'response' => [
                    'status',
                    'message',
                ],
                'data' => []
            ]);
    }
}
