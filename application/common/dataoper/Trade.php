<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/12
 * Time: 22:04
 */
namespace app\common\dataoper;

use app\common\Factory;

class Trade
{

    public function save($tradeNum, $suid, $buid, $gid, $gname, $tradWay, $addr, $request)
    {
        $model = Factory::getModelObj('trade');
        return $model->allowField(true)->save(
            [
                'tradenum' => $tradeNum,
                'suid' => $suid,
                'buid' => $buid,
                'gid' => $gid,
                'gname' => $gname,
                'tradway' => $tradWay,
                'addr' => $addr,
                'request' => $request
            ]
        );
    }

    /**
     * 获取所有交易订单
     */
    public function getAllTrades(array $condition)
    {
        $model = Factory::getModelObj('trade');
//        是否有关键词
        if (isset($condition['keyWord']) && $condition['keyWord'] != '') {
            $model = $model->whereOr('tradenum','like', '%' . $condition['keyWord'] . '%')
                ->whereOr('gname','like', '%' . $condition['keyWord'] . '%');
        }
        if (isset($condition['keyWord']) && $condition['tradestatus'] != -1) {
            $model = $model->where('tradestatus', $condition['tradestatus']);
        }
        if (isset($condition['keyWord']) && is_array($condition['time'])) {
            $model = $model->whereTime('create_time', $condition['time']['interval'], $condition['time']['value']);
        } elseif (is_string($condition['time'])) {
            $model = $model->whereTime('create_time', $condition['time']);
        }
        return $model->where('buid', $condition['uid'])
                    ->select();
        return $model->getLastSql();
    }
}