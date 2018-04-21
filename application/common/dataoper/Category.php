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
use app\common\Functions;

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

    /**
     * 根据fid获取子分类
     * @param int $fid
     * @return array
     */
    public function getCatsByFid($fid = 0)
    {
        $model = Factory::getModelObj('category');
        $cats = $model->where(['islist' => 1, 'fid' => $fid])->select();
        return !empty($cats) ? Functions::dataSetToArray($cats) : [];
    }

    /**
     * 递归获取无限极分类
     * @param $cats
     */
    public function getChildrenCat(&$cats)
    {
        $model = Factory::getModelObj('category');
        foreach ($cats as &$cat) {
            $childs = $model->where(['islist' => 1, 'fid' => $cat['id']])->select();
            if (!empty($childs)) {
                $cat['child'] = Functions::dataSetToArray($childs);
                $this->getChildrenCat($cat['child']);
            }
        }
    }

    /**
     * 获取所有分类【无限极】
     * @return array
     */
    public function getAllCats()
    {
        $model = Factory::getModelObj('category');
        $cats = $model->where(['islist' => 1, 'fid' => 0])->select();

        $cats = Functions::dataSetToArray($cats);
        $this->getChildrenCat($cats);
        return $cats;
    }
}