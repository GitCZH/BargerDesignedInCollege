<?php
/**
 * 用户地址类
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/13
 * Time: 0:01
 */
namespace app\common\model;
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
            ['请求被拒绝', '']
        ];
        return $str[$value];
    }
}