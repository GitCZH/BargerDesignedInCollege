<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/11
 * Time: 23:49
 */
namespace app\admin\controller;
use app\index\model\User;

class Index extends Base
{
    public function index()
    {
        return $this->fetch();
    }
}