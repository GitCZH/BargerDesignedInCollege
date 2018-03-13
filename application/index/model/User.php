<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/7
 * Time: 22:20
 */
namespace app\index\model;

use app\common\Functions;
use think\Model;

class User extends Model
{
    protected $table = 'ex_uaccount';
    protected $autoWriteTimestamp = true;
    protected $deleteTime = 'delete_time';

    public function setLoginpwdAttr($val) {
        return md5($val);
    }

    public function getIsvalidAttr($val) {
        $status = [
            '封禁',
            '正常'
        ];
        return $status[$val];
    }

    public function getUserPaging($condition = [], $page = 0, $limit = 10) {
        return $this->where($condition)
            ->page($page, $limit)
            ->select();
    }
    public function delUserById($uid) {
        $res = $this->save(['isvalid'=>0], ['id'=>$uid]);
        var_dump($res);
    }

    public function getUserById($uid) {
        return $this->get($uid) === null ? null : $this->getData();
    }

    public function checkEmailNew($email) {
        $res = $this->where(['loginemail' => $email])->find();
        return $res === null ? true : false;
    }

    public function getChartData()
    {
        $timestampArr = Functions::getEachMonthTimestamp();

        $userData = [];
        foreach ($timestampArr as $value) {
//            总数
            $res = $this
                ->where("isvalid = 1 and create_time > {$value['start']} and create_time < {$value['end']}")
                ->select();
            if (!$res) {
                $userData[] = 0;
            } else {
                $userData[] = count($res);
            }
        }
        return json_encode($userData);
    }
}
