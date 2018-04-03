<?php
/**
 * user 子系统相关业务逻辑
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/20
 * Time: 21:25
 */
namespace app\common\dataoper;
class User
{
    private static $userObj = null;
    public static function getUserModelObj()
    {
        if (!is_null(self::$userObj)) {
            return self::$userObj;
        }
        self::$userObj = new \app\common\model\User();
        return self::$userObj;
    }
    public static function checkLogin($params)
    {
        return true;
    }
    public function exitLogin()
    {
        UserCheck::exitLogin();
        session('uid', null);
        return true;
    }
    public function checkReg()
    {
        if (UserCheck::checkReg()) {
            return true;
        }
    }

    /**
     * 获取用户账户信息
     * @param $id
     */
    public function getUserAccountById($id)
    {

    }

    /**
     * 获取用户详细信息
     * @param $id
     */
    public function getUserDetailById($id)
    {

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
    public function getUserCharData()
    {

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
        return json_encode($userData);
    }
}