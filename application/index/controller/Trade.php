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
        $params = $request->param();
        $params['uid'] = session('uid');
        $trade = Factory::getOperObj('trade');
        $userC = Factory::getOperObj('userCredit');
        $gimg = Factory::getOperObj('gimgs');
//        订单查询参数  [param 订单的状态]
        if (!isset($params['time'])) {
//            可能参数
            /**
             * 时间
             * 关键词
             * status [must]
             * 时间 + 关键词 + status
             */
            $params['time'] = 'm';

        }
        $tradeOrder = $trade->getAllTrades($params);
//        return json($tradeOrder);
//        dump(Functions::dataSetToArray($tradeOrder));exit;
//        转换数据
        foreach($tradeOrder as &$item) {
            $item->tradeStatus = $item->tradestatus;
            $bUserCredit = $userC->getUserDetailByUid($item->buid);
            $item->bUName = $bUserCredit->nickname;
            $sUserCredit = $userC->getUserDetailByUid($item->suid);
            $item->sUName = $sUserCredit->nickname;
//            获取物品第一张图片
            $item['img'] = $gimg->getImgsByGid($item['gid'], 0);

        }
        $this->assign('tradeOrders', Functions::dataSetToArray($tradeOrder));
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
        $rRes = $trade->save($tradeNum, $goodsInfo['uid'], session('uid'), $params['gid'], $goodsInfo['gname'], $params['way'], $params['addr'], 1);
        if (!$rRes) {
            Functions::logs('request trade save failed' . var_export($params, true) . ' request-gid:' . $params['gid']);
            $code = 5;
        }
//        2、被请求方存储数据
//        获取请求方的物品信息
        foreach($params['sgid'] as $item) {
            $selfGoods = $goods->getGoodsByGid($item)->getData();
            $rRes = $trade->save($tradeNum, session('uid'), $goodsInfo['uid'], $item, $selfGoods['gname'], $params['way'], $params['addr'], 0);
            if (!$rRes) {
                Functions::logs('send request trade save failed' . var_export($params, true) . ' item-gid:' . $item);
                $code = 5;
            } else {
//                已成功发送交易请求 更新物品的状态[交易请求状态]
                $goods->updateGoods(['istrade' => 2], $selfGoods['id']);
            }
        }

        $errArr = Error::getCodeMsgArr($code);
        return json($errArr);
    }

    /**
     * 根据交易状态获取 交易订单
     * time 0 本月的交易 1 本年的交易 2 去年的交易 3去年以前的交易
     */
    public function getTradesByStatusAjax(Request $request)
    {
        $params = $request->param();
        $params['uid'] = session('uid');
        if ($params['selectTime'] == 0) {
            $params['time'] = 'm';
        }
        if ($params['selectTime'] == 1) {
            $params['time'] = 'y';
        }
        if ($params['selectTime'] == 2) {
//            去年
            $params['time']['interval'] = 'between';
            $thisYear = date('Y');
            $params['time']['value'] = [$thisYear-1 . '-1-1', $thisYear-1 . '-12-31'];
        }
        if ($params['selectTime'] == 3) {
//            去年以前
            $params['time']['interval'] = '<';
            $thisYear = date('Y');
            $params['time']['value'] = $thisYear-1 . '-1-1';
        }
        $trade = Factory::getOperObj('trade');
        $userC = Factory::getOperObj('userCredit');
        $gimg = Factory::getOperObj('gimgs');
        $tradeOrder = $trade->getAllTrades($params);
        $code = empty($tradeOrder) ? 2 : 0;
//        return json($tradeOrder);
        //        转换数据
        if (!empty($tradeOrder)) {
            foreach ($tradeOrder as &$item) {
//            $item->tradeStatus = $item->tradestatus;
                $bUserCredit = $userC->getUserDetailByUid($item->buid);
                $item->bUName = $bUserCredit->nickname;
                $sUserCredit = $userC->getUserDetailByUid($item->suid);
                $item->sUName = $sUserCredit->nickname;
//                获取第一张物品图片
                $item['img'] = $gimg->getImgsByGid($item['gid'], 0);
            }
        }
        $errArr = Error::getCodeMsgArr($code);
        $errArr['result'] = $tradeOrder;
        return json($errArr);
    }
}