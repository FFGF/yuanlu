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
function formatdailyPaperDate($year,$month,$branch_id){
    $day = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $time_stamp = mktime(0,0,0,$month,1,$year);
//    $week = date('w',mktime(0,0,0,$month,1,$year));
    $first_day = date('Y-m-d',$time_stamp);;
    $last_day = date('Y-m-d',mktime(0,0,0,$month,$day,$year));
//    $first_day = date('Y-m-d',$time_stamp-24*3600*--$week);
//    $last_week = date('w',mktime(0,0,0,$month,$day,$year));
//    $last_day = date('Y-m-d',mktime(0,0,0,$month,$day,$year)+24*3600*(6-$last_week));
    $day_daily_paper = [];
    $PerdayDataItem = D("PerdayDataItem");
    for(;strtotime($first_day)<=strtotime($last_day);){
        $temp = [];
        $temp['date'] = $first_day;
        $temp['week'] =  date("w",strtotime($first_day));
        $temp['business'] = $PerdayDataItem->judgeDataInput($branch_id,$first_day,false);
        $temp['finance'] = $PerdayDataItem->judgeDataInput($branch_id,$first_day,true);;
        $day_daily_paper[] = $temp;
        $first_day = date('Y-m-d',strtotime("$first_day +1 day"));
    }
    return $day_daily_paper;
}
//格式化填写账户数据信息
function formatData($data,$date){
    $formatdata = [];
    $item = [];
    $i = 0;
    $project = M('project');
    $branch = M('branch');
    foreach($data as $key=>$value){
        $item[pickKey($key)] = $value;
        $i++;
        if($i%6 == 0){
            $item['project_id'] = preg_replace('/\D/s', '', $key);
            $item['project_name'] = $project->where('id='.$item['project_id'])->getField('name',true)[0];
            $item['category'] = $project->where('id='.$item['project_id'])->getField('category',true)[0];
            $item['user_id'] = session('admin')['id'];
            $item['branch_id'] = $project->where('id='.$item['project_id'])->getField('branch_id',true)[0];
            $item['branch_name'] = $branch->where('id='.$item['branch_id'])->getField('name',true)[0];
            $item['effect_date'] = $date;
            $formatdata[$i/6] = $item;
            $item = [];
        }
    }
    return $formatdata;
}
//将字符串中的key提取出来
function pickKey($str){
    if(substr($str,0,strpos($str,preg_replace('/\D/s', '', $str)))){
        return substr($str,0,strpos($str,preg_replace('/\D/s', '', $str)));
    }else{
        return $str;
    }
}
//根据key取出符合要求的数据
function getValueByKey($key,$value,$data){
    $needData = [];
    foreach($data as $k=>$v){
        if($v[$key] == $value){
            $needData[] = $v;
        }
    }
    return $needData;
}
//生成两个日期之前的所有日期，返回一个日期数组
function getStartDateEndDateArray($start_date,$end_date){
    $date_array = [];
    for(;strtotime($start_date)<=strtotime($end_date);){
        $date_array[] = $start_date;
        $start_date = date("Y-m-d",strtotime("$start_date +1 day"));
    }
    return $date_array;
}

