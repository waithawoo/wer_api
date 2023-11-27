<?php

namespace Tests\Unit;

use App\Services\File\FileService;
use App\Services\File\LocalStorageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected FileService $fileService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = new LocalStorageService();
        Storage::fake('testing');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Storage::disk('testing')->deleteDirectory('');
    }

    public function test_can_get_file_data()
    {
        $filename = 'example.jpg';
        $dir = 'example_directory';

        Storage::disk('testing')->put($dir . '/' . $filename, 'file content');

        $fileData = $this->fileService->getFileData($filename, $dir, false, true);
        $this->assertArrayHasKey('data', $fileData);
        $this->assertArrayHasKey('mime', $fileData);
    }

    public function test_can_store_a_file()
    {
        $data = base64_encode('example file content');
        $dataType = 'base64';
        $dir = 'example_directory';

        $filename = $this->fileService->store($data, $dataType, $dir, false, true);
        $this->assertNotNull($filename);

        $path = $this->fileService->getStoragePath($dir, $filename);
        $this->assertTrue(Storage::disk('testing')->exists($path));
    }

    public function test_can_delete_a_file()
    {
        $filename = 'example.jpg';
        $dir = 'example_directory';
        Storage::disk('testing')->put($dir . '/' . $filename, 'file content');

        $result = $this->fileService->delete($filename, $dir, false, true);
        $this->assertTrue($result);

        $path = $this->fileService->getStoragePath($dir, $filename);
        $this->assertFalse(Storage::disk('testing')->exists($path));
    }
}
