<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/12
 * Time: 0:12
 */
namespace app\index\model;
use think\Model;

class UserCredit extends Model
{
    protected $table = 'ex_user';

    public function getUserDetailByUid ($uid) {
        return $this->where(['id' => $uid])
            ->find();
    }
}