<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 15:20
 */
namespace Admin\Model;
use Think\Model;

class PerdayDataItemModel extends Model{
    protected $tableName = "perday_data_item";
    //判断某一天是否已经录入数据
    public function judgeDataInput($brach_id,$date){
        $maps['branch_id'] = $brach_id;
        $maps['effect_date'] = $date;
        $result = $this->where($maps)->select();
        $flag = true;
        is_null($result)&&$flag = false;
        return $flag;
    }
}