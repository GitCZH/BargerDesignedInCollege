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
use think\Db;

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
     * 更新物品信息
     */
    public function updateGoods(array $datas, $id)
    {
        $model = Factory::getModelObj('goods');
//        ????  当使用save 更新时，在foreach中只会使第一个记录成功，后面的都是失败
        return $model->where(['id' => $id])->update($datas);
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

    /**
     * 获取最新上架的闲物
     */
    public function getLatestGoods()
    {
//        查询视图中的闲物图片关联的数据
        return Db::query('select *from ex_goods_gimg WHERE uid != ' . session('uid') . ' and istrade=\'0\' order by create_time desc limit 8');
        return Db::view('goods_gimg')
            ->where('uid', 'eq', session('uid'))
            ->order('create_time desc')
            ->limit(8)
            ->select();
    }

    /**
     * 获取同城闲物
     */
    public function getSameCityGoods($pid, $cid)
    {
        return Db::query('select *from ex_goods_gimg where gpid=' . $pid . ' and gcid=' . $cid . ' and uid !=' . session('uid') . ' and istrade=0');
    }

    /**
     * 更新物品浏览数
     */
    public function incScanNum($gid)
    {
        $model = Factory::getModelObj('goods');
        return $model->where(['id' => $gid])->setInc('scannum');
    }

    /**
     * 获取用户发布的且未被交易的闲置物品
     */
    public function getLeisureGoods($uid)
    {
        $model = Factory::getModelObj('goods');
        return $model->where(['uid' => $uid, 'istrade' => 0, 'status' => 1])->select();
    }
}