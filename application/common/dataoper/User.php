<?php
/**
 * user 子系统相关业务逻辑
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/20
 * Time: 21:25
 */
namespace app\common\dataoper;
use app\common\Factory;
use app\common\Functions;
use think\Cookie;

class User
{
    private static $userObj = null;
    private static function getUserModelObj()
    {
        if (!is_null(self::$userObj)) {
            return self::$userObj;
        }
        self::$userObj = new \app\common\model\User();
        return self::$userObj;
    }

    /**
     * 添加后台管理成员
     */
    public function addAdmin($name, $email, $pwd)
    {
        $model = Factory::getModelObj('user');
        return $model->allowField(true)->save(
            [
                'loginname' => $name,
                'loginemail' => $email,
                'loginpwd' => $pwd,
                'foradmin' => 1
            ]
        );
    }

    /**
     * 后台管理登录
     */
    public static function checkAdminLogin($name, $pwd)
    {
        $model = Factory::getModelObj('user');

        $res = $model->whereOr('loginname', $name)
            ->whereOr('loginemail', $name)
            ->where('foradmin', 1)
            ->find();
        if (empty($res)) {
            return 2;
        }
        if ($res->getData()['loginpwd'] != $pwd) {
            return 3;
        }
//        保存session值
        session('uid', $res->getData()['id']);
        session('uname', $res->getData()['loginname']);
        return 0;
    }

    /**
     * 验证登录
     * @param $name
     * @param $pwd
     * @return int
     */
    public static function checkLogin($name, $pwd, $remember)
    {
        $model = Factory::getModelObj('user');

        $res = $model->whereOr('loginname', $name)
            ->whereOr('loginemail', $name)
            ->find();
        if (empty($res)) {
            return 2;
        }
        if ($res->getData()['loginpwd'] != $pwd) {
            return 3;
        }
//        保存session值
        session('uid', $res->getData()['id']);
        session('uname', $res->getData()['loginname']);
        if ($remember) {
            Cookie::forever('uid',$res->getData()['id']);
            Cookie::forever('uname',$res->getData()['loginname']);
        }
        return 0;
    }

    /**
     * 退出登录
     * @return bool
     */
    public static function exitLogin()
    {
        session('uid', null);
        session('uname', null);
        return true;
    }

    /**
     * 检测是否有重复的注册邮箱
     * @return bool
     */
    public function checkReg($params)
    {
        $user = self::getUserModelObj();

        return true;
    }

    /**
     * 检测是否是已注册的邮箱
     */
    public function checkEmailNew($email) {
        $model = Factory::getModelObj('user');
        $res = $model->where(['loginemail' => $email])->find();
        return $res === null ? true : false;
    }

    /**
     * 新增账户
     * @param $accounts
     * @return array|false|int
     * @throws \Exception
     */
    public function saveAccount($accounts)
    {
        if (empty($accounts) && !is_array($accounts)) {
            return 0;
        }
        $user = self::getUserModelObj();
        if (is_array(reset($accounts))) {
//            保存多条数据
            return $user->saveAll($accounts);
        }
        return $user->save($accounts);
    }

    /**
     * 根据uid删除用户
     * @param $uid
     * @return int
     */
    public function delUserByUid($uid)
    {
        $user = self::getUserModelObj();
        return $user->where(['id' => $uid])->update(['isvalid' => 0]);
    }

    /**
     * 获取用户账户信息
     * @param $id
     */
    public function getUserAccountById($id)
    {
        $user = self::getUserModelObj();
        return $user->where(['id' => $id])->find();
    }

    /**
     * 获取用户总数
     */
    public function getAllUserCount(array $conditions)
    {
        $user = self::getUserModelObj();
        $user = $this->parseCondition($conditions, $user);
        return $user->count();
    }

    /**
     * @param $conditions 支持 where  order
     * @param $page
     * @param int $limit
     */
    public function getPagingUser(array $conditions, $page, $limit = 10)
    {
        $user = self::getUserModelObj();
        $user = $this->parseCondition($conditions, $user);
        return $user->page($page, $limit)->select();
    }

    /**
     * 解析条件数组
     * @param array $conditions
     * @param \think\Model $obj
     * @return $this|\think\Model
     */
    public function parseCondition(array $conditions, \think\Model $obj)
    {
        if (isset($conditions['where'])) {
            $obj = $obj->where($conditions['where']);
        }
        if (isset($conditions['group'])) {
            $obj = $obj->group($conditions['group']);
        }
        if (isset($conditions['order'])) {
            $obj = $obj->order($conditions['order']);
        }
        return $obj;
    }

    /**
     * 获取eChart数据表数据
     * @return string
     */
    public function getChartData()
    {
        $user = self::getUserModelObj();
        $timestampArr = Functions::getEachMonthTimestamp();

        $userData = [];
        foreach ($timestampArr as $value) {
//            总数
            $res = $user
                ->where("isvalid = 1 and create_time > {$value['start']} and create_time < {$value['end']}")
                ->select();
            if (!$res) {
                $userData[] = 0;
            } else {
                $userData[] = count($res);
            }
        }
        return $userData;
    }

    /**
     * 生成测试账号
     */
    public static function generateAccounts()
    {
        $accountArr = Functions::accountsRule();
        return (new self)->saveAccount($accountArr);
    }
}