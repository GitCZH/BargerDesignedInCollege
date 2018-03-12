<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/12
 * Time: 21:23
 */
namespace app\admin\controller;
class Jump extends Base
{
    public function toAdminLogin () {
        $this->error('请登录', 'admin/user/login');
    }
}