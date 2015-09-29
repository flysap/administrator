<?php

Route::group(['prefix' => 'admin', 'namespace' => 'Flysap\Application\Controllers'], function() {

    Route::get('login', 'AuthController@getLogin');
    Route::post('login', 'AuthController@postLogin');
    Route::get('logout', 'AuthController@getLogout');

    Route::get('register', 'AuthController@getRegister');
    Route::post('register', 'AuthController@postRegister');

});

Route::group(['prefix' => 'admin', 'namespace' => 'Flysap\Application\Controllers', 'middleware' => 'role:admin'], function() {
    Route::get('/', ['uses' => 'AdminController@main']);
});