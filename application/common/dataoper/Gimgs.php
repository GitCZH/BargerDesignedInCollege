<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/24
 * Time: 15:17
 */
namespace app\common\dataoper;
use app\common\Factory;
use app\common\Functions;

class Gimgs
{
    public function saveAll($data)
    {
        $model = Factory::getModelObj('gimgs');
        return $model->allowField(true)->saveAll($data);
    }

    /**
     * 根据闲物ID获取所对应的图片
     * @param $index  指定返回第几张图片 | 为null时表示返回所有图片
     * @return string | array
     */
    public function getImgsByGid($gid, $index = null)
    {
        $model = Factory::getModelObj('gimgs');
        $imgs = $model->where(['gid' => $gid, 'islist' => 1])->select();
        if (empty($imgs)) {
            return [];
        }
        if (!is_null($index) && is_numeric($index)) {
            return $index < count($imgs) ? $imgs[$index]->getData()['img'] : '';
        }
        return Functions::dataSetToArray($imgs);
    }
}