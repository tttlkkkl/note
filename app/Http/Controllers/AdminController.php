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
use App\Service\NoteSyn\Transform;
use App\Library\Transform\Attachment\Image;
use App\Service\OAuth\OAuth;
use Illuminate\Http\Request;
use Log;

class AdminController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('checkLogin');
    }

    /**
     * 管理首页
     */
    public function index() {
        $image=Image::getInstance(OAuth::getInstance()->getAccessToken(),'');
        $image->pullFileFromRemote('WEBRESOURCE0289db2655a662bf9a84607f9b58fee2');
    }

    /**
     * 登录回调
     */
    public function loginCallback(Request $request) {
        try {
            OAuth::getInstance()->loginCallback($request);
            return redirect('admin');
        } catch (\Exception $E) {
            if (get_class($E) == 'Exception' || env('APP_DEBUG') === true) {
                $msg = $E->getMessage();
                Log::error("code:{$E->getCode()}\t msg:{$msg}");
            } else {
                $msg = 'failed';
                Log::notice("code:{$E->getCode()}\t msg:{$msg}");
            }
            return $this->packing($E->getCode(), $msg);
        }
    }

    /**
     * 登录授权
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login() {
        return OAuth::getInstance()->getRequestToken();
    }

    /**
     * 同步所有笔记本数据数据
     */
    public function updateNoteBook() {
        try {
            return $this->packing(0, 'ok', Update::getInstance()->updateNoteBook(false));
        } catch (\Exception $E) {
            if (get_class($E) == 'Exception' || env('APP_DEBUG') === true) {
                $msg = $E->getMessage();
                Log::error("code:{$E->getCode()}\t msg:{$msg}");
            } else {
                $msg = 'failed';
                Log::notice("code:{$E->getCode()}\t msg:{$msg}");
            }
            return $this->packing($E->getCode(), $msg);
        }
    }

    /**
     * 同步笔记本
     */
    public function updateNote() {
        try {
            return $this->packing(0, 'ok', Update::getInstance()->updateNote());
        } catch (\Exception $E) {
            if (get_class($E) == 'Exception' || env('APP_DEBUG') === true) {
                $msg = $E->getMessage();
                Log::error("code:{$E->getCode()}\t msg:{$msg}");
            } else {
                $msg = 'failed';
                Log::notice("code:{$E->getCode()}\t msg:{$msg}");
            }
            return $this->packing($E->getCode(), $msg);
        }
    }

    /**
     * 同步笔记本并同步所有笔记
     */
    public function updateAll() {
        try {
            return $this->packing(0, 'ok', Update::getInstance()->updateNoteBook(true));
        } catch (\Exception $E) {
            if (get_class($E) == 'Exception' || env('APP_DEBUG') === true) {
                $msg = $E->getMessage();
                Log::error("code:{$E->getCode()}\t msg:{$msg}");
            } else {
                $msg = 'failed';
                Log::notice("code:{$E->getCode()}\t msg:{$msg}");
            }
            return $this->packing($E->getCode(), $msg);
        }
    }

    /**
     * 笔记本内容转化为标签
     */
    public function transformToTag()
    {
        try {
            return $this->packing(0, 'ok', Transform::getInstance()->transformNoteBookToTag());
        } catch (\Exception $E) {
            if (get_class($E) == 'Exception' || env('APP_DEBUG') === true) {
                $msg = $E->getMessage();
                Log::error("code:{$E->getCode()}\t msg:{$msg}");
            } else {
                $msg = 'failed';
                Log::notice("code:{$E->getCode()}\t msg:{$msg}");
            }
            return $this->packing($E->getCode(), $msg);
        }
    }

    /**
     * 转换一个标签
     * @return string
     */
    public function transformOneNote(Request $request)
    {
        try {
            return $this->packing(0, 'ok', Transform::getInstance()->transformOneNote($request->input('id')));
        } catch (\Exception $E) {
            if (get_class($E) == 'Exception' || env('APP_DEBUG') === true) {
                $msg = $E->getMessage();
                Log::error("code:{$E->getCode()}\t msg:{$msg}");
            } else {
                $msg = 'failed';
                Log::notice("code:{$E->getCode()}\t msg:{$msg}");
            }
            return $this->packing($E->getCode(), $msg);
        }
    }

    /**
     * 1.0
     */
    public function auth(){

    }
}