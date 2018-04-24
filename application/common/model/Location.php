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

    /**
     * 获取单个省份
     * @param $pid
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getProvinceByPid($pid)
    {
        return Db::table('provinces')->where(['provinceid' => $pid])->find();
    }

    /**
     * 获取单个城市
     * @param $cid
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getCityByCid($cid)
    {
        return Db::table('cities')->where('cityid', $cid)->find();
    }

    /**
     * 获取单个区县
     * @param $aid
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getAreaByAid($aid)
    {
        return Db::table('areas')->where('areaid', $aid)->find();
    }
}