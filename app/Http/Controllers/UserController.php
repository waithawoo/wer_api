<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\ListingRequest;
use App\Http\Repositories\UserRepo;

class UserController extends ApiController
{
    protected $user_repo;

    public function __construct(UserRepo $user_repo)
    {
        $this->user_repo = $user_repo;
    }

    /**
     * Retrieve user lists
     *
     * @param ListingRequest  $request
     * @return Response
     *
     * @throws Exception
     **/
    public function index(ListingRequest $request)
    {
        try {
            $validated = $request->validated();
            $per_page = array_key_exists('per_page', $validated) ? $validated['per_page'] : 20;
            $page = array_key_exists('page', $validated) ? $validated['page'] : 1;
            $res_data = $this->user_repo->getDataWithPigination($per_page, $page);
            return $this->paginatedSuccessResponse($res_data, 200, trans('messages.data_list', ['data' => 'User']));
        } catch (\Exception $e) {
            logger()->error($e);
            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }

    /**
     * Retrieve a single user
     *
     * @param int $id
     * @return Response
     *
     * @throws Exception
     **/
    public function findOrFail($id)
    {
        try {
            if (! is_numeric($id)) {
                return $this->errorResponse(__('messages.id_must_be_integer'), 422);
            }
            $user = $this->user_repo->find($id);
            if ($user) {
                return $this->successResponse($user, 200, 'user');
            } else {
                return $this->errorResponse(trans('messages.data_not_found', ['data' => 'User']), 404);
            }
        } catch (\Exception $e) {
            logger()->error($e);
            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }

    /**
     * Register/Create a user
     *
     * @param CreateRequest  $request
     * @return Response
     *
     * @throws Exception
     **/
    public function create(CreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $result = $this->user_repo->create($validated);
            return $this->successResponse($result, 200, trans('messages.create_success', ['data' => 'User']));
        } catch (\Exception $e) {
            logger()->error($e);
            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }

    /**
     * Update the user
     *
     * @param UpdateRequest $request
     * @return Response
     *
     * @throws Exception
     **/
    public function update(UpdateRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $user = $this->user_repo->find($id);
            if ($user) {
                $result = $this->user_repo->update($id, $validated);
                return $this->successResponse($result, 200, trans('messages.update_success', ['data' => 'User']));
            } else {
                return $this->errorResponse(trans('messages.data_not_found', ['data' => 'User']), 404);
            }
        } catch (\Exception $e) {
            logger()->error($e);
            return $this->errorResponse(__('http.status_code_500'), 500);
        }

    }

    /**
     * Delete a user
     *
     * @param int $id
     * @return Response
     *
     * @throws Exception
     **/
    public function delete($id)
    {
        try {
            if (! is_numeric($id)) {
                return $this->errorResponse(__('messages.id_must_be_integer'), 422);
            }
            $user = $this->user_repo->find($id);
            if ($user) {
                if ($this->user_repo->delete($id)) {
                    return $this->successResponse(null, 200, trans('messages.delete_success', ['data' => 'User']));
                }
            } else {
                return $this->errorResponse(trans('messages.data_not_found', ['data' => 'User']), 404);
            }
        } catch (\Exception $e) {
            logger()->error($e);
            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }
}
