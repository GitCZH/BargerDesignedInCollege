<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/13
 * Time: 0:01
 */
namespace app\index\model;
use think\Model;

class Goods extends Model
{
    protected $table = 'ex_goods';
    protected $autoWriteTimestamp = true;

    public function getGoodsPaging ($page, $limit = 20)
    {
        return $this->where(['islist' => 1])->page($page, $limit)->select();
    }


}