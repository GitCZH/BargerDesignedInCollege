<?php
/**
 * 交易创建控制相关类
 * {经发现，dataoper层是多余的一部分，功能可完全由model层实现  【数据结构相关由controller层控制】}
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/13
 * Time: 0:01
 */
namespace app\common\model;
use app\common\Factory;
use app\common\Functions;
use think\Model;

class Trade extends Model
{
    protected $table = 'ex_trade';
    protected $autoWriteTimestamp = true;

    public function getTradestatusAttr($value)
    {
        $str = [
            ['请求已发送', '等待对方接受'],
            ['对方已接受', '交易中'],
            ['交易完成', ''],
            ['交易完成', ''],
            ['请求被拒绝', '']
        ];
        return $str[$value];
    }

    /**
     * 接受 | 拒绝 交易请求
     * @param $tradeNum
     * @param $status
     * @return false|int
     */
    public function requestDone($tradeNum, $status)
    {
        $res = $this->allowField(true)->save(['tradestatus' => $status], ['tradenum' => $tradeNum]);
        if (!$res) {
            return $res;
        }
//        更新订单中物品的状态
//        获取tradeNum所有的物品
        $goodsObj = $this->where(['tradenum' => $tradeNum])->select();
        $goods = Factory::getOperObj('goods');
        if (!empty($goodsObj)) {
            foreach($goodsObj as $item) {
                $goods->updateGoods(['istrade' => 1], $item->gid);
            }
        }
        return $res;
    }

    /**
     * 交易完成
     */
    public function ackRequestDone($tid)
    {
        $res = $this->allowField(true)->save(['tradestatus' => 2], ['id' => $tid]);
//        更新订单中物品的状态
        if(!$res) {
            return $res;
        }
        $goods = Factory::getOperObj('goods');
        $goods->updateGoods(['istrade' => 2], $tid);
        return $res;
    }
}