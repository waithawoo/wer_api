<?php

namespace App\Services\File;

use Exception;
use Illuminate\Support\Facades\Storage;

class LocalStorageService implements FileService
{
    protected $storageType;

    public function __construct()
    {
        $this->storageType = 'public';
    }

    /**
     * Get the file data and mime type of a file
     *
     * @param string $filename the name of a file
     * @param string $dir the directory of a file
     * @return array ['data' => file data, 'mime' => mime type]
     * @throws Exception
     */
    public function getFileData(string $filename, string $dir = '', $is_public = true, $testing = false): array
    {
        try {
            if(!$is_public) $this->storageType = 'local';
            if($testing) $this->storageType = 'testing';

            $path = $this->getStoragePath($dir, $filename);

            if (Storage::disk($this->storageType)->exists($path)) {
                $file_data['data'] = Storage::disk($this->storageType)->get($path);
                $file_data['mime'] = Storage::disk($this->storageType)->mimeType($path);

                return $file_data;
            }
        } catch (Exception $e) {
            logger()->error($e);
        }

        return ['data' => '', 'mime' => ''];
    }

    /**
     * Store a file
     *
     * @param string $data file data
     * @param string $data_type file data type
     * @param string $dir the directory of a file
     * @return string|null the stored file name or null on failure
     * @throws Exception
     */
    public function store(string $data, string $data_type = 'base64', string $dir = '', $is_public = true, $testing = false)
    {
        try {
            if(!$is_public) $this->storageType = 'local';
            if($testing) $this->storageType = 'testing';

            if ($data_type == 'base64') {
                $filename = uniqid() . '.jpg';
                $prefixesToRemove = ['data:image/png;base64,', 'data:image/jpg;base64,', 'data:image/jpeg;base64,'];
                $cleanedBase64String = str_replace($prefixesToRemove, '', $data);
                $fileData = base64_decode($cleanedBase64String);
            } else {
                // Handle other data types
                $filename = uniqid() . '.' . $this->getFileExtension($data);
                $fileData = $data;
            }

            $path = $this->getStoragePath($dir, $filename);

            if (!Storage::disk($this->storageType)->exists($dir)) {
                Storage::disk($this->storageType)->makeDirectory($dir);
            }

            Storage::disk($this->storageType)->put($path, $fileData);
            return $filename;
        } catch (Exception $e) {
            logger()->error($e);
        }

        return null;
    }

    /**
     * Delete a file
     *
     * @param string $filename the name of a file
     * @param string $dir the directory of a file
     * @return bool true if the file is deleted successfully, false otherwise
     * @throws Exception
     */
    public function delete(string $filename, string $dir = '', $is_public = true, $testing = false)
    {
        try {
            if(!$is_public) $this->storageType = 'local';
            if($testing) $this->storageType = 'testing';

            $path = $this->getStoragePath($dir, $filename);

            if (Storage::disk($this->storageType)->exists($path)) {
                return Storage::disk($this->storageType)->delete($path);
            }
        } catch (Exception $e) {
            logger()->error($e);
        }

        return false;
    }

    /**
     * Get the full storage path for a file
     *
     * @param string $dir the directory of a file
     * @param string $filename the name of a file
     * @return string
     */
    public function getStoragePath(string $dir, string $filename): string
    {
        return ($dir ? $dir . DIRECTORY_SEPARATOR : '') . $filename;
    }

    /**
     * Get the file extension from the given file name or data.
     *
     * @param string $data
     * @return string
     */
    protected function getFileExtension(string $data): string
    {
        $extension = pathinfo($data, PATHINFO_EXTENSION);

        return $extension ?: 'bin';
    }
}
