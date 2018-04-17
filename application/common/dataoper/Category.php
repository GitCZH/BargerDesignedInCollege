<?php
/**
 * 无限极分类数据操作类
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/17
 * Time: 10:30
 */
namespace app\common\dataoper;
use app\common\Factory;

class Category
{
    public function create($catBigName, $catSmallName, $fid = 0)
    {
        $data = [
            'catnameB' => $catBigName,
            'catnameS' => $catSmallName,
            'fid' => $fid
        ];
        $model = Factory::getModelObj('category');

        return $model->data($data)->save();
    }

    public function edit($id, $data)
    {
        $model = Factory::getModelObj('category');
        return $model->save($data, ['id' => $id]);
    }
}