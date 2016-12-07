<?php
/**
 * SynLog
 *
 *
 * @version: 1.0
 * @datetime: 2016/11/28 20:06
 * @author: lihs
 * @copyright: ec
 */

namespace App\Service\NoteSyn;

use Illuminate\Support\Facades\DB;
class SynLog
{
    protected $Model;
    protected static $Obj;
    private function __construct()
    {
        $this->Model=DB::table('b_syn_log');
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
     * 记录一条同步日志
     * @param $msg
     * @param $code 0 普通提醒，1 警告，2 错误 ，3 同步异常中断 4 完成
     */
    public function record($msg,$code)
    {
        return $this->Model->insert(['code'=>$code?:0,'content'=>$msg]);
    }

    /**
     * 获取当前同步状态
     */
    public function getCurrentStatus()
    {
        return $this->Model->where(true)->orderBy('id','desc')->first();
    }

    /**
     * 清空所有日志
     */
    public function destroy()
    {
        return $this->Model->truncate();
    }
}