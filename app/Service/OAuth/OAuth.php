<?php

/**
 * OAuth
 *
 *
 * @version: 1.0
 * @datetime: 2016/11/9 15:47
 * @author: lihs
 * @copyright: ec
 */
namespace App\Service\OAuth;

use App\Library\Tool\Curl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use League\Flysystem\Exception;

class OAuth
{
    protected static $Obj;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (!self::$Obj) {
            self::$Obj = new self();
        }
        return self::$Obj;
    }

    /**
     * 登录跳转
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function getRequestToken()
    {
        if (!env('CONSUMER_KEY') || !env('CONSUMER_SECRET') || !env('ORIGIN_URL')) {
            throw new \Exception('请检查 CONSUMER_KEY,CONSUMER_SECRET,ORIGIN_URL 配置项！');
        }
        $url   = env('ORIGIN_URL') . '/oauth/authorize2';
        $param = [
            'client_id'     => env('CONSUMER_KEY'),
            'response_type' => 'code',
            'redirect_uri'=>env('APP_URL').'/loginCallback',
            'state'=>mt_rand(1,10000)
        ];
        session(['state'=>$param['state']]);
        $url.='?'.http_build_query($param,'&');
        return redirect($url);
    }

    /**
     * 登录回调
     * @param Request $request
     */
    public function loginCallback(Request $request)
    {
        if(session('state') != $request->input('state')){
            throw new \Exception('链接过期或不合法，请重试！',2360);
        }
        $code = $request->input('code');
        if(!$code){
            throw new \Exception('预授权失败，请重试!',-3657);
        }
        $url=env('ORIGIN_URL').'/oauth/access2';
        $param=[
            'client_id'=>env('CONSUMER_KEY'),
            'client_secret'=>env('CONSUMER_SECRET'),
            'grant_type'=>'authorization_code',
            'redirect_uri'=>env('APP_URL').'/loginCallback',
            'code'=>$code
        ];
        //$result=json_decode(Curl::get($url,$param,$header),true);
        if(isset($result['access_token'])){
            session(['access_token'=>$result['access_token']]);
        }
        $this->getUserInfo();
        $this->getNoteBook();
    }
    public function getAccessToken($code)
    {

    }
    private function getUserInfo()
    {
        if(!session('access_token')){
            throw new \Exception('access_token 缺失，请重新授权！',5563);
        }
        $url=env('ORIGIN_URL').'/yws/open/user/get.json';
        $param=[
            'oauth_token'=>session('access_token')
        ];
        $result=json_decode(Curl::get($url,$param,$header),true);
        echo '<pre>';
        print_r($result);
    }
    private function getNoteBook()
    {
        if(!session('access_token')){
            throw new \Exception('access_token 缺失，请重新授权！',5563);
        }
        $url=env('ORIGIN_URL').'/yws/open/notebook/all.json';
        $param=[
            'oauth_token'=>session('access_token')
        ];
        $result=json_decode(Curl::post($url,$param,$header),true);
        print_r($result);
        $this->getNoteList('/0979480BC1724560903DB37A62B4D8A1');
    }
    private function getNoteList($note)
    {
        if(!session('access_token')){
            throw new \Exception('access_token 缺失，请重新授权！',5563);
        }
        $url=env('ORIGIN_URL').'/yws/open/notebook/list.json';
        $param=[
            'oauth_token'=>session('access_token'),
            'notebook'=>$note
        ];
        $result=json_decode(Curl::post($url,$param,$header),true);
        print_r($result);
        foreach ($result as $k => $v){
            if(4!=$k){
                //continue;
            }
            $this->getNote($v);
        }
        die;
    }
    public function getNote($path)
    {
        if(!session('access_token')){
            throw new \Exception('access_token 缺失，请重新授权！',5563);
        }
        $url=env('ORIGIN_URL').'/yws/open/note/get.json';
        $param=[
            'oauth_token'=>session('access_token'),
            'path'=>$path
        ];
        $result=json_decode(Curl::post($url,$param,$header),true);
        //echo '<pre>';
        print_r($result);
        //header('Content-Type:application/x-www-form-urlencoded');
        //header('Content-Type:application/xml');
        //echo $header;
        echo $result['content'];
    }
}