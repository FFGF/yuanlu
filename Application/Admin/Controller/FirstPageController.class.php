<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 12:46
 */
namespace Admin\Controller;

use Think\Model;

class FirstPageController extends ChannelsController{
    //如果是业务操作员只获得对应部门，如果是管理员或者财务操作员获得所有部门
    public function firstPage(){
        $Branch = D("Branch");
        $branch_name = $Branch->getBranchNameByPower(session('admin')['power'],session('admin')['branch_id']);
        $this->assign('branch_name',$branch_name);
        $this->display();
    }
    //获得资产盈亏数据
    public function getAssetProffitLoss(){
        $data = I('get.');
        $branch_name = $data['branch_name'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        list($working_capital_data,$add_money_product,$date_array) = $this->getAssetProffitLossData($branch_name,$start_date,$end_date);
        $json['working_capital_data'] = $working_capital_data;
        $json['add_money_product'] = $add_money_product;
        $json['date_array'] = $date_array;
        $json['branch_name'] = $branch_name;
        $this->ajaxReturn($json);
    }
    public function getAssetProffitLossData($branch_name,$start_date,$end_date){
        $Branch = D("Branch");
        $PerdayDataItem = D("PerdayDataItem");
        $WorkingCapital = D("WorkingCapital");
        $branch_id = $Branch->getBranchIdByBranchName($branch_name);
        //获得一个时间数组，即从开始时间到结束时间
        $date_array = getStartDateEndDateArray($start_date,$end_date);
        //获得有数据的日期
        $date_array = $this->getDataDate($date_array,$branch_id,$PerdayDataItem);
        //获得运营资金数据
        $working_capital_data = $WorkingCapital->getDataByBranchId($branch_id,$date_array);
        //获得资产总和
        $add_money_product = $PerdayDataItem->getAddMoneyProduct($date_array,$branch_name);
        return [$working_capital_data,$add_money_product,$date_array];
    }
    //获得有数据的日期
    public function getDataDate($date_array,$branch_id,$PerdayDataItem){
        array_walk($date_array,function($date,$key) use($branch_id,$PerdayDataItem,&$date_array){
            if(!$PerdayDataItem->judgeOneDayOneBranchData($date,$branch_id)){
                $date_array[$key] = false;
            }
        });
        $date_array = array_filter($date_array);
        return array_values($date_array);
    }

}