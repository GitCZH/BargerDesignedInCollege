<?php
namespace app\index\controller;
use app\common\Factory;
use app\common\Functions;
use think\Controller;
class Index extends Base
{
    public function indexA()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ad_bd568ce7058a1091"></think>';
    }

    /**
     * 首页action
     */
    public function index() {
//        获取登录用户的信息
        $uCredit = Factory::getOperObj('userCredit');
        $uid = empty(session('uid')) ? cookie('uid') : session('uid');
        $userInfo = $uCredit->getUserDetailByUid($uid);
        $userInfo = empty($userInfo) ? [] : $userInfo->getData();
        if (!empty(session('uid') && empty($userInfo))) {
            return $this->success('完善个人信息', 'index/user/selfInfo');
        } else {
            $this->assign('userInfo', $userInfo);
        }

//        查询最新上架的闲物
        $goods = Factory::getOperObj('goods');
//        dump($goods->getLatestGoods());exit;
        $lastedGoods = $goods->getLatestGoods();
//        补充第一张图片
        if (!empty($lastedGoods)) {
            $gimg = Factory::getOperObj('gimgs');
            foreach($lastedGoods as &$item) {
                $img = $gimg->getImgsByGid($item['id'], 0);
                $item['img'] = (empty($img) ? 'tmp.jpg' : $img);
            }
        }
//        dump($lastedGoods);exit;
        $this->assign('lastedGoods', $lastedGoods);

//        查询同城的闲物
        $sameCityGoods = empty($userInfo) ? [] : $goods->getSameCityGoods($userInfo['upid'], $userInfo['ucid']);
//        dump($sameCityGoods);exit;
        $this->assign('sameCityGoods', $sameCityGoods);


        return $this->fetch();
    }
}
