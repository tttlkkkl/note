<?php

/**
 * Class Transform
 * 将有道笔记xml数据内容转化成可以被浏览器解析的html,并将笔记本内容调入书签
 *
 * @datetime: 2016/12/7 19:25
 * @author: lihs
 * @copyright: ec
 */

namespace App\Service\NoteSyn;

use App\Model\Note;
use App\Model\NoteBook;
use App\Model\Tag;
use App\Library\Transform\Main;
class Transform {
    protected static $Obj;
    private function __construct() {
    }

    /**
     * 获取实例
     * @return Transform
     */
    public static function getInstance(){
        if(!self::$Obj){
            self::$Obj=new self();
        }
        return self::$Obj;
    }

    /**
     * 批量的将笔记内容转化为标签
     */
    public function transformNoteBookToTag()
    {
        NoteBook::chunk(1, function ($noteBook) {
            $Tag=new Tag();
            foreach ($noteBook as $val) {

                $Tag::where('path','=', md5($val->path))->select('id')->first();
                $Tag->name=$val->name;
                if($Tag->id){
                    $up=$Tag->save();
                }else{
                    $up=Tag::create(['name'=>$val->name,'path'=>md5($val->path)]);
                }
                if($up){
                    SynLog::getInstance()->record('转换笔记本 '.$val->name.'到系统标签库,成功!',0,1);
                }else{
                    SynLog::getInstance()->record('转换笔记本 '.$val->name.'到系统标签库,失败!',0,1);
                }
            }
        });
    }

    /**
     * 转换一个笔记
     * @param $id
     */
    public function transformOneNote($id)
    {
        $id=$id?((int)$id):0;
        if(!$id){
            throw new \Exception('参数错误',40121);
        }
        $Note = Note::find($id);
        $content=Main::getInstance()->transform($Note->content);
    }
}