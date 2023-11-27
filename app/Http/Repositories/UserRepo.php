<?php

namespace App\Http\Repositories;

use App\Http\Repositories\BaseRepo;
use App\Http\Repositories\ImageRepo;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserRepo extends BaseRepo
{
    protected $image_repo;
    protected $img_path;

    public function __construct(User $model, ImageRepo $image_repo)
    {
        parent::__construct($model);
        $this->image_repo = $image_repo;
        $this->img_path = config('const.user_photo_path');
    }

    public function generateToken(User $user)
    {
        return JWTAuth::fromUser($user);
    }

    public function refreshToken()
    {
        return [
            'token' => auth()->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 . "sec"
        ];
    }

    public function create($request)
    {
        if (isset($request["photo"])) {
            $uploaded_path = $this->image_repo->uploadImage($request['photo'], $this->img_path);
            if ($uploaded_path) $request['photo'] = $uploaded_path;
        }
        $result = parent::create($request);
        $password = Hash::make($request['password']);
        $password_created = parent::fill($result->id, [
            'password' => $password,
        ]);

        return $result;
    }

    public function update($id, $request)
    {
        $result = parent::find($id);

        if (isset($request["photo"])) {
            $uploaded_path = $this->image_repo->updateImg($request['photo'], $this->img_path, $result['photo']);
            if ($uploaded_path) $request['photo'] = $uploaded_path;
        }

        $result = parent::update($id, $request);

        return $result;
    }

    public function delete($id)
    {
        $result = parent::find($id);

        $deleted = parent::delete($id);

        if($deleted) $this->image_repo->deleteImg($result['photo']);

        return $result;
    }
}
