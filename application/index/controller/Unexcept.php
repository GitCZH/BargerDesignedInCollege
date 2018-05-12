<?php
/**
 * 未预期到的错误
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/10
 * Time: 11:02
 */
namespace app\index\controller;
use think\Controller;

class Unexcept extends Controller
{
    public function to404()
    {
        return $this->fetch();
    }
}