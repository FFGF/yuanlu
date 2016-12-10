<?php
//源庐的项目

function getCategory($vo){
    foreach($vo as $key=>$value){
        if($key == 'category'){
            return $value['category'];
        }
    }
}

function rmbToDollar($value,$rate){
    $value = str_replace(',','',$value);
    return number_format($value/$rate,2);
}

function formatWeek($value){
    switch($value){
        case 1:
            return "星期一";
        case 2:
            return "星期二";
        case 3:
            return "星期三";
        case 4:
            return "星期四";
        case 5:
            return "星期五";
        case 6:
            return "星期六";
        case 0:
            return "星期日";
    }
}
//删除数组中指定元素
function array_remove(&$arr, $offset)
{
    array_splice($arr, $offset, 1);
}
//为了实现日报日历查看，需要格式化日期
function formatdailyPaperDate($year,$month){
    $day = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $time_stamp = mktime(0,0,0,$month,0,$year);
    $week = date('w',mktime(0,0,0,$month,1,$year));
    $first_day = date('Y-m-d',$time_stamp-24*3600*--$week);
    $last_week = date('w',mktime(0,0,0,$month,$day,$year));
    $last_day = date('Y-m-d',mktime(0,0,0,$month,$day,$year)+24*3600*(6-$last_week));

    $day_daily_paper = [];
    for(;strtotime($first_day)<=strtotime($last_day);){
        $day_daily_paper[] = $first_day;
        $first_day = date('Y-m-d',strtotime("$first_day +1 day"));
    }

    return $day_daily_paper;
}
