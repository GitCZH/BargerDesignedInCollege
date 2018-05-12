<?php
/**
 * 用户基本信息相关类
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/12
 * Time: 14:56
 */
namespace app\index\controller;
use app\common\Error;
use app\common\Factory;
use app\common\Functions;

class Userinfo extends Base
{
    /**
     * 获取用户的收货地址
     * @param $uid
     */
    public function getReceiveAddrs($uid)
    {
        $addr = Factory::getOperObj('addr');
        $addrData = $addr->getReceiveAddrs($uid);
        $code = empty($addrData) ? 2 : 0;
        $errArr = Error::getCodeMsgArr($code);
        if (!$code) {
//            $errArr['result'] = Functions::dataSetToArray($addrData);
//            将省市代码转为文字
            foreach(Functions::dataSetToArray($addrData) as $key => $item) {
                $errArr['result'][$key] = Functions::addrCodeToWord($item['apid'], $item['acid'], $item['aaid']);
                $errArr['result'][$key]['id'] = $item['id'];
                $errArr['result'][$key]['detail'] = $item['adetail'];
            }
        }
        return json($errArr);
    }
}