<?php
/**
 * 小工具类
 * User: ZhongHua
 * Date: 2018/4/22
 * Time: 22:25
 */
namespace app\common;
class LittleTools
{
    /**
     * 计算PSI 生物钟规律
     * 0 高潮期
     * 1 临界期
     * 2 低潮期
     */
    public static function soothSaying($year, $month, $day)
    {
        $bornTime =  strtotime($year . '-'. $month . '-' . $day);
        $totalDays = ceil((time() - $bornTime) / 60 * 60 * 24);

        $i = 16.5;;
        $s = 14;
        $p = 11.5;

        $modi = $totalDays % 33;
        $mods = $totalDays % 28;
        $modp = $totalDays % 22;

//        智力周期
        if ($modi == 0 || abs($modi - $i) < $i * 0.1) {
            $stayi = 1;
        } else {
            $stayi = ($modi > $i) ? 0 : 2;
        }

//        情绪周期
        if ($mods == 0 || abs($mods - $s) < $s * 0.1) {
            $stays = 1;
        } else {
            $stays = ($mods > $s) ? 0 : 2;
        }

//        体力周期
        if ($modp == 0 || abs($modp - $p) < $p * 0.1) {
            $stayp = 1;
        } else {
            $stayp = ($modp > $p) ? 0 : 2;
        }

        return ['physical' => $stayp, 'sensitive' => $stays, 'intelligence' => $stayi];
    }
}