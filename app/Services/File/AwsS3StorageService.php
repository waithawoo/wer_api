<?php

namespace App\Services\File;

class AwsS3StorageService implements FileService
{
    public function getFileData(string $filename, string $dir = ''): array
    {
        $file_data = ['data' => '', 'mime' => ''];
        // ....continue custom logic here

        return $file_data;
    }

    public function store(string $data, string $data_type = 'base64', string $dir = '', $is_public = true)
    {
        $filename = '';
        // ....continue custom logic here

        return $filename;
    }
}
