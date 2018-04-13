<?php
/**
 * 用户信誉积分处理规则   TODO 物品管理，交易管理实现后可完善这部分功能
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/13
 * Time: 14:26
 */
namespace app\admin\controller;
class Credit
{
    private $weights = [
        'order' => 0.65,
        'trueInfo' => 0.1,
        'relation' => 0.25,
    ];

    public static function test()
    {
        echo 1111;
    }

//    行为【交易忠诚度】*0.65   真实信息【学历】*0.1  举报|拉黑*0.25
    public function clearingCredit()
    {
        $order = $this->getOrderCredit();
        $true = $this->getTrueInfoCredit();
        $relation = $this->getRelationCredit();

        return $order * $this->weights['order'] +
                $true * $this->weights['trueInfo'] +
                $relation * $this->weights['relation'];
    }
    /**
     * 结算用户交易忠诚度积分
     * 【订单】
     */
    private function getOrderCredit()
    {

    }

    /**
     * 用户真实信息评估积分
     */
    private function getTrueInfoCredit()
    {

    }

    /**
     * 用户关系积分
     */
    private function getRelationCredit()
    {

    }
}