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

    public function editById($id, $data)
    {
        $model = Factory::getModelObj('category');
        return $model->save($data, ['id' => $id]);
    }

    public function delById($id)
    {
        $model = Factory::getModelObj('category');
        return $model->where(['id' => $id])->delete();
    }

    public function getCatsByFid($fid = 0)
    {
        $model = Factory::getModelObj('category');
        $cats = $model->where(['islist' => 1, 'fid' => $fid])->select();
        return $cats->getData();
    }

    public function getChildrenCat($cats)
    {
        $model = Factory::getModelObj('category');
        foreach ($cats as &$cat) {
            $child = $model->where(['islist' => 1, 'fid' => $cat['id']])->select();
            if (!empty($child)) {
                $cats['child'] = $child->getData();
                $this->getChildrenCat($child->getData());
            }
        }
        return $cats;
    }
    public function getAllCats()
    {
        $model = Factory::getModelObj('category');
        $cats = $model->where(['islist' => 1, 'fid' => 0])->select();
        if (!empty($cats)) {
            $cats = $cats->getData();
        }

        foreach ($cats as &$cat) {
            $child = $this->getCatsByFid($cat['id']);
        }
        return $cats;
    }
}