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
    private static $modelObj = null;
    private static $operObj = null;

    public static function getModelObj($modelName)
    {
        if (!is_null(self::$modelObj)) {
            return self::$modelObj;
        }
        if (!empty($modelName)) {
            $modelName = '\app\common\model\\' . ucfirst($modelName);
            self::$modelObj = new $modelName;
        }
        return self::$modelObj;
    }

    public static function getOperObj($operName)
    {
        if (!is_null(self::$operObj)) {
            return self::$operObj;
        }
        if (!empty($operName)) {
            $operName = '\app\common\dataoper\\' . ucfirst($operName);
            self::$operObj = new $operName;
        }
        return self::$operObj;
    }
}