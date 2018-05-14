<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/7
 * Time: 22:04
 */
namespace app\index\controller;
use app\common\Cons;
use app\common\controller\UserCheck;
use app\common\Error;
use app\common\Factory;
use app\common\Functions;
use think\Controller;
use think\Cookie;
use think\Request;
use think\Validate;

class User extends Base {

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

    /**
     * 个人中心页
     */
    public function center()
    {
//        获取个人信息

        return $this->fetch();
    }

    /**
     * 录入个人信息页
     */
    public function selfInfo()
    {
        $userC = Factory::getOperObj('userCredit');
        $location = Factory::getOperObj('location');
        $userDetail = $userC->getUserDetailByUid(session('uid'));
        if (!empty($userDetail)) {
            $this->assign('userInfo', $userDetail->getData());
//            获取省市信息
            $city = $location->getCityByCid($userDetail->getData()['ucid']);
            $area = $location->getAreaByAid($userDetail->getData()['uaid']);
            $this->assign('city', $city);
            $this->assign('area', $area);
        }
//        获取省份信息
        $provinces = $location->getProvinces();
        $this->assign('provinces', $provinces);
        return $this->fetch();
    }

    /**
     * 保存信息
     * @param Request $request
     */
    public function saveSelfInfo(Request $request)
    {
        $params = $request->param();
        $params['idnum'] = session('uid');
        $avatar = $request->file('avatar');
//        上传头像
        if (!empty($avatar)) {
            $res = Functions::uploads($avatar, Cons::UPLOAD_USER_AVATAR_PATH);
            if (!$res) {
                Functions::logs(session('uname') . '<>' . session('uid') . '用户头像上传失败' . var_export($res, true));
                $params['avatar'] = 'tmp.jpg';
            } else {
                $params['avatar'] = $res->getSaveName();
            }
        }
//        保存数据
//        判断是新增还是更新信息
        $userC = Factory::getOperObj('userCredit');
        $res = $userC->getUserDetailByUid(session('uid'));
        $res = empty($res) ? $userC->saveOne($params) : $userC->updateByUid(session('uid'), $params);
        if (!$res) {
            return $this->error('操作失败', 'index/user/selfInfo');
        }
        return $this->success('操作成功', 'index/user/selfInfo');
    }

    /**
     * 收货地址页
     */
    public function receiveAddrs()
    {
//        查询已有的收货地址
        $addr = Factory::getOperObj('addr');
        $addrs = $addr->getReceiveAddrs(session('uid'));
        $addrs = empty($addrs) ? [] : Functions::dataSetToArray($addrs);

        $location = Factory::getOperObj('location');
        //        获取省份信息
        $provinces = $location->getProvinces();
        $this->assign('provinces', $provinces);
//        省市代码转换
        foreach($addrs as &$item) {
            $item['str'] = Functions::addrCodeToWord($item['apid'], $item['acid'], $item['aaid']);
        }
        $this->assign('addrs', $addrs);
        return $this->fetch();
    }

    /**
     * 保存收货地址
     */
    public function saveReceiveAddrs(Request $request)
    {
        $params = $request->param();
        $params['uid'] = session('uid');
        $addr = Factory::getOperObj('addr');

        $res = isset($params['addrId']) ? $addr->updateById($params, $params['addrId']) : $addr->saveOne($params);
        return $res ? $this->success('操作成功', 'index/user/receiveAddrs') : $this->error('操作失败', 'index/user/receiveAddrs');
    }

}