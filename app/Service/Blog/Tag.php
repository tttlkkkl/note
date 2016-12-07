<?php

/**
 * Tag
 * 标签处理
 *
 * @version: 1.0
 * @datetime: 2016/11/28 19:39
 * @author: lihs
 * @copyright: ec
 */
namespace App\Service\Blog;
use Illuminate\Support\Facades\DB;

class Tag
{
    protected $Model;
    protected static $Obj;
    private function __construct()
    {
        $this->Model=DB::table('b_tag');
    }

    /**
     * 获取实例
     */
    public static function getInstance()
    {
        if(!self::$Obj){
            self::$Obj=new self();
        }
        return self::$Obj;
    }

}