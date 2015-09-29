<?php

namespace Flysap\Application\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller {

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * @var string
     */
    protected $redirectPath = '/admin';

    /**
     * @var string
     */
    protected $loginPath = '/admin/login';


    /**
     * Create a new authentication controller instance.
     *
     */
    public function __construct() {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get login form .
     *
     * @return \Illuminate\View\View
     */
    public function getLogin() {
        if (view()->exists('themes::auth.login'))
            return view('themes::auth.login');

        if (view()->exists('auth.authenticate')) {
            return view('auth.authenticate');
        }

        return view('auth.login');
    }


}