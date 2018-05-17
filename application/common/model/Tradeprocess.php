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

class Tradeprocess extends Model
{
    protected $table = 'ex_tradeprocess';
    protected $autoWriteTimestamp = true;

    /**
     * 获取 交易的所有 流程信息
     * @param $tid
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getProcessByTid($tid)
    {
        return $this->where(['tid' => $tid])->select();
    }

    public function addProcessInfo($tid, $info)
    {
        return $this->allowField(true)->save(['tid' => $tid, 'process' => $info]);
    }
}