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
        $this->display("FirstPage/firstPage");
    }
    //获得资产盈亏数据
    public function getAssetProffitLoss(){
        $data = I('get.');
        $branch_name = $data['branch_name'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        list($working_capital_data,$add_money_product,$date_array,$float_asset_money,$float_asset_product) = $this->getAssetProffitLossData($branch_name,$start_date,$end_date);
        //为了适应插件heighcharts插件需要对数据进行处理
        //浮动盈亏 = 资产现金＋资产品－运营资金
        array_walk($add_money_product,function(&$value,$key) use($working_capital_data,$float_asset_money,$float_asset_product,$working_capital_data){
            $value = array('y'=>$value,
                'FloatProfit'=>number_format($value-$working_capital_data[$key], 2, '.', ','),
                'FloatAssetMoney'=>number_format($float_asset_money[$key], 2, '.', ','),
                'FloatAssetProduct'=>number_format($float_asset_product[$key], 2, '.', ','),
                'working'=>number_format($working_capital_data[$key], 2, '.', ','));
        });

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
        //获得资产现金浮动
        $float_asset_money = $PerdayDataItem->getFloatAssetMoney($date_array,$branch_name);
        //获得资产品浮动
        $float_asset_product = $PerdayDataItem->getFloatAssetProduct($date_array,$branch_name);
        return [$working_capital_data,$add_money_product,$date_array,$float_asset_money,$float_asset_product];
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

    //部门资产结构数据
    public function getBranchStructure(){
        $data = I('get.');
        $branch_name = $data['branch_name'];
        $date = $data['date'];
        $PerdayDataItem = D("PerdayDataItem");
        $Branch = D("Branch");
        $branch_id = $Branch->getBranchIdByBranchName($branch_name);
        $branchStructureData = $this->getBranchStructureData($branch_id,$date);
        //获得上一个交易日期
        $lastDate = $PerdayDataItem->getLastDate($branch_id,$date);
        $lastBranchStructureData = $this->getBranchStructureData($branch_id,$lastDate);
        //计算差值
        array_walk($lastBranchStructureData,function(&$value,$index)use($branchStructureData){$value = $branchStructureData[$index] - $value;});
        $json['branchStructureData'] = $branchStructureData;
        $json['lastBranchStructureData'] = $lastBranchStructureData;
        $this->ajaxReturn($json);
    }
    //获得部门资产结构相关数据
    public function getBranchStructureData($branch_id,$date){
        $PerdayDataItem = D("PerdayDataItem");
        $result = $PerdayDataItem->getDataByBranchDay($branch_id,$date);
        $branch_structure = [];
        array_walk($result,function($value,$index) use(&$branch_structure){
            $branch_structure['asset_money'] +=doubleval($value['asset_money']);
            $branch_structure['asset_product'] += doubleval($value['asset_product']);
            $branch_structure['finance_debt'] += doubleval($value['finance_debt']);
            $branch_structure['receivable'] += doubleval($value['receivable']);
            $branch_structure['payable'] += doubleval($value['payable']);
        });
        return $branch_structure;
    }
    //获得部门业绩对比图
    public function getBranchContrast(){
        $data = I('get.');
        $branch_name_array = explode(',',$data['branch_name']) ;
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $data = [];
        foreach($branch_name_array as $value){
            $data [] = $this->getBranchContrastData($value,$start_date,$end_date);
        }
        $json['date_array'] = $this->getDateArray($branch_name_array[0],$start_date,$end_date);
        $json['data'] = $data;
        $this->ajaxReturn($json);
    }
    //获得部门业绩对比图数据
    public function getBranchContrastData($branch_name,$start_date,$end_date){
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
        $data = [];
        array_walk($working_capital_data,function($value,$index) use($add_money_product,&$data,$branch_name){
            $data[] = array('y'=>($add_money_product[$index] - $value)/$value,
                            'FloatProfit'=>$add_money_product[$index] - $value,
                            'name'=>$branch_name);
        });
        return $data;
    }
    //获得有数据的日期数组
    public function getDateArray($branch_name,$start_date,$end_date){
        $Branch = D("Branch");
        $PerdayDataItem = D("PerdayDataItem");
        $branch_id = $Branch->getBranchIdByBranchName($branch_name);
        //获得一个时间数组，即从开始时间到结束时间
        $date_array = getStartDateEndDateArray($start_date,$end_date);
        //获得有数据的日期
        $date_array = $this->getDataDate($date_array,$branch_id,$PerdayDataItem);
        return $date_array;
    }
}