<?php
namespace App\Library\Transform\Attachment;
use App\Library\Transform\Attachment\Base;
/**
 * Class Image
 * 图片处理
 *
 * @datetime: 2016/12/29 11:31
 * @author: lihs
 * @copyright: ec
 */
class Image extends Base {
    protected static $Obj;
    private function __construct($token,$savePath='') {
        $this->setBaseParams($token,$savePath);
    }

    /**
     * 获取实例
     * @param $token
     * @param string $savePath
     * @return Image
     */
    public static function getInstance($token,$savePath=''){
        if(!self::$Obj){
            self::$Obj=new self($token,$savePath);
        }
        return self::$Obj;
    }

    /**
     * 设置基础参数
     */
    public function setBaseParams($token,$savePath=''){
        $this->token=$token;
        $this->savePath=$savePath?:'/www/static/m_blog_source/source';
    }
}