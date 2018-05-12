<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/6
 * Time: 0:43
 */
namespace app\index\controller;

use app\common\Error;
use app\common\Factory;
use app\common\Functions;
use think\Controller;
use think\Request;

class Goods extends Base
{
    public function detail(Request $request)
    {
        $gid = (int)$request->param('gid');
        if ($gid == 0) {
            $this->redirect('index/unexcept/to404');
        }
        $goods = Factory::getOperObj('goods');
        $gimg = Factory::getOperObj('gimgs');
        $ucredit = Factory::getOperObj('userCredit');
//        查询闲物详情
        $goodsInfo = $goods->getGoodsByGid($gid)->getData();
//        查询闲物图片
        $gimgs = $gimg->getImgsByGid($gid);
//        查询闲物主人信息
        $userInfo = $ucredit->getUserDetailByUid($goodsInfo['uid']);

//      更新浏览数
        $goods->incScanNum($gid);

        if (empty($userInfo)) {
            $userInfo['sex'] = 0;
            $userInfo['nickname'] = 'nick';
            $userInfo['credit'] = 80;
            $userInfo['avatar'] = 'tmp.jpg';
            $userInfo['phone'] = '18659696687';
        }
        $this->assign('goodsInfo', $goodsInfo);
        $this->assign('gimgs', $gimgs);
        $this->assign('user', $userInfo);
        return $this->fetch();
    }

    /**
     * 获取用户发布的未被交易的闲置物品
     */
    public function getLeisureGoods(Request $request)
    {
        $uid = (int)$request->param('uid');
        $goods = Factory::getOperObj('goods');
        $leisureGoods = $goods->getLeisureGoods($uid);
        $code = empty($leisureGoods) ? 2 : 0;
        $errArr = Error::getCodeMsgArr($code);
        if ($code == 0) {
            $leisureGoods = Functions::dataSetToArray($leisureGoods);
            $gimg = Factory::getOperObj('gimgs');
            foreach($leisureGoods as &$item) {
//                查找一张图片
                $item['img'] = $gimg->getImgsByGid($item['id'], 0);
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            }
            $errArr['result'] = $leisureGoods;
        }
        return json($errArr);
    }
}