<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/12
 * Time: 0:18
 */
namespace app\admin\controller;
use app\index\model\UserCredit;
use think\Controller;
use think\Request;

class User extends Controller
{
    public function showUserDetail (Request $request)
    {
        $uid = $request->param('uid');
        $ucredit = new UserCredit();
        var_dump($ucredit->getUserDetailByUid($uid));
    }
}