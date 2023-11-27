<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Services\File\FileService;

class FileController extends ApiController
{
    public function __construct(FileService $file_service)
    {
        $this->file_service = $file_service;
    }

    /**
     * show function for a file
     *
     * @param  string  $filename a filename
     * @return Response
     *
     * @throws Exception
     **/
    public function show($dir, $filename)
    {
        try {
            $file = $this->file_service->getFileData($filename, $dir, false);
            if (empty($file['data'])) {
                return $this->errorResponse(trans('messages.data_not_found', ['data' => 'File']), 404);
            }

            return response($file['data'], 200)->header('Content-Type', $file['mime']);
        } catch (\Exception $e) {
            logger()->error($e);

            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }

    /**
     * download function for a file
     *
     * @param string $filename filename
     * @return Response
     * @throws Exception
     **/
    public function download($filename)
    {
        try{
            $file = $this->file_service->getFileData($filename, 'user_photos', false);

            if($file['data'] == ''){
                return $this->errorResponse(trans('messages.data_not_found', ['data' => 'File']), 404);
            }

            $headers = [
                'Content-Type' => $file['mime'],
                'Content-Disposition' => 'attachment; filename="' . basename($filename) . '"',
            ];
            return response($file['data'], 200, $headers);
        } catch (\Exception $e) {
            logger()->error($e);
            return $this->errorResponse(__('http.status_code_500'), 500);
        }

    }
}
