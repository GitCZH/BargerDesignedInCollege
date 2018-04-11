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
        $business = new \app\common\controller\UserCheck();
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
        $ucredit = new \app\common\model\UserCredit();
        var_dump($ucredit->getUserDetailByUid($uid));
    }

    /**
     * 用户统计
     * @return mixed
     */
    public function countUser()
    {
        $user = new \app\common\dataoper\User();
        $data = $user->getChartData();
        $this->assign('totalNum', json_encode($data));
        return $this->fetch();
    }

    /**
     * 用户列表
     * @param Request $request
     * @return mixed
     */
    public function userList(Request $request)
    {
        $user = new \app\common\dataoper\User();
        $userCredit = new \app\common\dataoper\UserCredit();
        $page = (int)$request->param('page');
        $key = !empty($request->param('key')) ? $request->param('key') : null;
        $where = '';
        if (!is_null($key)) {
            $where = 'loginname like \'%' . $key . '%\'';
        }
        if ($page < 0) {
            $page = 0;
        }
        $limit = 10;
//        获取总页数

        $where .= !empty($where)  ? ' and isvalid=1' : 'isvalid=1';
        $counts = $user->getAllUserCount(['where' => $where]);
        $totalPage = ceil($counts / $limit);
        $res = $user->getPagingUser([
            'where' => $where,
            'order' => 'create_time desc'
        ], $page, $limit);
        if (!$res) {
            $this->assign('userlist', []);
        } else {
            foreach ($res as &$v) {
                $v = $v->getData();
                if (null !== $uc = $userCredit->getUserDetailByUid($v['id'])) {
                    $v['detail'] = $uc->getData();
                } else {
                    $v['detail'] = [];
                }
            }
            $this->assign('userlist', $res);
        }
        $this->assign('totalpage', $totalPage);
        $this->assign('totalNum', $counts);
        if (!is_null($key)) {
            $this->assign('keyWord', $key);
        }
        return $this->fetch();
    }

    /**
     * 删除用户接口
     * @param Request $request
     * @return string
     */
    public function delUser(Request $request)
    {
        $uid = (int)$request->param('uid');
        $user = new \app\common\dataoper\User();
        $res = $user->delUserByUid($uid);
        $res = ($res !== false) && !empty($res) ? 1 : 0;
        $msg = $res ? 'success' : 'failed';
        return json_encode(['errorCode' => $res, 'errorMsg' => $msg]);
    }

    /**
     * 获取用户详细信息接口
     */
    public function getUserDetailed(Request $request)
    {
        $uid = (int)$request->param('uid');
        $user = new \app\common\dataoper\UserCredit();
        $detailed = $user->getUserDetailByUid($uid);
        $errCode = 1;
        $errMsg = '详细信息如下！';
        if (empty($detailed)) {
            $errCode = 0;
            $errMsg = '暂无详细信息';
        }
        return json_encode(
            [
                'errorCode' => $errCode,
                'result' => empty($detailed) ? '' : $detailed,
                'errorMsg' => $errMsg
            ]
        );
    }

    /**
     * 生成测试账户
     */
    public function generateAccounts()
    {
        $res = \app\common\dataoper\User::generateAccounts();

        if (!empty($res)) {
            $this->success('创建成功', 'user/userlist', '', 1);
        }
        $this->error('创建失败', 'admin/index/index', '', 1);
    }
}