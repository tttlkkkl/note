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

    public function getRequestToken()
    {
        if (!env('CONSUMER_KEY') || !env('CONSUMER_SECRET') || !env('ORIGIN_URL')) {
            throw new \Exception('请检查 CONSUMER_KEY,CONSUMER_SECRET,ORIGIN_URL 配置项！');
        }
        $url   = env('ORIGIN_URL') . '/oauth/authorize2';
        $param = [
            'client_id'     => env('CONSUMER_KEY'),
            'response_type' => 'code',
            'redirect_uri'=>urlencode(env('APP_URL').'/home/callback'),
            'state'=>mt_rand(1,1000)
        ];
        $url.='?'.http_build_query($param,'&');
        return redirect($url);
    }
}