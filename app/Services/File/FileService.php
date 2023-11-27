<?php

namespace App\Services\File;

interface FileService
{
    public function getFileData(string $filename, string $dir = '', $is_public = true): array;

    public function store(string $data, string $data_type = 'base64', string $dir = '', $is_public = true);
}
