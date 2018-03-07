<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/7
 * Time: 22:20
 */
namespace app\index\model;

use think\Model;

class User extends Model
{
    protected $table = 'ex_uaccount';
    protected $autoWriteTimestamp = true;
    protected $deleteTime = 'delete_time';

    public function setLoginpwdAttr($val) {
        return md5($val);
    }

    public function getIsvalidAttr($val) {
        $status = [
            '封禁',
            '正常'
        ];
        return $status[$val];
    }
}
