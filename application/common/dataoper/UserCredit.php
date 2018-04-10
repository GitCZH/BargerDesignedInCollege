<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/20
 * Time: 21:50
 */
namespace app\common\dataoper;
class UserCredit
{

    private static $userCreditObj = null;
    private static function getUserModelObj()
    {
        if (!is_null(self::$userCreditObj)) {
            return self::$userCreditObj;
        }
        self::$userCreditObj = new \app\common\model\User();
        return self::$userCreditObj;
    }

    /**
     * 根据uid获取用户的详细信息
     * @param $uid
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getUserDetailByUid ($uid) {
        $userCreditObj = self::getUserModelObj();
        return $userCreditObj->where(['id' => $uid])
            ->find();
    }
}