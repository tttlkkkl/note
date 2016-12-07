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
use Illuminate\Support\Facades\DB;
use App\Library\Tool\Common;

class Update
{
    protected static $Obj;

    private function __construct()
    {
        //Common::switchRunningState();
    }

    /**
     * 获取实例
     */
    public static function getInstance()
    {
        if (!self::$Obj) {
            self::$Obj = new self();
        }
        return self::$Obj;
    }

    /**
     * 同步所有笔记本
     * @param $data
     * @return bool|mixed
     */
    public function updateNoteBook($synNote=true)
    {
        SynLog::getInstance()->record('开始同步笔记数据',0);
        $data   = OriginPull::getInstance(OAuth::getInstance()->getAccessToken())->getNoteBook();
        $result = $noteSynResult = [
            'insert' => 0,
            'update' => 0,
            'failed' => 0,
            'total'  => count($data),
        ];
        foreach ($data as $k => $v) {
            $up=0;
            unset($data[$k]['group'],$v['group']);
            $v['update_time'] = time();
            $NoteBook=DB::table('b_note_book')->where('path', '=', $v['path'])->select('id')->first();
            $nid=isset($NoteBook)?$NoteBook->id:0;
            if ($nid) {
                if (DB::table('b_note_book')->where('path', '=', $v['path'])->update($v)) {
                    SynLog::getInstance()->record("更新笔记本《{$v['name']}》,成功!",0);
                    $result['update'] += 1;
                    $up=1;
                } else {
                    $result['failed'] += 1;
                    SynLog::getInstance()->record("更新笔记本《{$v['name']}》,失败!", 1);
                }
            } else {
                if ($nid=DB::table('b_note_book')->insertGetId($v)) {
                    SynLog::getInstance()->record("新建笔记本《{$v['name']}》,成功!",0);
                    $result['insert'] += 1;
                    $up=1;
                } else {
                    SynLog::getInstance()->record("新建笔记本《{$v['name']}》,失败!", 1);
                    $result['failed'] += 1;
                }
            }
            if ($synNote && $up) {
                $noteResult = $this->updateNote($v['path'],$nid);
                SynLog::getInstance()->record("开始同步笔记本《{$v['name']}》中的笔记",0);
                foreach ($noteSynResult as $key => $val){
                    $noteSynResult[$key]=$noteSynResult[$key]+$noteResult[$key];
                }
            }
        }
        SynLog::getInstance()->record('同步笔记本数据完成!',4);
        $result['noteSynResult']=$noteSynResult;
        return $result;
    }

    /**
     * 同步笔记本下面的笔记内容
     * @param $note 笔记本路径
     * @return array|bool
     */
    public function updateNote($notePath,$nid=0)
    {
        if (!$notePath) {
            return false;
        }
        if (is_string($notePath)) {
            try {
                $data = OriginPull::getInstance(OAuth::getInstance()->getAccessToken())->getNoteList($notePath);
            } catch (\Exception $E) {
                return false;
            }
        } else {
            return false;
        }
        if(!$nid){
            $nid=DB::table('b_note_book')->where('path', '=', $notePath)->select('id')->first()->id;
        }
        $result = [
            'insert' => 0,
            'update' => 0,
            'failed' => 0,
            'total'  => count($data),
        ];
        foreach ($data as $v) {
            $note=OriginPull::getInstance(OAuth::getInstance()->getAccessToken())->getNote($v);
            if(!$note){
                continue;
            }
            $note=array_filter($note,function($val){
                return !is_null($val);
            });
            $note['nid']=$nid;
            $note['update_time']=time();
            if (!DB::table('b_note')->where('path', '=', $note['path'])->select('id')->get()->isEmpty()) {
                if (DB::table('b_note')->where('path', '=', $note['path'])->update($note)) {
                    SynLog::getInstance()->record("更新笔记 '{$note['title']}',成功!",0);
                    $result['update'] += 1;
                } else {
                    $result['failed'] += 1;
                    SynLog::getInstance()->record("更新笔记'{$note['title']}',失败!", 1);
                }
            } else {
                if (DB::table('b_note')->insert($note)) {
                    SynLog::getInstance()->record("新建笔记'{$note['title']}',成功!",0);
                    $result['insert'] += 1;
                } else {
                    SynLog::getInstance()->record("新建笔记'{$note['title']}',失败!", 1);
                    $result['failed'] += 1;
                }
            }
        }
        return $result;
    }
}