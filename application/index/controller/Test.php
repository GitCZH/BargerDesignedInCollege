<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/2/22
 * Time: 20:36
 */
namespace app\index\controller;
use think\Controller;

class Test extends Controller {
    public function index() {
        return $this->fetch();
    }
}