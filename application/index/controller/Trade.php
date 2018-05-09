<?php
/**
 * 订单控制器
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/6
 * Time: 22:36
 */
namespace app\index\controller;
use think\Controller;
use think\Request;

class Trade extends Base
{
    /**
     * 我的订单页面
     *
     */
    public function tradeList(Request $request)
    {
        $params = (int)$request->param();
//        订单查询参数  [param 订单的状态]
        if (!empty($params)) {
//            可能参数
            /**
             * 时间
             * 关键词
             * status [must]
             * 时间 + 关键词 + status
             */
//-1 查询所有订单

        }
        return $this->fetch();
    }

    /**
     * 交易详情
     */
    public function tradeDetail(Request $request)
    {
        return $this->fetch();
    }
}