<?php
/**
 * Update
 *
 *
 * @version: 1.0
 * @datetime: 2016/11/24 19:23
 * @author: lihs
 * @copyright: ec
 */

namespace App\Service\NoteSyn;


use App\Service\OAuth\OAuth;
class Update
{
    protected static $Obj;
    private function __construct()
    {

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

    /**
     * 同步所有笔记本
     */
    public function updateNoteBook()
    {
        echo '<pre>';
        var_dump(OriginPull::getInstance(OAuth::getInstance()->getAccessToken())->getNoteBook());
    }

    /**
     * 同步笔记本下面的列表
     */
    public function updateNoteList($note)
    {

    }

    /**
     * 同步笔记
     * @param $path
     */
    public function updateNote($path)
    {

    }
}