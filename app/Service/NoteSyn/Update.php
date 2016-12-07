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
            if (!(DB::table('b_note_book')->where('path', '=', $v['path'])->select('id')->get()->isEmpty())) {
                if (DB::table('b_note_book')->where('path', '=', $v['path'])->update($v)) {
                    SynLog::getInstance()->record("更新笔记本《{$v['name']}》,成功!",0);
                    $result['update'] += 1;
                    $up=1;
                } else {
                    $result['failed'] += 1;
                    SynLog::getInstance()->record("更新笔记本《{$v['name']}》,失败!", 1);
                }
            } else {
                if (DB::table('b_note_book')->insert($v)) {
                    SynLog::getInstance()->record("新建笔记本《{$v['name']}》,成功!",0);
                    $result['insert'] += 1;
                    $up=1;
                } else {
                    SynLog::getInstance()->record("新建笔记本《{$v['name']}》,失败!", 1);
                    $result['failed'] += 1;
                }
            }
            if ($synNote && $up) {
                $noteResult = $this->updateNote($v['path']);
                foreach ($noteSynResult as $key => $v){
                    SynLog::getInstance()->record("开始同步笔记本《{$v['name']}》中的笔记",0);
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
     * @param $note
     * @return array|bool
     */
    public function updateNote($note)
    {
        if (!$note) {
            return false;
        }
        if (is_string($note)) {
            try {
                $data = OriginPull::getInstance(OAuth::getInstance()->getAccessToken())->getNoteList($note);
            } catch (\Exception $E) {
                return false;
            }
        } elseif (is_array($note)) {
            $data = $note;
            unset($note);
        } else {
            return false;
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
    /**
     * 返回请求，"后台静默"继续运行
     * @param $callBack
     */
    private function switchRunningState()
    {
        set_time_limit(0);//设置不超时
        ini_set('memory_limit','100M');//设置最大内存
        ignore_user_abort(true);//脚本继续执行
        ob_end_clean();//清除缓冲区数据
        header("Connection: close");//关闭连接
        header("HTTP/1.1 200 OK");//返回状态码
        header('content-type:application/json;charset=utf8');
        $return=array(
            'data'=>null,
            'msg'=>'任务已开始执行...',
            'code'=>0
        );
        echo json_encode($return,JSON_UNESCAPED_UNICODE);
        $size = ob_get_length();//缓冲数据长度
        header("Content-Length: $size");
        ob_end_flush();
        ob_flush();
        flush();
        if (session_id()) session_write_close();
        return fastcgi_finish_request();//关闭请求
    }
}