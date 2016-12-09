<?php

/**
 * Class Note
 * 笔记数据模型
 *
 * @datetime: 2016/12/9 9:44
 * @author: lihs
 * @copyright: ec
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
class Note extends Model{
    protected $table='b_note';
    public $timestamps = false;
}