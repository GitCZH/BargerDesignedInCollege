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
use app\common\Factory;
use think\Controller;
use think\Cookie;
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
        $errCode = \app\common\dataoper\User::checkLogin($params['loginName'], md5($params['loginPwd']), $params['remember']);
        $errArr = Error::getCodeMsgArr($errCode);
        if ($errCode == 2) {
            $errArr['result'] = '用户名不存在';
        }
        if ($errCode == 3) {
            $errArr['result'] = "密码错误";
        }
        return json($errArr);
    }

    /**
     * 退出登录
     */
    public function exitLogin() {
        $common = new UserCheck();
        if ($common->exitLogin()) {
            return $this->redirect('index/index/index');
        }
    }

    /**
     * 注册页面
     */
    public function register() {
        return $this->fetch();
    }

    /**
     * 验证注册接口
     * @param Request $request
     * @return \think\response\Json
     */
    public function checkRegister(Request $request) {
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
            $code = 1;
            $errArr = Error::getCodeMsgArr($code);
            $errArr['result'] = $err;
            return json($errArr);
        }
        $user = Factory::getOperObj('user');
        if(!$user->checkEmailNew($params['loginemail'])) {
            $err = '邮箱已被使用';
            $code = 1;
            $errArr = Error::getCodeMsgArr($code);
            $errArr['result'] = $err;
            return json($errArr);
        }
        $res = $user->saveAccount($params);
        if($res) {
            $code = 0;
            $errArr = Error::getCodeMsgArr($code);
            return json($errArr);
        }
    }

}