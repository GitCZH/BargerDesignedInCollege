<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/12
 * Time: 22:04
 */
namespace app\common\dataoper;

use app\common\Factory;
use think\Db;

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
        $sql = 'select * from %s ';
//        $model = Factory::getModelObj('trade');
//        是否有关键词  使用子查询精确结果
        if (isset($condition['keyWord']) && $condition['keyWord'] != '') {
//            $subSql = $model->whereOr('tradenum','like', '%' . $condition['keyWord'] . '%')
//                ->whereOr('gname','like', '%' . $condition['keyWord'] . '%')->buildSql();
            $subSql = '(select *from `ex_trade` WHERE `tradenum` LIKE \'%' . $condition['keyWord'] . '%\' or `gname` like \'%'
                . $condition['keyWord'] . '%\' )';
        }
        $sql = (isset($subSql) ? sprintf($sql, $subSql) : sprintf($sql, 'ex_trade')) . ' as s where ';
        if (isset($condition['tradestatus']) && $condition['tradestatus'] != -1) {
            $sql .= ' s.`tradestatus` = \'' . $condition['tradestatus'] .'\' and ';
        }
        /*if (isset($condition['time']) && is_array($condition['time'])) {
//            $model = $model->whereTime('create_time', $condition['time']['interval'], $condition['time']['value']);
            $time = $this->whereTime('create_time', $condition['time']['interval'], $condition['time']['value']);
        } elseif (is_string($condition['time'])) {
//            $model = $model->whereTime('create_time', $condition['time']);
            $time = $this->whereTime('create_time', $condition['time']);
        }*/
        if (isset($condition['time'])) {
            if (is_array($condition['time'])) {
                $time = $this->whereTime('create_time', $condition['time']['interval'], $condition['time']['value']);
            }
            if (is_string($condition['time'])) {
                $time = $this->whereTime('create_time', $condition['time']);
            }
        }
        if (isset($time)) {
            $sql .= $time . ' and';
        }
        $sql .= ' buid = ' . $condition['uid'] . ' and';
        $sql = rtrim($sql, 'and');
//        return $sql;
        return Db::query($sql);
    }

    /**
     * 框架的 whereTime 在使用子查询时不能正确解析时间，
     * 只好自己使用原生SQL语句
     * @param $op
     * @param null $range
     * @return array|null
     */
    public function whereTime($field, $op, $range = null)
    {
        if (is_null($range)) {
            if (is_array($op)) {
                $range = $op;
            } else {
                // 使用日期表达式
                switch (strtolower($op)) {
                    case 'today':
                    case 'd':
                        $range = ['today', 'tomorrow'];
                        break;
                    case 'week':
                    case 'w':
                        $range = ['this week 00:00:00', 'next week 00:00:00'];
                        break;
                    case 'month':
                    case 'm':
                        $range = ['first Day of this month 00:00:00', 'first Day of next month 00:00:00'];
                        break;
                    case 'year':
                    case 'y':
                        $range = ['this year 1/1', 'next year 1/1'];
                        break;
                    case 'yesterday':
                        $range = ['yesterday', 'today'];
                        break;
                    case 'last week':
                        $range = ['last week 00:00:00', 'this week 00:00:00'];
                        break;
                    case 'last month':
                        $range = ['first Day of last month 00:00:00', 'first Day of this month 00:00:00'];
                        break;
                    case 'last year':
                        $range = ['last year 1/1', 'this year 1/1'];
                        break;
                    default:
                        $range = $op;
                }
            }
            $op = is_array($range) ? 'between' : '>';
        }
        return 's.`' . $field . '` ' . $op . ' ' . (is_array($range) ? strtotime($range[0]) . ' and ' . strtotime($range[1]) : strtotime($range));
    }

    /**
     * 根据订单号获取所有有关物品
     */
    public function getByTradeNum($tradeNum)
    {
        $model = Factory::getModelObj('trade');
        return $model->where(['tradenum' => $tradeNum])->select();
    }
}