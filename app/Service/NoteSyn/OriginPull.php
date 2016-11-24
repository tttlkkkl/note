<?php

/**
 * OriginPull
 * 从有道服务器拉取数据
 *
 * @version: 1.0
 * @datetime: 2016/11/24 16:02
 * @author: lihs
 * @copyright: ec
 */
namespace App\Service\NotePull;
use  Log;
class OriginPull
{
    protected $access_token;
    protected static $Obj;
    private function __construct($access_token)
    {
        $this->access_token=$access_token;
    }

    /**
     * 获取实例
     * @param $access_token
     * @return OriginPull
     */
    public static function getInstance($access_token)
    {
        if(!self::$Obj){
            self::$Obj=new self($access_token);
        }
        return self::$Obj;
    }

    /**
     * 获得笔记数据
     * @throws Exception
     */
    public function getNoteBook()
    {
        if(!session('access_token')){
            throw new \Exception('access_token 缺失，请重新授权！',5563);
        }
        $url=env('ORIGIN_URL').'/yws/open/notebook/all.json';
        $param=[
            'oauth_token'=>session('access_token')
        ];
        $header='';
        $result=json_decode(Curl::post($url,$param,$header),true);
        if($result && $result['error']){
            Log::error('获取笔记本列表失败:'.json_encode($result,JSON_UNESCAPED_UNICODE));
            return false;
        }
        return $result;
    }

    /**
     * 获得笔记列表
     * @param $note 笔记本路径
     * @throws Exception
     */
    public function getNoteList($note)
    {
        if(!session('access_token')){
            throw new \Exception('access_token 缺失，请重新授权！',5563);
        }
        $url=env('ORIGIN_URL').'/yws/open/notebook/list.json';
        $param=[
            'oauth_token'=>session('access_token'),
            'notebook'=>$note
        ];
        $header='';
        $result=json_decode(Curl::post($url,$param,$header),true);
        if($result && $result['error']){
            Log::error('获取笔记列表失败:'.json_encode($result,JSON_UNESCAPED_UNICODE));
            return false;
        }
        return $result;
    }

    /**
     * 获得一个笔记详细信息
     * @param $path
     * @throws Exception
     */
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
        $header='';
        $result=json_decode(Curl::post($url,$param,$header),true);
        if($result && $result['error']){
            Log::error('获取笔记内容失败:'.json_encode($result,JSON_UNESCAPED_UNICODE));
            return false;
        }
        return $result;
    }
}