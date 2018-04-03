<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/20
 * Time: 21:50
 */
namespace app\common;
class UserCredit
{

    private static $userCreditObj = null;
    public static function getUserModelObj()
    {
        if (!is_null(self::$userCreditObj)) {
            return self::$userCreditObj;
        }
        self::$userCreditObj = new \app\common\model\User();
        return self::$userCreditObj;
    }

    public function getUserDetailByUid ($uid) {
        $userCreditObj = self::getUserModelObj();
        return $userCreditObj->where(['id' => $uid])
            ->find();
    }
}