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
        $result=json_decode(Curl::get($url,$param,$header),true);
        if(isset($result['access_token'])){
            session(['access_token'=>$result['access_token']]);
            return true;
        }else{
            throw new \Exception($result['massage']?:'授权失败',$result['error']?:4033);
        }
    }

    /**
     * 获取access_token
     * @param $code
     */
    public function getAccessToken()
    {
        if(session('access_token')){
            return session('access_token');
        }else{
            throw new \Exception('授权过期,请重新登录!',5656);
        }
    }

    /**
     * 获取用户信息
     * @param $access_token
     * @return mixed
     */
    public function getUserInfo($access_token)
    {
        $url=env('ORIGIN_URL').'/yws/open/user/get.json';
        $param=[
            'oauth_token'=>$access_token
        ];
        $result=json_decode(Curl::get($url,$param,$header),true);
        return $result;
    }
}