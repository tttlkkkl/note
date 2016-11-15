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
     * @param int $status
     * @param $msg
     * @param $data
     * @param string $type
     * @param null $rootNodeName
     * @return string
     */
    public function packing($status = 0, $msg, $data)
    {
        $returnData = [
            'status' => $status,
            'msg'    => $msg,
            'data'   => $data,
        ];
        return json_encode($returnData, JSON_UNESCAPED_UNICODE);
    }
}
