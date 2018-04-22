<?php
/**
 * 物品管理相关
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/13
 * Time: 15:35
 */

namespace app\admin\controller;
use app\common\Factory;
use app\common\Functions;
use app\common\LittleTools;
use think\Request;

class Goods extends Base
{
    /**
     * 商品总体情况
     * 折线图表展示
     */
    public function overallSituation()
    {

    }

    /**
     *商品列表
     */
    public function goodsList()
    {

    }

    /**
     * 待审核列表
     */
    public function auditList()
    {

    }

    /**
     * 下架列表
     */
    public function theShelves()
    {

    }

    /**
     * @annotation测试方法
     * 新增物品
     */
    public function create(Request $request)
    {
        if (empty($request->param())) {
//            获取分类信息
            $category = Factory::getOperObj('category');
            $cats = $category->getAllCats();
            Functions::pasteArrToOptions($cats, $options, 0, 0);
            $this->assign('cats', $options);
//            查询省份信息
            $location = Factory::getOperObj('location');
            $provinces = $location->getProvinces();
            $this->assign('provinces', $provinces);
            return $this->fetch();
        }
//        接收参数

//        验证参数

//        存储数据
    }
}