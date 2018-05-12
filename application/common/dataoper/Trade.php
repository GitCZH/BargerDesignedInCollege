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

    public function save($tradeNum, $suid, $buid, $gid, $gname, $tradWay)
    {
        $model = Factory::getModelObj('trade');
        return $model->allowField(true)->save(
            [
                'tradenum' => $tradeNum,
                'suid' => $suid,
                'buid' => $buid,
                'gid' => $gid,
                'gname' => $gname,
                'tradway' => $tradWay
            ]
        );
    }
}