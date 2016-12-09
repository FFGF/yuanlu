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
    public function reportInput(){
        $this->display();
    }
    //判断某一天是否已经录入数据
    public function judgeDataInput(){
        $date = I("date");
        $branch_id = session("admin")['branch_id'];
        $PerdayDataItem = D("PerdayDataItem");
        $flag = $PerdayDataItem->judgeDataInput($branch_id,$date);
        $json['code'] = 0;
        $flag&&$json['code'] = 1;//数据已经存储了
        $this->ajaxReturn($json);
    }
    //日报日期选择下一步按钮
    public function nextButtonDateChoose(){
        $user_id = session("admin")["id"];
        $Branch = D("Branch");
        //银行数据
        $result_bank = $Branch->getBranchProjectByUserId($user_id,1);
        //期货数据
        $result_qihuo = $Branch->getBranchProjectByUserId($user_id,2);
        //现货库存结存
        $result_cunhuo = $Branch->getBranchProjectByUserId($user_id,3);
        $this->assign('project_qihuo', $result_qihuo);
        $this->assign('project_bank', $result_bank);
        $this->assign('project_cunhuo',$result_cunhuo);
//        dump($result_cunhuo);
//        die();
        $inputContent = 1;
        $this->assign("inputContent",$inputContent);
        $this->display("NewReport/reportInput");
    }
}