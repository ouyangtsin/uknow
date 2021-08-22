<?php
// +----------------------------------------------------------------------
// | UKnowing [You Know] 简称 UK
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------

namespace app\common\library\helper;

use DateTime;
use DateTimeZone;

/**
 * 日期时间处理类
 */
class DateHelper
{
    /**
     * @param $time
     * @return false|string
     * 获取当前日期时间
     */
   public static function intToDate($time){
       return date('Y-m-d H:i:s',$time);
   }
    /**
     * 日期转时间戳
     *
     * @param $value
     * @return false|int
     */
    public static function dateToInt($value)
    {
        if (empty($value)) {
            return $value;
        }

        if (!is_numeric($value)) {
            return strtotime($value);
        }

        return $value;
    }
    /**
     * @param $time
     * @return string
     * 多少天前
     */
   public static function timeAgo($time){
       //当前时间的时间戳
       $nowTime = strtotime(date('Y-m-d H:i:s'),time());
       //之前时间参数的时间戳
       $postTime = strtotime($time);
       //相差时间戳
       $countTime = $nowTime - $postTime;
       //进行时间转换
       if($countTime<=10){
           return '刚刚';
       }else if($countTime>10 && $countTime<=30){
           return '刚才';
       }else if($countTime>30 && $countTime<=60){
           return '刚一会';
       }else if($countTime>60 && $countTime<=120){
           return '1分钟前';
       }else if($countTime>120 && $countTime<=180){
           return '2分钟前';
       }else if($countTime>180 && $countTime<3600){
           return intval(($countTime/60)).'分钟前';
       }else if($countTime>=3600 && $countTime<3600*24){
           return intval(($countTime/3600)).'小时前';
       }else if($countTime>=3600*24 && $countTime<3600*24*2){
           return '昨天';
       }else if($countTime>=3600*24*2 && $countTime<3600*24*3){
           return '前天';
       }else if($countTime>=3600*24*3 && $countTime<=3600*24*20){
           return intval(($countTime/(3600*24))).'天前';
       }else{
           return $time;
       }
   }

    /**
     * 格式化 UNIX 时间戳为人易读的字符串
     * @param    int    Unix 时间戳
     * @param    mixed $local 本地时间
     * @return    string    格式化的日期字符串
     */
    public static function humanDate($remote, $local = null)
    {
        $timeDiff = (is_null($local) || $local ? time() : $local) - $remote;
        $chunks = array(
            array(60 * 60 * 24 * 365, 'year'),
            array(60 * 60 * 24 * 30, 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24, 'day'),
            array(60 * 60, 'hour'),
            array(60, 'minute'),
            array(1, 'second')
        );

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($timeDiff / $seconds)) != 0) {
                break;
            }
        }
        return lang("%d {$name}%s ago", $count, ($count > 1 ? 's' : ''));
    }


    /***
     * 日期筛选格式化
     * @param $dateRange
     * @return array
     */
    public static function dateRange($dateRange): array
    {
        if ($dateRange) {
            $dateRange = explode(" 至 ", $dateRange);
        }
        if (is_array($dateRange) && count($dateRange) == 2) {
            $dateRange[0] = strtotime($dateRange[0]);
            $dateRange[1] = strtotime($dateRange[1]) + 24 * 60 * 60 - 1;
        }
        return $dateRange;
    }
}
