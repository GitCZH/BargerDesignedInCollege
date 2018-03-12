<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/12
 * Time: 0:18
 */
namespace app\admin\controller;
use app\index\model\UserCredit;
use think\Request;

class User extends Base
{
    public function login () {
        return $this->fetch();
    }
    public function checkLogin (Request $request) {
        $params = $request->param();
        session('uid', 1);
        echo 111;
        var_dump(session('uid'));
        $this->redirect('index/index');
    }
    public function showUserDetail (Request $request)
    {
        $uid = $request->param('uid');
        $ucredit = new UserCredit();
        var_dump($ucredit->getUserDetailByUid($uid));
    }
}