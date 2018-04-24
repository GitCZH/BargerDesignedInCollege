<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/24
 * Time: 15:17
 */
namespace app\common\dataoper;
use app\common\Factory;

class Gimgs
{
    public function saveAll($data)
    {
        $model = Factory::getModelObj('gimgs');
        return $model->allowField(true)->saveAll($data);
    }
}