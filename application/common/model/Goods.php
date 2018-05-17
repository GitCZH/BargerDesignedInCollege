<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/13
 * Time: 0:01
 */
namespace app\common\model;
use think\Model;

class Goods extends Model
{
    protected $table = 'ex_goods';
    protected $autoWriteTimestamp = true;

    /**
     * 更新信息  by id
     */
    public function updateGoods(array $data, $id)
    {
        return $this->allowField(true)->save($data, ['id' => $id]);
    }
}