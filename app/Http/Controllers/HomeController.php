<?php

namespace App\Http\Controllers;

use App\Service\OAuth\OAuth;
use Illuminate\Http\Request;
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index()
    {
        try{
            return $this->packing(0,'success',OAuth::getInstance()->getRequestToken());
        }catch (\Exception $E){
            return $this->packing($E->getCode(),$E->getMessage(),null);
        }
    }

    /**
     * 登录回调
     */
    public function loginCallback(Request $request)
    {
        try{
            return $this->packing(0,'success',OAuth::getInstance()->loginCallback($request));
        }catch (\Exception $E){
            return $this->packing($E->getCode(),$E->getMessage(),null);
        }
    }
}
