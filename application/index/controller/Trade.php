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
use think\Db;
use think\Request;

class Trade extends Base
{
    protected $tradeStatusArr = [
        ['请求状态', '等待双方同意'],
        ['对方已接受', '交易中'],
        ['交易完成', ''],
        ['交易完成', ''],
        ['请求被拒绝', '']
    ];
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
            $item['tradeStatus'] = $this->tradeStatusArr[$item['tradestatus']];
            $bUserCredit = $userC->getUserDetailByUid($item['buid']);
            $item['bUName'] = $bUserCredit->nickname;
            $sUserCredit = $userC->getUserDetailByUid($item['suid']);
            $item['sUName'] = $sUserCredit->nickname;
//            获取物品第一张图片
            $item['img'] = $gimg->getImgsByGid($item['gid'], 0);

        }
//        以数组中的  request 列排序
        $requestArr = array_column($tradeOrder, 'request');
        array_multisort($requestArr, SORT_DESC, $tradeOrder);
//        dump($tradeOrder);exit;
        $this->assign('tradeOrders', $tradeOrder);
        return $this->fetch();
    }

    /**
     * 交易详情
     */
    public function tradeDetail(Request $request)
    {
        $tid = $request->param('tid');
        $trade = Factory::getOperObj('trade');
        $userC = Factory::getOperObj('userCredit');
        $gimg = Factory::getOperObj('gimgs');
        $process = Factory::getModelObj('tradeprocess');
        $tradeDetailArr = $trade->getByTradeNum($tid);
        $selfArr = [];
        $saleArr = [];
        if (!empty($tradeDetailArr)) {
            foreach($tradeDetailArr as &$item) {
//                获取对应的状态信息
                $processInfo = $process->getProcessByTid($item->id);
                $item['process'] = Functions::dataSetToArray($processInfo);
//                获取对应user信息
                $bUser = $userC->getUserDetailByUid($item->buid);
                $sUser = $userC->getUserDetailByUid($item->suid);
                $item['bUName'] = empty($bUser) ? '佚名' : $bUser->nickname;
                $item['sUName'] = empty($sUser) ? '佚名' : $sUser->nickname;
//                获取图片
                $img = $gimg->getImgsByGid($item->gid, 0);
                $item['img'] = $img;
                if ($item->suid == session('uid')) {
                    $selfArr[] = $item;
                } else {
                    $saleArr[] = $item;
                }
            }
        }
        $this->assign('selves', Functions::dataSetToArray($selfArr));
        $this->assign('sales', Functions::dataSetToArray($saleArr));
//        dump($selfArr);exit;
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
//        1、被请求方的数据存储
        $trade = Factory::getOperObj('trade');
        $rRes = $trade->save($tradeNum, $goodsInfo['uid'], session('uid'), $params['gid'], $goodsInfo['gname'], $params['way'], $params['addr'], 1);
        if (!$rRes) {
            Functions::logs('request trade save failed' . var_export($params, true) . ' request-gid:' . $params['gid']);
            $code = 5;
        }
//        2、请求方存储数据
//        获取请求方的物品信息
        foreach($params['sgid'] as $item) {
            $selfGoods = $goods->getGoodsByGid($item)->getData();
            $rRes = $trade->save($tradeNum, session('uid'), $goodsInfo['uid'], $item, $selfGoods['gname'], $params['way'], $params['addr'], 0);
            if (!$rRes) {
                Functions::logs('send request trade save failed' . var_export($params, true) . ' item-gid:' . $item);
                $code = 5;
            } else {
//                已成功发送交易请求 更新物品的状态[交易请求状态]
                $res = $goods->updateGoods(['istrade' => 1], $item);
                Functions::logs(var_export($res, true) .' send request update istrade ' . var_export($selfGoods, true));
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
        $str = [];
        if (!empty($tradeOrder)) {
            foreach ($tradeOrder as &$item) {
                $item['tradeStatus'] = $this->tradeStatusArr[$item['tradestatus']];
                $bUserCredit = $userC->getUserDetailByUid($item['buid']);
//                $item['bUName'] = $bUserCredit->nickname;
                $sUserCredit = $userC->getUserDetailByUid($item['suid']);
//                $item['sUName'] = $sUserCredit->nickname;
//                获取第一张物品图片
                $item['img'] = $gimg->getImgsByGid($item['gid'], 0);

                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);

                $tmp = '<div>
                            <div class="am-container">
                                <div class="am-u-sm-3"><b class="am-text-danger">' . ($item['request'] == 1 ? '我发送的请求' : '我收到的请求') . '</b><br /><b>' . $item['create_time'] . '</b></div>
                                <div class="am-u-sm-6">订单号：<br /><b>' . $item['tradenum'] . '</b></div>
                                <div class="am-u-sm-3"><a href="#" class="am-text-warning">' . $sUserCredit->nickname . '</a><br />%s</div>
                            </div>
                            <hr class="am-divider">
                            <div class="am-container">
                                <div class="am-u-sm-2">
                                    <img src="/ExchangeGit/public/static/goods/upload/' . $item['img'] . '" width="200" height="120" class="am-img-thumbnail am-radius">
                                    <span>' . $item['gname'] . '</span>
                                </div>
                                <div class="am-u-sm-2">
                                    <span class="am-text-success">' .($item['tradway'] == 0 ? '直接购买' : '物物交换') . '</span>
                                </div>
                                <div class="am-u-sm-2">
                                    <span class="am-text-success">[' . $item['tradeStatus'][0] . ']</span><br>
                                    <span href="#" class="am-text-sm am-text-warning">' . $item['tradeStatus'][1] . '</span>
                                </div>
                                <div class="am-u-sm-2">
                                    <a href="#">' . $bUserCredit->nickname . '</a>
                                </div>
                                <div class="am-u-sm-2">
                                    <a target="_blank" href="' . url('index/trade/tradeDetail', 'tid=' . $item['tradenum']) . '">订单详情</a>
                                </div>
                                <div class="am-u-sm-2">
                                    <a href="#">评价</a>
                                </div>
                            </div>
                            <hr class="am-divider">
                        </div>';

                $appendStr = '<a href="' . url('index/trade/requestDone', 'signal=1&tradnum=' . $item['tradenum']) . '" class="am-btn am-btn-success">同意</a>
                                    <a href="' . url('index/trade/requestDone', 'signal=0&tradnum=' . $item['tradenum']) . '" class="am-btn am-btn-danger">拒绝</a>';
                $str[] = !$item['tradestatus'] && !$item['request'] ? sprintf($tmp, $appendStr) : sprintf($tmp, '');
            }
        }
        $errArr = Error::getCodeMsgArr($code);
        $errArr['result'] = $str;
        return json($errArr);
    }

    /**
     * 交易请求处理
     *   同意 | 拒绝  请求
     */
    public function requestDone(Request $request)
    {
        $signal = (int)$request->param('signal');
        $tradeNum = $request->param('tradnum');

        $status = $signal ? 1 : 4;
        $trade = Factory::getModelObj('trade');
        $res = $trade->requestDone($tradeNum, $status);
        return $res ? $this->success('操作成功') : $this->error('操作失败');

        /*$code = $res ? 0 : 1;
        $errArr = Error::getCodeMsgArr($code);
        $errArr['result'] = $res ? '操作成功' : '操作失败';
        return json($errArr);*/
    }

    /**
     * 交易完成   确认收货
     */
    public function ackReceiveGoods(Request $request)
    {
        Functions::logs('确认收货' . session('uid') . ' ' . session('uname'));
        $tid = (int)$request->param('tid');
        $trade = Factory::getModelObj('trade');
        $res = $trade->ackRequestDone($tid);
        $code = $res ? 0 : 1;
        $errArr = Error::getCodeMsgArr($code);
        return json($errArr);
    }

    public function addTradeInfo(Request $request)
    {
        $tid = (int)$request->param('tid');
        $info = $request->param('info');

        $tProcess = Factory::getModelObj('tradeprocess');
        $res = $tProcess->addProcessInfo($tid, $info);
        $code = $res ? 0 : 1;
        $errArr = Error::getCodeMsgArr($code);
        return json($errArr);
    }
}