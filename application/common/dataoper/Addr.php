<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/12
 * Time: 15:00
 */
namespace app\common\dataoper;
use app\common\Factory;

class Addr
{
    /**
     * 获取用户的收获地址
     */
    public function getReceiveAddrs($uid)
    {
        $model = Factory::getModelObj('addr');
        return $model->where(['uid' => $uid])->select();
    }
}