<?php
/**
 * 工厂类
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/17
 * Time: 10:34
 */
namespace app\common;

class Factory
{
//    模型对象池
    private static $modelObj = [];
//    数据操作对象池
    private static $operObj = [];

    public static function getModelObj($modelName)
    {
        if (!empty($modelName)) {
            $modelName = '\app\common\model\\' . ucfirst($modelName);
        }
        if (!empty(self::$modelObj)) {
            $catchObj = array_map(function($obj) use ($modelName){
                if ($obj instanceof $modelName) {
                    return $obj;
                }
            }, self::$modelObj);

            if (!empty($catchObj[0])) {
                return reset($catchObj);
            }
        }

        if (!empty($modelName)) {
            self::$modelObj[] = new $modelName;
        }
        return end(self::$modelObj);
    }

    public static function getOperObj($operName)
    {
        if (!empty($operName)) {
            $operName = '\app\common\dataoper\\' . ucfirst($operName);
        }
        if (!empty(self::$operObj)) {
            $catchObj = array_map(function($obj) use ($operName){
                if ($obj instanceof $operName) {
                    return $obj;
                }
            }, self::$operObj);
            if (!empty($catchObj[0])) {
                return reset($catchObj);
            }
        }
        if (!empty($operName)) {
            self::$operObj[] = new $operName;
        }
        return end(self::$operObj);
    }
}