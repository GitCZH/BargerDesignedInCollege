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

    /**
     * 新增收货地址
     */
    public function saveOne(array $datas)
    {
        $model = Factory::getModelObj('addr');
        return $model->allowField(true)->save($datas);
    }

    /**
     * 更新收货地址
     */
    public function updateById(array $datas, $id)
    {
        $model = Factory::getModelObj('addr');
        return $model->allowField(true)->save($datas, ['id' => $id]);
    }
}