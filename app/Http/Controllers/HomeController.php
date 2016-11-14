<?php

namespace App\Http\Controllers;

use App\Service\OAuth\OAuth;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return OAuth::getInstance()->getRequestToken();
    }
    public function callback()
    {
        print_r($_POST);
    }
}
