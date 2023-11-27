<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Repositories\UserRepo;
use App\Http\Repositories\LoginHistoryRepo;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\LoginHistory;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use Carbon\Carbon;

class AuthController extends ApiController
{
    private $user_repo;

    public function __construct(UserRepo $user_repo, LoginHistoryRepo $login_history_repo)
    {
        // $this->middleware('auth:api', ['except' => ['login']]);
        $this->user_repo = $user_repo;
        $this->login_history_repo = $login_history_repo;
    }

    /**
     * Login a user
     *
     * @param LoginRequest  $request
     * @return Response
     *
     * @throws Exception
     **/
    public function login(LoginRequest $request)
    {
        try {
            $validated_data = $request->validated();
            $user = $this->user_repo->whereFirst(['email' => $validated_data['email']]);

            if (! $user || ! Hash::check($validated_data['password'], $user->password)) {
                $this->login_history_repo->create([
                    'user_id' => null,
                    'email' => $validated_data['email'],
                    'status' => false,
                    'ip_address' => $request->ip(),
                ]);

                return $this->errorResponse(__('http.status_code_401'), 401);
            }

            $user->token = $this->user_repo->generateToken($user);
            $user->token_type = 'bearer';
            $user->expires_in = auth()->factory()->getTTL() * 60 . 'sec';

            $this->login_history_repo->create([
                'user_id' => $user->id,
                'email' => $user->email,
                'status' => true,
                'ip_address' => $request->ip(),
            ]);

            return $this->successResponse($user, 200,  __('auth.login_success'));
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) {
                throw $e;
            }
            logger()->error($e);

            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }

    /**
     * Logout the logged in user
     *
     * @return Response
     *
     * @throws Exception
     **/
    public function logout()
    {
        try{
            auth()->logout();
            return $this->successResponse(null, 200, __('auth.logout_success'));
        } catch (\Exception $e) {
            logger()->error($e);
            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }

    /**
     * Refresh the token
     *
     * @return Response
     *
     * @throws Exception
     **/
    public function refreshToken()
    {
        try{
            $token = $this->user_repo->refreshToken();

            return $this->successResponse($token, 200, __('auth.token_refresh_success'));
        } catch (\Exception $e) {
            logger()->error($e);
            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }

    /**
     * Change password
     *
     * @param ChangePasswordRequest $request
     * @return Response
     *
     * @throws Exception
     **/
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = auth()->user();
            $validated_data = $request->validated();

            if (! Hash::check($validated_data['current_password'], $user->password)) {
                return $this->errorResponse(__('auth.incorrect_password'), 422);
            }

            $new_password = Hash::make($validated_data['new_password']);
            $password_changed = $this->user_repo->fill($user->id, [
                'password' => $new_password,
            ]);

            if($password_changed){
                auth()->logout();
            }

            return $this->successResponse(null, 200, __('auth.change_password_success'));
        } catch (\Exception $e) {
            logger()->error($e);

            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }

    /**
     * Forgot password
     *
     * @param ForgotPasswordRequest $request
     * @return Response
     *
     * @throws Exception
     **/
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $validated = $request->validated();

        $user = $this->user_repo->whereFirst(['email' => $validated['email']]);

        if (! $user) {
            return $this->errorResponse(__('auth.email_not_registered'), 401);
        }

        $reset_password_token = Str::random(60);
        $user->reset_password_token = $reset_password_token;
        $user->reset_password_is_verified = 1;
        $user->save();

        $resetLink = 'http://example.com/reset-password/'.$reset_password_token; // route of frontend

        $details = [
            'name' => $user->name,
            'title' => 'Mail from WER',
            'body' => 'Click the following link to reset password.',
            'reset_link' => $resetLink
        ];

        try {
            Mail::to($validated['email'])->send(new ResetPassword($details));
        } catch (\Exception $e) {
            return $this->errorResponse(__('auth.email_provider_issue'), 500);
        }

        return $this->successResponse(null, 200, __('auth.password_reset_link_send_success'));
    }

    /**
     * Verify reset_password_token
     *
     * @return Response
     *
     * @throws Exception
     **/
    public function verifyResetPasswordToken($token)
    {
        try{
            $user = User::where('reset_password_token', $token)->where('reset_password_is_verified', 1)->first();

            if ($user) {
                $email = $user->email;
                return $this->successResponse(['email' => $email], 200, __('auth.token_is_valid'));
            }

            return $this->errorResponse(__('auth.token_is_invalid'), 401);
        } catch (\Exception $e) {
            logger()->error($e);

            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }

    /**
     * Reset password
     *
     * @param ResetPasswordRequest $request
     * @return Response
     *
     * @throws Exception
     **/
    public function resetPassword(ResetPasswordRequest $request)
    {
        try{
            $validated = $request->validated();

            $user = User::where('email', $validated['email'])->where('reset_password_token', $validated['token'])->where('reset_password_is_verified', 1)->first();

            if ($user) {
                $user->reset_password_is_verified = 0;
                $user->reset_password_token = '';
                $user->password = Hash::make($validated['password']);
                $user->updated_at = Carbon::now();
                $user->save();

                return $this->successResponse([], 200, __('auth.change_password_success'));
            }

            return $this->errorResponse(__('auth.token_is_invalid'), 401);
        } catch (\Exception $e) {
            logger()->error($e);

            return $this->errorResponse(__('http.status_code_500'), 500);
        }
    }
}
