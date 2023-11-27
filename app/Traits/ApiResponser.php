<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponser
{
    private $pagedJSON = [
        'response' => [
            'status' => '',
            'message' => '',
        ],
        'data' => [],
        'meta' => [],
    ];

    private $simpleJSON = [
        'response' => [
            'status' => '',
            'message' => '',
        ],
        'data' => [],
    ];

    /**
     * Building success response with normal data
     *
     * @param  int  $statusCode
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data = [], $statusCode = Response::HTTP_OK, $message = '')
    {
        $this->simpleJSON['response']['status'] = __("messages.success");
        $this->simpleJSON['response']['message'] = $message;
        $this->simpleJSON['data'] = $data;

        return response()->json($this->simpleJSON, $statusCode);
    }

    /**
     * Building success response with paginated data
     *
     * @param  int  $statusCode
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function paginatedSuccessResponse($data = [], $statusCode = Response::HTTP_OK, $message = '')
    {
        $this->pagedJSON['response']['status'] = __("messages.success");
        $this->pagedJSON['response']['message'] = $message;
        $this->pagedJSON['data'] = array_key_exists('data', $data) ? $data['data'] : $data;
        $this->pagedJSON['meta'] = array_key_exists('meta', $data) ? $data['meta'] : [];

        return response()->json($this->pagedJSON, $statusCode);
    }

    /**
     * Building error response
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, $statusCode)
    {
        $this->simpleJSON['response']['status'] = __("messages.error");
        $this->simpleJSON['response']['message'] = $message;

        return response()->json($this->simpleJSON, $statusCode);
    }
}
