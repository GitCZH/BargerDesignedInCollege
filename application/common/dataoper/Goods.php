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
    /**
     * 添加闲物
     * @param $data
     * @return bool
     */
    public function addGoods($data)
    {
        $model = Factory::getModelObj('goods');
        if($model->allowField(true)->save($data)) {
            return $model->id;
        }
        Functions::logs('发布闲物失败' . var_export($data, true));
        return false;
    }

    /**
     * 编辑闲物
     */
    public function editGoods($data, $id)
    {
        $model = Factory::getModelObj('goods');
        return $model->allowField(true)->save($data, ['id' => $id]);
    }

    /**
     * 闲物审核通过
     */
    public function passAudit($id)
    {
        $model = Factory::getModelObj('goods');
        return $model->where(['id' => $id, 'islist' => 1])->update(['status' => 1]);
    }

    /**
     * 获取已审核物品列表
     */
    public function getGoodsList()
    {
        $model = Factory::getModelObj('goods');
        return $model->where(['status' => 1, 'islist' => 1])->select();
    }

    /**
     * 下架闲物
     */
    public function theShelvesGoods($id)
    {
        $model = Factory::getModelObj('goods');
        return $model->save(['status' => 2], ['id' => $id]);
    }

    /**
     * 获取待审核的闲物
     */
    public function getUnauditGoods($page, $limit = 10)
    {
        $model = Factory::getModelObj('goods');
        return $model->where(['status' => 0, 'islist' => 1])->page($page, $limit)->select();
    }

    /**
     * 根据记录ID获取闲物相关信息
     */
    public function getGoodsByGid($gid)
    {
        $model = Factory::getModelObj('goods');
        return $model->where(['id' => $gid])->find();
    }

    /**
     * 根据status获取总数
     * @param $status 0 1 2 3 。。。
     */
    public function getTotalCountsByStatus($status)
    {
        $model = Factory::getModelObj('goods');
        return $model->where(['status' => $status, 'islist' => 1])->count();
    }
}