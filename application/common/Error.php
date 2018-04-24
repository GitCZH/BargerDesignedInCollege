<?php
/**
 * 错误代码和提示信息类
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/4/17
 * Time: 11:11
 */
namespace app\common;
class Error
{
    private $code = 0;
    private $msg = '';
    private static $errArr = [
        0 => '成功',
        1 => '失败',
        2 => '空数据',
        3 => '验证失败'
    ];

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
        self::$errArr[$this->getCode()] = $this->getMsg();
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getErrArr()
    {
        return self::$errArr;
    }

    /**
     * @param array $errArr
     */
    public function setErrArr($errArr)
    {
        self::$errArr = array_merge(self::$errArr, $errArr);
    }

    public static function getCodeMsg($code)
    {
        return self::$errArr[$code];
    }

    public static function getCodeMsgArr($code)
    {
        return [
            'errorCode' => $code,
            'errorMsg' => self::getCodeMsg($code)
        ];
    }
}