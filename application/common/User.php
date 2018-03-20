<?php
/**
 * user 子系统相关业务逻辑
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/20
 * Time: 21:25
 */
namespace app\common;
class User
{
    public function checkLogin()
    {
        if (UserCheck::checkLogin()) {
            session('uid', 1);
            return true;
        }
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
    public function getUserList()
    {

    }
    public function getAllUserCount()
    {

    }
    public function getUserCharData()
    {

    }
}