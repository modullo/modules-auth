<?php


use Illuminate\Support\Facades\Route;




Route::group(['namespace' => 'Modullo\ModulesAuth\Http\Controllers', 'middleware' => ['web'], 'prefix' => 'auth'], static function() {
  Route::middleware('guest')->group(function () {
    Route::get('','ModulesAuthController@showLoginForm')->name('login');
    Route::get('register','ModulesAuthController@showRegisterForm')->name('register');
    Route::post('','ModulesAuthController@login')->name('auth.login');

  });
});


Route::group(['namespace' => 'Modullo\ModulesAuth\Http\Controllers', 'middleware' => ['guest']], static function() {
  Route::get('home','ModulesAuthController@index')->name('home');
});