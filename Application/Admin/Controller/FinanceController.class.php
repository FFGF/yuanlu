<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/4
 * Time: 14:29
 */
namespace Admin\Controller;

use Think\Model;

class FinanceController extends ChannelsController{
    public function index(){
        dump('12');
    }
    //汇率录入
    public function inputExchangeRate(){
        $historyExchangeRate = D("ExchangeRate")->getHistoryExchageRate();
        $this->assign("historyExchangeRate",$historyExchangeRate);
        $this->display("Finance/inputExchangeRate");
    }
    //判断某一天汇率是否输入
    public function judgeExchangeRateInput(){
        $date = I("date");
        $flag = D("ExchangeRate")->judgeExchangeRateInput($date);
        $json['code'] = 0;
        $flag&&$json['code'] = 1;//数据已经存储了
        $this->ajaxReturn($json);
    }
    //保存前台输入的汇率
    public function saveExchangeRate(){
        $data = I("get.");
        $result = D("ExchangeRate")->saveExchangeRate($data);
        $json['code'] = 0;
        if($result){$json['code'] = 1;}
        $this->ajaxReturn($json);
    }
    //汇率修改
    public function modifyExchangeRate(){
        $timestamp = I("effect_date");
        $date = date("Y-m-d",$timestamp);
        $this->assign("date",$date);
        $this->display("Finance/modifyExchangeRate");
    }
    //保存修改的数据
    public function modifyExchangeRateData(){
        $data = I("get.");
        $result = D("ExchangeRate")->modifyExchangeRateData($data);
        $json['code'] = 0;
        if($result){$json['code'] = 1;}
        $this->ajaxReturn($json);
    }
}