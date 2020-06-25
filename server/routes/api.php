<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'fingerprint-header-required'], function () {
    Route::post('/login', 'Auth\LoginController@login');
    Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'Auth\ResetPasswordController@reset');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::group(['middleware' => 'verify-otp-at-login'], function () {
            Route::group(['middleware' => 'google2fa-is-not-activated'], function () {
                Route::post('/checkpoint', 'Auth\CheckpointController@check');
                Route::get('/checkpoint/resend', 'Auth\CheckpointController@resend');
            });

            Route::get('/checkpoint/google2fa/activate', 'Auth\CheckpointController@activateGoogle2fa')
                ->middleware('activate-google2fa');
        });

        Route::group(['middleware' => 'access-app'], function () {
            Route::post('/logout', 'Auth\LoginController@logout');
            Route::get('/dashboard', function (Request $request) {
                return $request->user();
            });
        });
    });
});
