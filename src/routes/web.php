<?php


use Illuminate\Support\Facades\Route;


use Modullo\ModulesAuth\Http\Controllers\ModulesAuthController;


Route::group(['namespace' => 'Modullo\ModulesAuth\Http\Controllers', 'middleware' => ['web']], static function() {

  Route::group(['prefix' => 'auth','middleware' => ['guest']],function() {
      Route::get('login','ModulesAuthController@showLoginForm')->name('login');
//      Route::get('register','ModulesAuthController@showRegisterForm')->name('register');
      Route::post('login','ModulesAuthController@login')->name('auth.login');
//      Route::post('register','ModulesAuthController@register')->name('auth.register');
  });

    Route::middleware('auth')->group(function () {
      Route::get('logout','ModulesAuthController@logout')->name('logout');
      Route::get('home','ModulesAuthController@index')->name('modullo.home');

    });

});


