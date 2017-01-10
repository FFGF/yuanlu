<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/4
 * Time: 16:43
 */
namespace Admin\Model;
use Think\Model;

class ExchangeRateModel extends Model{
    protected $tableName = "exchange_rate";
    //获得历史汇率
    public function getHistoryExchageRate(){
        return $this->order('effect_date desc')->select();
    }
    //判断某一天是否输入数据
    public function judgeExchangeRateInput($date){
        $maps['effect_date'] = $date;
        $result = $this->where($maps)->select();
        $flag = true;
        is_null($result)&&$flag = false;
        return $flag;
    }
    //保存数据
    public function saveExchangeRate($data){
        return $this->add($data);
    }
    //修改数据
    public function modifyExchangeRateData($data){
        $maps['effect_date'] = $data['effect_date'];
        $result = $this->where($maps)->save($data);
        $flag = true;
        is_null($result)&&$flag = false;
        return $flag;
    }
    //获得某天汇率数据
    public function getOneDayExchangeRateData($date){
        $maps['effect_date'] = $date;
        return $this->where($maps)->find();
    }
}