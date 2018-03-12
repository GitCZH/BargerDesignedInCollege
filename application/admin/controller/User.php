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
        $data['errCode'] = 0;
        $data['errMsg'] = 'success';
        return json_encode($data);
    }
    public function showUserDetail (Request $request)
    {
        $uid = $request->param();
        $ucredit = new UserCredit();
        var_dump($ucredit->getUserDetailByUid($uid));
    }
}