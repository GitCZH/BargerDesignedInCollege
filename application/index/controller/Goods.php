<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/6
 * Time: 0:43
 */
namespace app\index\controller;

use think\Controller;
use think\Request;

class Goods extends Base
{
    public function detail(Request $request)
    {
        $gid = (int)$request->param('gid');
        return $this->fetch();
    }
}