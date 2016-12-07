<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 接口拼装函数
     * @param int $code
     * @param $msg
     * @param $data
     * @param string $type
     * @param null $rootNodeName
     * @return string
     */
    public function packing($code = 0, $msg='ok', $data=[],$extend=[])
    {
        $returnData = [
            'code' => $code,
            'msg'    => $msg,
            'data'   => $data,
        ];
        if($extend && is_array($extend)){
            $returnData=array_merge($returnData,$extend);
        }
        header('Content-Type:application/json; charset=utf-8');
        return json_encode($returnData, JSON_UNESCAPED_UNICODE);
    }
}
