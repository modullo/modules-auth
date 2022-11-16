<?php


use Illuminate\Support\Facades\Route;


use Modullo\ModulesAuth\Http\Controllers\ModulesAuthController;


Route::prefix('api/v1')->namespace('Modullo\ModulesAuth\Http\Controllers')->name('api.')->group(function (){
    Route::middleware('auth:sanctum')->group(function (){
        Route::post('auth/login','ModulesAuthController@loginForTokenUsers')->name('auth.loginToken');
    });
});
