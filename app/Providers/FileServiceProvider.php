<?php

namespace App\Providers;

use App\Services\File\AwsS3StorageService;
use App\Services\File\FileService;
use App\Services\File\LocalStorageService;
use Exception;
use Illuminate\Support\ServiceProvider;

class FileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FileService::class, function ($app) {
            if (config('services.filestorage') === 'local') {
                return new LocalStorageService();
            }
            if (config('services.filestorage') === 'awsS3') {
                return new AwsS3StorageService();
            }
            throw new Exception('The filestorage service is invalid.');
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
