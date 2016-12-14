<?php

/**
 *
 * Date: 16-12-12
 * Time: 上午12:37
 * author :李华 yehong0000@163.com
 */
namespace App\Library\Transform;
class Main
{
    protected static $Obj;
    private function __construct()
    {
    }

    /**
     * @return Main
     */
    public static function getInstance()
    {
        if(!self::$Obj){
            self::$Obj=new self;
        }
        return self::$Obj;
    }
    public function transform($content)
    {
        echo $content;
        die;
    }
}