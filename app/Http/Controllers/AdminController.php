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
use App\Service\NoteSyn\Update;
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
        $this->middleware('checkLogin');
    }

    /**
     * 管理首页
     */
    public function index()
    {
        Update::getInstance()->updateNoteBook();
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

    /**
     * 登录授权
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login()
    {
        return OAuth::getInstance()->getRequestToken();
    }
    /**
     * 管理页
     */
    public function manage()
    {

    }
}