<?php
/**
 * 业务判断细节  --  设计调用 model 中的数据操作
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/20
 * Time: 21:32
 */
namespace app\common\controller;
use app\common\dataoper\User;

class UserCheck
{
    public function checkLogin($params)
    {
//        调用数据相关操作方法
        return User::checkLogin($params);
    }
    public static function exitLogin()
    {
        return true;
    }
    public static function checkReg()
    {
        return true;
    }
}