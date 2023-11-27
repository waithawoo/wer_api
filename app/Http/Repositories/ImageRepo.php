<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\Storage;
use App\Services\File\FileService;

class ImageRepo
{
    public function __construct(FileService $file_service = null)
    {
        $this->file_service = $file_service;
    }

    public function uploadImage($image, $filePath, $is_public = false)
    {
        $storedFilename = $this->file_service->store($image, 'base64', $filePath, $is_public);
        return $filePath.'/'.$storedFilename;
    }

    public function getImgPath($path)
    {
        $filename = basename($path);
        $directory_path = dirname($path);

        $fileUrl = request()->root()."/api/files/".$directory_path."/".$filename;
        return $fileUrl;
    }

    public function updateImg($image, $newPath, $oldPath = null, $is_public = false)
    {
        if ($uploaded_path = $this->uploadImage($image, $newPath, $is_public)) {
            if ($oldPath) {
                $this->deleteImg($oldPath, $is_public);
            }
            return $uploaded_path;
        }
        return false;
    }

    public function deleteImg($filepath, $is_public = false)
    {
        return $this->file_service->delete($filepath, '', $is_public);
    }
}
