<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/6
 * Time: 0:43
 */
namespace app\index\controller;

use think\Controller;

class Goods extends Controller
{
    public function detail()
    {
        return $this->fetch();
    }
}