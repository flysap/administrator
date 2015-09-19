<?php

use Flysap\Application\Controllers\AdminController;

Route::group(['prefix' => 'admin'], function() {
    Route::get('/', ['uses' => AdminController::class .'@main']);
});