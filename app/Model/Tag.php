<?php

/**
 * Class Tag
 * 类功能
 *
 * @datetime: 2016/12/9 9:55
 * @author: lihs
 * @copyright: ec
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tag extends Model{
    protected $table='b_tag';
    protected $guarded = ['deleted_at'];

    /**
     * 用于同步的探测性保存
     * @param $name
     * @param $path
     */
    public function synUpdate($name,$path){
        $dateTime=date('Y-m-d h:i:S');
        $insertData=[
            'name'=>$name,
            'created_at'=>$dateTime,
            'updated_at'=>$dateTime,
            'path'=>$path
        ];
    }
}