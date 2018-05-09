<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/13
 * Time: 23:05
 */
namespace app\common;

use think\File;
use think\Log;
use think\Request;

class Functions
{
    public static function getEachMonthTimestamp()
    {
        $year = date('Y');
        $days = [28, 29];
        if ($year % 100 == 0) {
            if ($year % 400 == 0) {
                $is = 1;
            } else {
                $is = 0;
            }
        } else {
            if ($year % 4 == 0) {
                $is = 1;
            } else {
                $is = 0;
            }
        }
        $day = $days[$is];
        $febStr = "Y-02-{$day} 00:00:00";
        $data = [
            [
                'start'=>strtotime(date('Y-01-01 00:00:00')),
                'end'  => strtotime(date('Y-01-31 00:00:00'))
            ],
            [
                'start'=>strtotime(date('Y-02-01 00:00:00')),
                'end'  => strtotime(date($febStr))
            ],
            [
                'start'=>strtotime(date('Y-03-01 00:00:00')),
                'end'  => strtotime(date('Y-03-31 00:00:00'))
            ],
            [
                'start'=>strtotime(date('Y-04-01 00:00:00')),
                'end'  => strtotime(date('Y-04-30 00:00:00'))
            ],
            [
                'start'=>strtotime(date('Y-05-01 00:00:00')),
                'end'  => strtotime(date('Y-05-31 00:00:00'))
            ],
            [
                'start'=>strtotime(date('Y-06-01 00:00:00')),
                'end'  => strtotime(date('Y-06-30 00:00:00'))
            ],
            [
                'start'=>strtotime(date('Y-07-01 00:00:00')),
                'end'  => strtotime(date('Y-07-31 00:00:00'))
            ],
            [
                'start'=>strtotime(date('Y-08-01 00:00:00')),
                'end'  => strtotime(date('Y-08-31 00:00:00'))
            ],
            [
                'start'=>strtotime(date('Y-09-01 00:00:00')),
                'end'  => strtotime(date('Y-09-30 00:00:00'))
            ],
            [
                'start'=>strtotime(date('Y-10-01 00:00:00')),
                'end'  => strtotime(date('Y-10-30 00:00:00'))
            ],
            [
                'start'=>strtotime(date('Y-11-01 00:00:00')),
                'end'  => strtotime(date('Y-11-30 00:00:00'))
            ],
            [
                'start'=>strtotime(date('Y-12-01 00:00:00')),
                'end'  => strtotime(date('Y-12-31 00:00:00'))
            ]
        ];

        return $data;
    }

    /**
     * 日志记录管理 【复述日志记录方式】
     */
    public static function logs($msg, $type = 'log', $drive = 'File', $path = APP_PATH . 'logs')
    {
        Log::init([
            'type' => $drive,
            'path' => $path
        ]);
        Log::record($msg, $type);
    }

    /**
     * 文件上传方法
     * @return false | File
     */
    public static function uploads
    (
        File $file,
        $path = ROOT_PATH . 'public/static/avatars/',
        array $validate = ['size' => 2097152, 'ext' => 'jpg,jpeg,png,gif']
    )
    {
        return $file->validate($validate)->move($path);
    }

    /**
     * select数据集转Data数组
     */
    public static function dataSetToArray($dataSet)
    {
        if (empty($dataSet)) {
            return [];
        }
        array_walk($dataSet, function(&$item){
            $item = $item->getData();
        });
        return $dataSet;
    }

    /**
     * 拼接select分类数据
     */
    public static function pasteArrToOptions($data, &$str = '', $fids = null, $times = 0, $option = '<option value="%s">%s%s</option>')
    {
        if (empty($data)) {
            return '';
        }

        foreach($data as $item) {
            if (is_null($fids)) {
//                value值需要用到fid值时
                $value = $item['id'];
            } else {
                $value = $fids . '-' . $item['id'];
            }
            $str .= sprintf($option, $value, str_repeat('-', $times), $item['catnameB']);
            if (isset($item['child'])) {
                if (is_null($fids)) {
                    self::pasteArrToOptions($item['child'], $str, $times + 1);
                } else {
                    self::pasteArrToOptions($item['child'], $str, $fids . '-' . $item['id'], $times + 1);
                }
            }
        }
    }

    /**
     * 补充查询
     */

    /**
     * 测试账号生成规则
     */
    public static function accountsRule($num = 20)
    {
        $accountArr = [];
        $pwd = '123456';
        for($i = 0; $i < $num; $i++) {
            $preName = 'ttttest_';
            $unique = uniqid($preName, true);
            $preName = $unique;
            $preEmail = $unique . '@1.com';
            $accountArr[] = [
                'loginname' => $preName,
                'loginemail' => $preEmail,
                'loginpwd' => $pwd
            ];
        }
        return $accountArr;
    }
}