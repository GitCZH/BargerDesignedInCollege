<?php
/**
 * 省份地区的查询构造器
 * User: ZhongHua
 * Date: 2018/4/22
 * Time: 15:26
 */
namespace app\common\model;

use think\Db;

class Location
{
    /**
     * 获取所有省份
     */
    public function getProvinces()
    {
        return Db::table('provinces')->select();
    }

    /**
     * 获取所有市区
     */
    public function getCities($pid)
    {
        return Db::table('cities')->where('provinceid', $pid)->select();
    }

    /**
     * 获取所有区县
     */
    public function getAreas($cid)
    {
        return Db::table('areas')->where('cityid', $cid)->select();
    }
}