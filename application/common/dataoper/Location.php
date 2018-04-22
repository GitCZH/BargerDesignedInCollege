<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/22
 * Time: 15:52
 */
namespace app\common\dataoper;
use app\common\Factory;

class Location
{
    public function getProvinces()
    {
        $model = Factory::getModelObj('location');
        return $model->getProvinces();
    }

    public function getCities($pid)
    {
        $model = Factory::getModelObj('location');
        return $model->getCities($pid);
    }

    public function getAreas($cid)
    {
        $model = Factory::getModelObj('location');
        return $model->getAreas($cid);
    }
}