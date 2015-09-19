<?php

namespace Flysap\Application\Controllers;

use App\Http\Controllers\Controller;

class AdminController extends Controller {

    public function main() {
        return view('themes::pages.main');
    }
}