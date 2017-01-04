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
}