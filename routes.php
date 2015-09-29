<?php

Route::group(['prefix' => 'admin', 'namespace' => 'Flysap\Application\Controllers'], function() {

    Route::get('login', ['as' => 'login', 'uses' => 'AuthController@getLogin']);
    Route::post('login', ['as' => 'post_login', 'uses' => 'AuthController@postLogin']);
    Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@getLogout']);

});

Route::group(['prefix' => 'admin', 'namespace' => 'Flysap\Application\Controllers', 'middleware' => 'role:admin'], function() {

    Route::get('/', ['as' => 'home', 'uses' => 'AdminController@main']);


    /**
     * That controller will show all settings from file config files which are registered to global
     *  config repository and merge with database config as database priority .. All the changes will be stored in database .
     *
     */
    Route::get('settings', ['as' => 'settings', 'uses' => 'SettingsController@index']);
    Route::get('settings/{section}', ['as' => 'edit_setting','uses' => 'SettingsController@edit']);
    Route::post('settings/{section}', ['as' => 'update_setting', 'uses' => 'SettingsController@update']);
    Route::get('settings/delete/{section}', ['as' => 'delete_setting', 'uses' => 'SettingsController@delete']);
});