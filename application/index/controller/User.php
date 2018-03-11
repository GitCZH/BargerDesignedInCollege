<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/7
 * Time: 22:04
 */
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Validate;

class User extends Controller {

    public function loginIn() {
        $user = model('user');
        $data = [
            'loginname' => 'test',
            'loginpwd'  => 'test',
            'loginemail'=> 'test@123.com'
        ];
//        $res = $user->data($data);
//        $user->save();
//        print_r($user->select());
//        return $this->fetch();
        return $this->fetch();
    }

    public function checkLogin(Request $request) {
        $params = $request->param();
        $user = model('user');
        $query = [
            'loginname' => $params['loginname'],
            'loginpwd' => md5($params['loginpwd']),
            'isvalid' => 1
        ];
//        $sql = $user->where($query)->find()->getLastSql();
        $res = $user->where($query)->find();
        if(!$res) {
            return 1;
        }
//        找到返回模型对象，未找到返回null
//        print_r($sql);
        $userAccount = $res->getData();
        session('uid', $res->getData()['id']);
        session('uname', $params['loginname']);
    }

    public function exitLogin() {

    }

    public function register() {
        return $this->fetch();
    }

    public function checkRegister(Request $request) {
// TODO  验证邮箱唯一性
        $params = $request->param();
        $msg = [
            'loginname' =>  '用户名至少6个字符',
            'loginpwd'  =>  '密码至少6个字符',
            'loginemail'=>  '填写正确的邮箱格式'
        ];
        $validate  = new Validate([
           'loginname'  =>  'require|min:6',
            'loginpwd'  =>  'require|min:6',
            'loginemail'    =>  'require|email'
        ], $msg);
        if(!$validate->check($params)) {
            $err = $validate->getError();
            echo $err;
            return;
        }
        $user = model('user');
        if(!$this->checkEmailNew($params['loginemail'])) {
            echo '邮箱已被使用';
            return ;
        }
        $res = $user->data($params, true)->save();
        if($res) {
            echo 'success';
        }
    }

}