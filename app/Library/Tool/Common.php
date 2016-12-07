<?php

/**
 * Class Common
 * 类功能
 *
 * @datetime: 2016/12/7 19:15
 * @author: lihs
 * @copyright: ec
 */

namespace App\Library\Tool;


class Common {

    /**
     * 返回请求，"后台静默"继续运行
     *
     * @param int $timeLimit
     * @param string $memoryLimit
     * @return bool
     */
    public static function switchRunningState($timeLimit=0,$memoryLimit='100M')
    {
        set_time_limit($timeLimit);//设置超时时间
        ini_set('memory_limit',$memoryLimit);//设置最大内存
        ignore_user_abort(true);//脚本继续执行
        ob_end_clean();//清除缓冲区数据
        header("Connection: close");//关闭连接
        header(getenv('SERVER_PROTOCOL')." 200 OK");//返回状态码
        header('content-type:application/json;charset=utf8');
        $return=array(
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