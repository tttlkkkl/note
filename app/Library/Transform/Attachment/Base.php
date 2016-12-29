<?php
namespace App\Library\Transform\Attachment;

use App\Library\Tool\Curl;

/**
 * Class Base
 * 基本文件操作
 *
 * @datetime: 2016/12/29 11:33
 * @author: lihs
 * @copyright: ec
 */
class Base {
    protected $savePath;
    protected $token;

    /**
     * 从远程获取文件
     *
     * @param $source
     */
    public function pullFileFromRemote($source) {
        $url = env('ORIGIN_URL') . '/yws/open/resource/download/';
        $header='';
        $param = [
            'oauth_token' => $this->token,
            'id'      => $source
        ];
        $result = json_decode(Curl::get($url, $param, $header), true);
        var_dump($result);
    }

    /**
     * 检查文件目录是否可写
     *
     * @param $path
     * @return bool
     */
    public static function checkPath($path) {
        if (!is_dir($path)) {
            return mkdir($path, 0755, true);
        } else {
            return is_writable($path);
        }
    }
}