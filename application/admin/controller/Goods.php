<?php
/**
 * 物品管理相关
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/13
 * Time: 15:35
 */

namespace app\admin\controller;
use app\common\Cons;
use app\common\Error;
use app\common\Factory;
use app\common\Functions;
use think\Request;

class Goods extends Base
{
    /**
     * 商品总体情况
     * 折线图表展示
     */
    public function overallSituation()
    {

    }

    /**
     *已审核商品列表
     */
    public function goodsList(Request $request)
    {
        $goods = Factory::getOperObj('goods');

        $page = (int)$request->param('page', 1);
        $totalCounts = $goods->getTotalCountsByStatus(0);
        $limit = 10;
        $totalPage = ceil($totalCounts / $limit);
        $unauditGoods = $goods->getGoodsList($page,$limit);
        $this->assign('totalNum', $totalCounts);
        $this->assign('totalpage', $totalPage);
        if (empty($unauditGoods)) {
            $this->assign('goodslist', $unauditGoods);
            return $this->fetch();
        }
        $imgs = Factory::getOperObj('gimgs');
        $user = Factory::getOperObj('user');
        $category = Factory::getOperObj('category');
        $unauditGoods = Functions::dataSetToArray($unauditGoods);
        foreach ($unauditGoods as &$item) {
            //        获取闲物的一张图片
            $img = $imgs->getImgsByGid($item['id'], 0);
            if (!empty($img)) {
                $item['img'] = $img;
            }
//            获取发布人
            $u = $user->getUserAccountById($item['uid']);
            if (!empty($u)) {
                $item['uid'] = $u->getData()['loginname'];
            } else {
                $item['uid'] = '佚名';
            }
//            获取所属分类
            $catArr = explode('-', $item['cid']);
            array_shift($catArr);
            $catStr = '';
            foreach ($catArr as $value) {
                $cat = $category->getById($value);
                if (!empty($cat)) {
                    $catStr .= $cat->getData()['catnameB'] . '>';
                }
            }
            $item['cid'] = rtrim($catStr, '>');
        }
//        dump($unauditGoods);exit;
//        图片目录
        $this->assign('imgpath', '/ExchangeGit/public/static/goods/upload/');
        $this->assign('goodslist', $unauditGoods);
        return $this->fetch();
    }

    /**
     * 待审核列表
     */
    public function auditList(Request $request)
    {
        $goods = Factory::getOperObj('goods');

        $page = (int)$request->param('page', 1);
        $totalCounts = $goods->getTotalCountsByStatus(0);
        $limit = 10;
        $totalPage = ceil($totalCounts / $limit);
        $unauditGoods = $goods->getUnauditGoods($page,$limit);
        $this->assign('totalNum', $totalCounts);
        $this->assign('totalpage', $totalPage);
        if (empty($unauditGoods)) {
            $this->assign('unauditGoods', $unauditGoods);
            return $this->fetch();
        }
        $imgs = Factory::getOperObj('gimgs');
        $user = Factory::getOperObj('user');
        $category = Factory::getOperObj('category');
        $unauditGoods = Functions::dataSetToArray($unauditGoods);
        foreach ($unauditGoods as &$item) {
            //        获取闲物的一张图片
            $img = $imgs->getImgsByGid($item['id'], 0);
            if (!empty($img)) {
                $item['img'] = $img;
            }
//            获取发布人
            $u = $user->getUserAccountById($item['uid']);
            if (!empty($u)) {
                $item['uid'] = $u->getData()['loginname'];
            } else {
                $item['uid'] = '佚名';
            }
//            获取所属分类
            $catArr = explode('-', $item['cid']);
            array_shift($catArr);
            $catStr = '';
            foreach ($catArr as $value) {
                $cat = $category->getById($value);
                if (!empty($cat)) {
                    $catStr .= $cat->getData()['catnameB'] . '>';
                }
            }
            $item['cid'] = rtrim($catStr, '>');
        }
//        dump($unauditGoods);exit;
//        图片目录
        $this->assign('imgpath', '/ExchangeGit/public/static/goods/upload/');
        $this->assign('unauditGoods', $unauditGoods);
        return $this->fetch();
    }

    /**
     * 审核通过
     */
    public function passAudit(Request $request)
    {
        $id = (int)$request->param('id');
        $goods = Factory::getOperObj('goods');
        $res = $goods->passAudit($id);
        $errCode = $res ? 0 : 1;
        $errArr = Error::getCodeMsgArr($errCode);
        return json($errArr);
    }

    /**
     * 下架闲物列表
     */
    public function theThelvesList()
    {

    }

    /**
     * 下架闲物
     */
    public function theShelves(Request $request)
    {
        $id = (int)$request->param('id');
        $goods = Factory::getOperObj('goods');
        $res = $goods->theShelvesGoods($id);
        $errCode = $res ? 0 : 1;
        $errArr = Error::getCodeMsgArr($errCode);
        return json($errArr);
    }

    /**
     * @annotation测试方法
     * 新增物品
     */
    public function create(Request $request)
    {
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
            $errorCode = 3;
            $errArr = Error::getCodeMsgArr($errorCode);
            $errArr['result'] = $result;
            return json($errArr);
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
            $errArr = Error::getCodeMsgArr(1);
            $errArr['result'] = '添加失败';
            return json($errArr);
        }
//        上传图片
        $file = $request->file('gimgs');
        if (!empty($file)) {
            $saveNameArr = [];
            foreach($file as $key => $item) {
                $res = Functions::uploads($item, Cons::UPLOAD_GOODS_IMG_PATH);
                if (!$res) {
                    Functions::logs('文件上传失败' . var_export($res, true));
                }
                $saveNameArr[$key]['img'] = $res->getSaveName();
                $saveNameArr[$key]['gid'] = $id;
            }
//            把文件信息保存到数据库
            $gim = Factory::getOperObj('gimgs');
            $gim->saveAll($saveNameArr);
        }
        $errArr = Error::getCodeMsgArr(0);
        $errArr['result'] = '添加成功';
        return json($errArr);
    }
}