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
        try{
            return $this->packing(0,'ok',Update::getInstance()->updateNoteBook());
        }catch (\Exception $E){
            return $this->packing($E->getCode(),$E->getMessage());
        }
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
     * 同步所有笔记本数据数据
     */
    public function updateNoteBook()
    {
        try{
            return $this->packing(0,'ok',Update::getInstance()->updateNoteBook());
        }catch (\Exception $E){
            return $this->packing($E->getCode(),$E->getMessage());
        }
    }

    /**
     * 同步笔记本
     */
    public function updateNote()
    {
        try{
            return $this->packing(0,'ok',Update::getInstance()->updateNote());
        }catch (\Exception $E){
            return $this->packing($E->getCode(),$E->getMessage());
        }
    }
}