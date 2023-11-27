<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class FileTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        User::factory()->create([
            'email' => 'johndoe@example.org',
            'password' => Hash::make('testpassword'),
            'phone' => '09987654321',
            'photo' => 'user_photos/avatar.jpg'
        ]);
    }

    public function test_show_file()
    {
        $dir = 'user_photos';
        $filename = 'avatar.jpg';

        $file = UploadedFile::fake()->image('/user_photos/avatar.jpg', 400, 300)->size(100);
        Storage::disk('local')->put($file->name, $file->tempFile);

        $user = User::first();
        $token = JWTAuth::fromUser($user);
        $headers = ['Authorization' => "Bearer $token"];
        $response = $this->json('GET', "api/files/{$dir}/{$filename}", [], $headers);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', $file->getMimeType());

        Storage::disk('local')->delete($file->name);
    }

    public function test_download_file()
    {
        $dir = 'user_photos';
        $filename = 'avatar.jpg';

        $file = UploadedFile::fake()->image('/user_photos/avatar.jpg', 400, 300)->size(100);
        Storage::disk('local')->put($file->name, $file->tempFile);

        $user = User::first();
        $token = JWTAuth::fromUser($user);
        $headers = ['Authorization' => "Bearer $token"];

        $response = $this->json('GET', "api/download-file/{$filename}", [], $headers);

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="'.$filename.'"');

        Storage::disk('local')->delete($file->name);
    }
}
