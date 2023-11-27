<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;

Route::middleware("localization")->group(function () {
    // ----- routes which need auth
    Route::middleware('auth:api')->group(function () {
        // auth apis
        Route::controller(AuthController::class)->prefix('user')->group(function () {
            Route::get('/logout', 'logout');
            Route::get('/refresh-token', 'refreshToken');
            Route::post('/change-password', 'changePassword');
        });

        // user apis
        Route::controller(UserController::class)->prefix('user')->group(function () {
            Route::post('/update/{id}', 'update');
            Route::delete('delete/{id}', 'delete');
        });

        // ----- file apis
        // show or read file like image without Content-Disposition header
        Route::get('files/{dir}/{filename}', [FileController::class, 'show'])->name('file')->where('filename', '.*');
        // download file with Content-Disposition header
        Route::get('download-file/{filename}', [FileController::class, 'download'])->name('download-file');
    });

    // ----- routes which need no auth
    // auth apis
    Route::controller(AuthController::class)->prefix('user')->group(function () {
        Route::post('/login', 'login');
        Route::post('/forgot-password', 'forgotPassword');
        Route::get('/verify-reset-password-token/{token}', 'verifyResetPasswordToken');
        Route::post('/reset-password', 'resetPassword');
    });
    // user apis
    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('/list', 'index');
        Route::post('/register', 'create');
        Route::get('{id}', 'findOrFail');
    });
});
