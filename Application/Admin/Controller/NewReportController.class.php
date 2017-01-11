<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/7
 * Time: 18:45
 */
namespace Admin\Controller;

use Think\Model;

class NewReportController extends ChannelsController{
    public function index(){
        echo 123;
        die();
    }

    //表单输入
    public function dateChoose(){
        $this->display('NewReport/dateChoose');
    }
    //判断某一天是否已经录入数据
    public function judgeDataInput(){
        $date = I("date");
        $branch_id = session("admin")['branch_id'];
        $PerdayDataItem = D("PerdayDataItem");
        //如果是业务部门，只判断部门是否录入了 期货，现货数据
        if(session('admin')['power'] == 1){
            $flag = $PerdayDataItem->judgeDataInput($branch_id,$date,false);
        }else if(session('admin')['power'] == 2){
            $flag = $PerdayDataItem->judgeDataInput($branch_id,$date,true);
        }
        $json['code'] = 0;
        $flag&&$json['code'] = 1;//数据已经存储了
        $this->ajaxReturn($json);
    }
    //日报日期选择下一步按钮
    public function nextButtonDateChoose(){
        $user_id = session("admin")["id"];
        $Branch = D("Branch");
        session('dateChoose',I('dateChoose'));
        //银行数据
        $result_bank = $Branch->getBranchProjectByUserId(1,$user_id);
        if(session('admin')['power']==2){
            $result_bank = $Branch->getBranchProjectByUserId(1);
        }
        //期货数据
        $result_qihuo = $Branch->getBranchProjectByUserId(2,$user_id);
        //现货库存结存
        $result_cunhuo = $Branch->getBranchProjectByUserId(3,$user_id);

        $this->assign('project_qihuo', $result_qihuo);
        $this->assign('project_bank', $result_bank);
        $this->assign('project_cunhuo',$result_cunhuo);
        $this->display("NewReport/fillAccountInformation");
    }
    //填写账户信息页面下一步按钮
    public function nextButtonFAI(){
        $inputData = I('post.');
        $date = session('dateChoose');
        $formatData = formatData($inputData,$date);
        session('formatData',$formatData);
        //如果是银行人员，则搜索业务数据；如果是业务人员，则搜索财务数据
        $PerdayDataItem = D("PerdayDataItem");
        list($result_qihuo,$result_cunhuo,$result_bank) = $PerdayDataItem->getPreviewConfirmData(session('dateChoose'),session('admin')['branch_id'],$formatData,session('admin')['power']);

        $this->assign("result_qihuo",$result_qihuo);
        $this->assign("result_cunhuo",$result_cunhuo);
        $this->assign("result_bank",$result_bank);
        $this->display("NewReport/previewConfirm");
    }
    //对输入的数据或者之前已经保存到数据库的数据进行修改
    public function modifyDailyData(){
        $result_qihuo = getValueByKey('category',2,session('formatData'));
        $result_bank = getValueByKey('category',1,session('formatData'));
        $result_cunhuo = getValueByKey('category',3,session('formatData'));
        $this->assign('project_qihuo', $result_qihuo);
        $this->assign('project_bank', $result_bank);
        $this->assign('project_cunhuo',$result_cunhuo);
        $this->display("NewReport/modifyDailyData");
    }
    //保存财务，业务数据
    public function saveDailyData(){
        $PerdayDataItem = D("PerdayDataItem");
        $PerdayDataItem->saveData(session('formatData'));
        session('formatData',null);
        session('dateChoose',null);
        $this->redirect("NewReport/dailyPaper");
    }

    //查看某一天的数据
    public function lookReport(){
        $this->display("NewReport/lookReport");
    }
    //资产日报
    public function dailyPaper(){
        $this->display("NewReport/dailyPaper");
    }
    public function getDailyPaperDate(){
        $year = I('year');
        $month = I('month');
        $this->ajaxReturn(formatdailyPaperDate($year,$month,session('admin')['branch_id']));
    }
    //查看日历日报数据时，按照部门获得数据
    public function getPerdayDataItemByBranchId(){
        $data = I('get.');
        $type = $data['type'];
        $date = $data['date'];
        $result_qihuo = [];
        $result_cunhuo = [];
        $result_bank = [];
        $PerdayDataItem = D("PerdayDataItem");
        $OneDayOneBranchData = $PerdayDataItem->getOneDayOneBranchData($date,session('admin')['branch_id'],session('admin')['power']);

        session('formatData',$OneDayOneBranchData);
        session('dateChoose',$date);
        //之前根据button类型来显示一个部门的业务，财务数据。。。后来修改了，全部显示
//        $type == 'business' &&
        ($result_qihuo = getValueByKey('category','2',$OneDayOneBranchData))&&($result_cunhuo = getValueByKey('category','3',$OneDayOneBranchData));
//        $type == 'finance' &&
        ($result_bank = getValueByKey('category','1',$OneDayOneBranchData));

        $this->assign("result_qihuo",$result_qihuo);
        $this->assign("result_cunhuo",$result_cunhuo);
        $this->assign("result_bank",$result_bank);
        $this->assign("date",$date);
        $this->display("NewReport/lookReport");
    }
    //领导查看资产日报
    public function dailyPaperLead(){
        $Branch = D("Branch");
        $branch_name = $Branch->getBranchNameByPower(session('admin')['power'],session('admin')['branch_id']);
        $this->assign('branch_name',$branch_name);
        $this->display("NewReport/lead/dailyPaper");
    }
    //领导查看获得
    public function getDailyPaperDateLead(){
        $year = I('year');
        $month = I('month');
        $branch_name = I('branch_name');
        $Branch = D("Branch");
        $branch_id = $Branch->getBranchIdByBranchName($branch_name);
        //如果部门id为空，说明是查看所有部门
        $branch_id == null ? $this->ajaxReturn(formatdailyPaperDateLead($year,$month,$branch_name)): $this->ajaxReturn(formatdailyPaperDate($year,$month,$branch_id));
    }
    //领导根据日期查看数据
    public function getPerdayDataLead(){
        $data = I('get.');
        $date = $data['date'];
        $branch_name = $data['branch_name'];
        $this->getLeadData($date,$branch_name);
        die();
    }
    //获得领导查看的数据
    public function getLeadData($date,$branch_name){
        $Branch = D("Branch");
        $PerdayDataItem = D("PerdayDataItem");
        $branch_id = $Branch->getBranchIdByBranchName($branch_name);
        $data = $PerdayDataItem->getLeadData($date,$branch_id);
        $this->assign('formatdata',$data);
        $this->assign("date",$date);
        $this->display('NewReport/lead/showReport');
    }
    //数据导出
    public function exportExcel($timestamp=null){
        $timestamp = I('timestamp')?I('timestamp'):$timestamp;
        $date = date('Ymd',$timestamp);
        $objPHPExcel = exportExcel();
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
        browser_export('Excel2007','资产权益日报_'.$date.'.xlsx');//输出到浏览器
        $objWriter->save("php://output");
    }
    //财务人员导出数据
    public function exportExcelFinance(){
        $timestamp = I('date');
        $date = date('Y-m-d',$timestamp);
        $PerdayDataItem = D("PerdayDataItem");
        $data = $PerdayDataItem->getLeadData($date,null);
        $this->exportExcel($timestamp);
    }
}