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
    return $value * $rate;
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