<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/22
 * Time: 16:45
 */
namespace app\admin\controller;
use think\Request;
use app\common\Error;
use app\common\Factory;

class Location extends Base
{
    public function getCities(Request $request)
    {
        $pid = (int)$request->param('pid');
        $location = Factory::getOperObj('location');
        $cities = $location->getCities($pid);
        $errCode = empty($cities) ? 2 : 0;
        $errArr = Error::getCodeMsgArr($errCode);
        $errArr['result'] = $cities;
        return json($errArr);
    }

    public function getAreas(Request $request)
    {
        $cid = (int)$request->param('cid');
        $location = Factory::getOperObj('location');
        $areas = $location->getAreas($cid);
        $errCode = empty($areas) ? 2 : 0;
        $errArr = Error::getCodeMsgArr($errCode);
        $errArr['result'] = $areas;
        return json($errArr);
    }
}