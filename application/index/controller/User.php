<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/7
 * Time: 22:04
 */
namespace app\index\controller;
use app\common\controller\UserCheck;
use app\common\Error;
use think\Controller;
use think\Request;
use think\Validate;

class User extends Controller {

    /**
     * 登录模板
     * @return mixed
     */
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

    /**
     * 登录验证
     * @param Request $request
     * @return int
     */
    public function checkLogin(Request $request) {
        $params = $request->param();
        $common = new UserCheck();
        $res = $common->checkLogin($params);
        $errCode = $res ? 0 : 1;
        $errArr = Error::getCodeMsgArr($errCode);
        if ($res) {
            $errArr['result'] = "登录失败，请重新输入账户信息！";
        }
        return json($errArr);
    }

    public function exitLogin() {
        $common = new UserCheck();
        if ($common->exitLogin()) {
            return $this->redirect('index/index/index');
        }
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