<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/24
 * Time: 15:01
 */
namespace app\common\dataoper;
use app\common\Factory;
use app\common\Functions;

class Goods
{
    public function addGoods($data)
    {
        $model = Factory::getModelObj('goods');
        if($model->allowField(true)->save($data)) {
            return $model->id;
        }
        Functions::logs('新增商品失败' . var_export($data, true));
        return false;
    }
}