<?php

use Flysap\Administrator\Controllers\AdminController;

Route::group(['namespace' => 'admin'], function() {

    Route::get('main', ['uses' => AdminController::class .'@main']);
});