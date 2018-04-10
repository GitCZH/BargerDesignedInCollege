<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/7
 * Time: 22:20
 */
namespace app\common\model;

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

    /**
     * 检测是否是已注册的邮箱
     * @param $email
     * @return bool
     */
    public function checkEmailNew($email) {
        $res = $this->where(['loginemail' => $email])->find();
        return $res === null ? true : false;
    }
}
