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
use app\common\LittleTools;
use think\Config;
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
     *商品列表
     */
    public function goodsList()
    {

    }

    /**
     * 待审核列表
     */
    public function auditList()
    {

    }

    /**
     * 下架列表
     */
    public function theShelves()
    {

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