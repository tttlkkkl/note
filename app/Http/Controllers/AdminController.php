<?php

/**
 * AdminController
 * 简单后台管理控制器
 *
 * @version: 1.0
 * @datetime: 2016/11/24 17:11
 * @author: lihs
 * @copyright: ec
 */
namespace App\Http\Controllers;
use App\Service\OAuth\OAuth;
use Illuminate\Http\Request;
class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function index()
    {
        return OAuth::getInstance()->getRequestToken();
    }

    /**
     * 登录回调
     */
    public function loginCallback(Request $request)
    {
        try{
            OAuth::getInstance()->loginCallback($request);
            return redirect('admin');
        }catch (\Exception $E){
            return $this->packing($E->getCode(),$E->getMessage(),null);
        }
    }
}