<?php
/**
 * 多级分类管理
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/13
 * Time: 15:45
 */
namespace app\admin\controller;
use app\common\Error;
use app\common\Factory;
use think\Request;

class Category extends Base
{
    /**
     * 新增分类
     */
    public function create(Request $request)
    {
        $catBigName = $request->param('bigName');
        $catSmallName = $request->param('smallName', ' ');
        $catFid = (int)$request->param('fid');

        if (empty($catBigName)) {
            return json(['errorCode' => 1, 'errorMsg' => '分类名不能为空']);
        }
        $category = Factory::getOperObj('category');
        $res = $category->create($catBigName, $catSmallName, $catFid);
        $errCode = $res ? 0 : 2;
        $errMsg = $res ? '创建成功' : '创建失败';
        $msg = Error::getCodeMsg($errCode);

        return json(['errorCode' => $errCode, 'errorMsg' => $errMsg]);
    }

    /**
     * 修改分类
     */
    public function edit(Request $request)
    {
        $catBigName = $request->param('bigName');
        $catSmallName = $request->param('smallName', ' ');
        $catFid = (int)$request->param('fid');
        $id = (int)$request->param('id');

        if (empty($catBigName)) {
            return json(['errorCode' => 1, 'errorMsg' => '分类名不能为空']);
        }
        $category = Factory::getOperObj('category');
        $res = $category->edit($id, ['catnameB' => $catBigName]);

        $errCode = $res ? 0 : 1;
        $errArr = Error::getCodeMsgArr($errCode);

        return json($errArr);
    }

    /**
     * 删除分类
     */
    public function del(Request $request)
    {

    }
}