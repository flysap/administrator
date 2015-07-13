<?php

use Flysap\Administrator\Controllers\AdminController;

Route::group(['prefix' => 'admin'], function() {
    Route::get('/', ['uses' => AdminController::class .'@main']);
});