<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/11
 * Time: 23:49
 */
namespace app\admin\controller;
use app\index\model\User;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $u = new User();
        $res = $u->getUserPaging();
        $this->assign('userlist', $res);
        return $this->fetch();
    }
}