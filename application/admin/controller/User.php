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
        $business = new \app\common\User();
        if ($business->checkLogin($params)) {
            $data['errCode'] = 0;
            $data['errMsg'] = 'success';
        } else {
            $data['errCode'] = -1;
            $data['errMsg'] = 'wrong';
        }
        return json_encode($data);
    }

    public function exitLogin()
    {
        $business = new \app\common\User();
        if ($business->exitLogin()) {
            $this->success('成功退出', 'user/login');
        }
    }
    public function showUserDetail (Request $request)
    {
        $uid = $request->param();
        $ucredit = new UserCredit();
        var_dump($ucredit->getUserDetailByUid($uid));
    }

    /**
     * 用户统计
     * @return mixed
     */
    public function countUser()
    {
        $user = new \app\index\model\User();
        $data = $user->getChartData();
        $this->assign('totalNum', $data);
        return $this->fetch();
    }

    public function userList(Request $request)
    {
        $user = new \app\index\model\User();
        $userCredit = new UserCredit();
        $page = $request->param('page');
        if ($page < 0) {
            $page = 0;
        }
        $limit = 3;
//        获取总页数
        $counts = $user->where(['isvalid' => 1])->count();
        $totalPage = ceil($counts / $limit);
        $res = $user->where(['isvalid' => 1])->order('create_time desc')->page($page, $limit)->select();
        if (!$res) {
            $this->assign('userlist', []);
        } else {
            foreach ($res as &$v) {
                $v = $v->getData();
                if (null !== $uc = $userCredit->getUserDetailByUid($v['id'])) {
                    $v['realname'] = $uc->getData()['realname'];
                } else {
                    $v['realname'] = '佚名';
                }
            }
            $this->assign('userlist', $res);
        }
        $this->assign('totalpage', $totalPage);
        return $this->fetch();
    }

    public function searchUser()
    {

    }

    public function delUser(Request $request)
    {
        $uid = $request->param('uid');
        return json_encode($uid);
    }
}