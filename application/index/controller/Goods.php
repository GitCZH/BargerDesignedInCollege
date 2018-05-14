<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/5/6
 * Time: 0:43
 */
namespace app\index\controller;

use app\common\Cons;
use app\common\Error;
use app\common\Factory;
use app\common\Functions;
use think\Controller;
use think\Request;

class Goods extends Base
{
    /**
     * 闲物详情页
     * @param Request $request
     */
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

    /**
     * 发布闲置闲物页
     */
    public function publishGoods(Request $request)
    {
//        若未录入个人信息，则跳转至录入信息页面
        $userC = Factory::getOperObj('userCredit');
        $userDetail = $userC->getUserDetailByUid(session('uid'));
        if (empty($userDetail)) {
            return $this->error('请先完善个人信息', 'index/user/selfInfo');
        }
        if (empty($request->param())) {
//            获取分类信息
            $category = Factory::getOperObj('category');
            $cats = $category->getAllCats();
            Functions::pasteArrToOptions($cats, $options, 0, 0);
            $this->assign('cats', $options);
//            查询省份信息
            $location = Factory::getOperObj('location');
            $provinces = $location->getProvinces();
            $this->assign('provinces', $provinces);
            return $this->fetch();
        }
//        接收参数
        $params = $request->param();
        $rules = [
            'gname' => 'require|min:2|max:20',
            'isnew' => 'in:0,1',
            'canbuy' => 'in:0,1',
            'cid' => 'require',
            'gpid' => 'number',
            'gcid' => 'require|number',
            'gaid' => 'require|number',
            'gdescribe' => 'require'
        ];
        $msg = [
            'gname.require' => '名称必须',
            'gname.min' => '名称最少2个字符',
            'gname.max' => '名称最多20个字符',
            'isnew.in' => '数据错误',
            'canbuy.in' => '数据错误',
            'cid.require' => '所属分类必须',
            'gpid.number' => '数据错误',
            'gcid.number' => '数据错误',
            'gcid.require' => '市区信息必须',
            'gaid.number' => '数据错误',
            'gaid.require' => '区县信息必须',
            'gdescribe.require' => '描述必须'
        ];
//        自动验证
        $result = $this->validate($params, $rules, $msg);
        if ($result !== true) {
            return $this->error($result);
        }
//      获取地区连续信息
        $location = Factory::getOperObj('location');
        $province = $location->getProvinceByPid($params['gpid']);
        $city = $location->getCityByCid($params['gcid']);
        $area = $location->getAreaByAid($params['gaid']);
        $params['glocation'] = $province['province'] . $city['city'] . $area['area'];
        $params['uid'] = session('uid');
//        保存到数据库
        $goods = Factory::getOperObj('goods');
        $id = $goods->addGoods($params);
        if (!$id) {
            return $this->error('添加失败');
        }
//        上传图片
        $file = $request->file('gimgs');
        if (!empty($file)) {
            $saveNameArr = [];
            foreach($file as $key => $item) {
                $res = Functions::uploads($item, Cons::UPLOAD_GOODS_IMG_PATH);
                if (!$res) {
                    Functions::logs('文件上传失败' . var_export($res, true));
                } else {
                    $saveNameArr[$key]['img'] = $res->getSaveName();
                    $saveNameArr[$key]['gid'] = $id;
                }
            }
//            把文件信息保存到数据库
            $gim = Factory::getOperObj('gimgs');
            $gim->saveAll($saveNameArr);
        }
        return $this->success('添加成功');
    }
}