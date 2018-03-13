<?php
/**
 * Created by PhpStorm.
 * User: ZhongHua
 * Date: 2018/3/13
 * Time: 23:05
 */
namespace app\common;

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
}