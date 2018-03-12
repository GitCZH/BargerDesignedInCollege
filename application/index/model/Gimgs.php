<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/13
 * Time: 0:05
 */
namespace app\index\model;
use think\Model;

class Gimgs extends Model
{
    protected $table = 'ex_gims';

    public function getImgsByGid ($gid) {
        return $this->where(['gid' => $gid])->select();
    }

    public function getOneImgByGid ($gid) {
        return $this->where(['gid' => $gid])->find();
    }
}