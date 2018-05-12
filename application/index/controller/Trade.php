<?php
/**
 * 订单控制器
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/6
 * Time: 22:36
 */
namespace app\index\controller;
use app\common\Error;
use app\common\Factory;
use app\common\Functions;
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

    /**
     * 发送交易请求
     * @param Request $request
     * @return \think\response\Json
     */
    public function tradeRequest(Request $request)
    {
        $params = $request->param();
//        过滤自己发布的闲物
        $goods = Factory::getOperObj('goods');
        $goodsInfo = $goods->getGoodsByGid($params['gid']);
        if (empty($goodsInfo) || $goodsInfo->getData()['uid'] == session('uid')) {
            $code = 4;
            $errArr = Error::getCodeMsgArr($code);
            return json($errArr);
        }

//        获取交易订单号
        $code = 0;
        $tradeNum = Functions::generateTradeNum();
        $goodsInfo = $goodsInfo->getData();
//        执行交易请求流程
//        1、发起请求的数据存储
        $trade = Factory::getOperObj('trade');
        $rRes = $trade->save($tradeNum, $goodsInfo['uid'], session('uid'), $params['gid'], $goodsInfo['gname'], $params['way'], $params['addr']);
        if (!$rRes) {
            Functions::logs('request trade save failed' . var_export($params, true) . ' request-gid:' . $params['gid']);
            $code = 5;
        }
//        2、被请求方存储数据
//        获取请求方的物品信息
        foreach($params['sgid'] as $item) {
            $selfGoods = $goods->getGoodsByGid($item)->getData();
            $rRes = $trade->save($tradeNum, session('uid'), $goodsInfo['uid'], $item, $selfGoods['gname'], $params['way'], $params['addr']);
            if (!$rRes) {
                Functions::logs('send request trade save failed' . var_export($params, true) . ' item-gid:' . $item);
                $code = 5;
            }
        }
        $errArr = Error::getCodeMsgArr($code);
        return json($errArr);
    }
}