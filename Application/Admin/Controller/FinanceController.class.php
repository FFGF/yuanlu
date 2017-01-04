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
    //保存前台输入的汇率
    public function saveExchangeRate(){
        dump(I('post.'));
        die();
    }
}