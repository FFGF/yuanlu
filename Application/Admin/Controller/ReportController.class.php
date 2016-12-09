<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/9
 * Time: 0:01
 */
namespace Admin\Controller;

use Think\Model;

class ReportController extends ChannelsController{
    public function index($date=null,$submit_flag=null){
        if($date!=1){
            session('s_time',null);
        }
        $project = M('branch');
        $maps['user.id'] = session('admin')['id'];
        $maps['project.category'] = array('eq','2');
        //期货数据
        $result_qihuo = $project->join('user on user.branch_id = branch.id')
                          ->join('project on project.branch_id = branch.id')
                          ->field('project.id,project.name as name_project,project.branch_id,branch.name as branch_name')
                          ->where($maps)
                          ->select();
        //银行数据
        $maps['project.category'] = array('eq','1');
        $result_bank = $project->join('user on user.branch_id = branch.id')
            ->join('project on project.branch_id = branch.id')
            ->field('project.id,project.name as name_project,project.branch_id,branch.name as branch_name,project.category')
            ->where($maps)
            ->select();
        //现货库存结存
        $maps['project.category'] = array('eq','3');
        $result_cunhuo = $project->join('user on user.branch_id = branch.id')
            ->join('project on project.branch_id = branch.id')
            ->field('project.id,project.name as name_project,project.branch_id,branch.name as branch_name,project.category')
            ->where($maps)
            ->select();

        $branch = M('branch')->where('id='.session('admin')['branch_id'])->getField('name');
        $this->assign('power',session('admin')['power']);
        $this->assign('branch',$branch);
        $this->assign('project_qihuo', $result_qihuo);
        $this->assign('project_bank', $result_bank);
        $this->assign('project_cunhuo',$result_cunhuo);
        $this->assign('submit_flag',$submit_flag);
        $this->display('index');
}
    //获得个人负责项目的数据(业务人员)
    public function getUserData(){
        session('s_time',I('s_time'));

        $date = date('Y-m-d',I('s_time'));//生效时间
        $user_id = session('admin')['id'];
        $branch_id = session('admin')['branch_id'];
        $power = M('user')->where("id=$user_id")->getField('power',true);
        $Model = new Model();
        $select = "select b.id,pd.id as project_id,p.category,p.`name`,b.`name` as name1,pd.asset_money,pd.asset_product,pd.finance_debt,pd.receivable,pd.payable,pd.remark,p.bank_category
from branch b,perday_data_item pd,project p
where pd.project_id = p.id and pd.branch_id = b.id
and pd.effect_date='$date' and b.id=$branch_id
ORDER BY b.id;";

        $select_not_bank = "select b.id,pd.id as project_id,p.category,p.`name`,b.`name` as name1,pd.asset_money,pd.asset_product,pd.finance_debt,pd.receivable,pd.payable,pd.remark,p.bank_category
from branch b,perday_data_item pd,project p
where pd.project_id = p.id and pd.branch_id = b.id
and pd.effect_date='$date' and b.id=$branch_id and  p.category != 1
ORDER BY b.id;";

        $select_bank = "select b.id,pd.id as project_id,p.category,p.`name`,b.`name` as name1,pd.asset_money,pd.asset_product,pd.finance_debt,pd.receivable,pd.payable,pd.remark,p.bank_category
from branch b,perday_data_item pd,project p
where pd.project_id = p.id and pd.branch_id = b.id
and pd.effect_date='$date' and b.id=$branch_id and  p.category = 1
ORDER BY b.id;";

        $project = M('branch');
        $maps['user.id'] = session('admin')['id'];
        $maps['project.category'] = array('eq','2');
        //期货数据
        $result_qihuo = $project->join('user on user.branch_id = branch.id')
            ->join('project on project.branch_id = branch.id')
            ->field('project.id,project.name as name_project,project.branch_id,branch.name as branch_name')
            ->where($maps)
            ->select();

        //现货库存结存
        $maps['project.category'] = array('eq','3');
        $result_cunhuo = $project->join('user on user.branch_id = branch.id')
            ->join('project on project.branch_id = branch.id')
            ->field('project.id,project.name as name_project,project.branch_id,branch.name as branch_name,project.category')
            ->where($maps)
            ->select();

        $branch = M('branch')->where('id='.session('admin')['branch_id'])->getField('name');
        $result = $Model->query($select);

        $result_not_bank = $Model->query($select_not_bank);
        $result_bank = $Model->query($select_bank);

        if(count($result) == 0){
           $this->index('1',$submit_flag=1);
        }else{
            $this->assign('userdata',$result);
            $this->assign('branch',$branch);
            $this->assign('power',$power[0]);
            $flag = 1;
            if(count($result_not_bank) == 0 and count($result_bank) != 0){
                $flag = 2;
                $this->assign('project_qihuo', $result_qihuo);
                $this->assign('project_cunhuo',$result_cunhuo);
                $this->assign('result_bank',$result_bank);
            }
            $submit_flag = 1;
            $this->assign('submit_flag',$submit_flag);
            $this->assign('flag',$flag);
            $this->display('index');
        }
    }
    //折线图
    public function lineChart(){
        $branch = M('branch');
        $branch_name_array = $branch->where('id not in (10,11,12)')->getField('name',true);

        if($_POST){
            $branch_name = I('level');
            $branch_id = M('branch')->where('name='.'\''.$branch_name.'\'')->getField('id',true)[0];
            $date = [];
            $perday_data_item = M('perday_data_item');

            for($d=I('s_time');$d<=I('e_time');){
                $date [] = date('Y-m-d',$d);
                $d +=86400;
            }

            if($branch_name =="所有部门"){
                //去掉没有数据的日期
                foreach($date as $key=>$value){
                    if(!$perday_data_item->where('effect_date='.'\''."$value".'\'')->select()){
                        unset($date[$key]);
                    }
                }
            }else{
                //去掉没有数据的日期
                foreach($date as $key=>$value){
                    if(!$perday_data_item->where('effect_date='.'\''."$value".'\''.'and '.'branch_id='.'\''."$branch_id".'\'')->select()){
                        unset($date[$key]);
                    }
                }
            }
            $date = array_values($date);

            $working_capital_array = [];
            $working_capital = M('working_capital');

            $Model = new Model();
            $select = "select p.id as project_id,b.id,p.`name`,b.`name` as name1,
if(p.bank_category = 1, format(pd.asset_money * e.onshore_exchange_rate,2),format(pd.asset_money,2)) as asset_money,
if(p.bank_category = 1, format(pd.asset_product * e.onshore_exchange_rate,2),format(pd.asset_product,2)) as asset_product,
if(p.bank_category = 1, format(pd.finance_debt * e.onshore_exchange_rate,2),format(pd.finance_debt,2)) as finance_debt,
if(p.bank_category = 1, format(pd.receivable * e.onshore_exchange_rate,2),format(pd.receivable,2)) as receivable,
if(p.bank_category = 1, format(pd.payable * e.onshore_exchange_rate,2),format(pd.payable,2)) as payable,
pd.remark,p.bank_category,e.onshore_exchange_rate
from branch b,perday_data_item pd,project p,exchange_rate e
where pd.project_id = p.id and pd.branch_id = b.id and pd.effect_date = e.effect_date
and e.effect_date='datetime' and b.name='branchname'
ORDER BY b.id";

            if($branch_name == "所有部门"){
                $select = "select p.id as project_id,b.id,p.`name`,b.`name` as name1,
if(p.bank_category = 1, format(pd.asset_money * e.onshore_exchange_rate,2),format(pd.asset_money,2)) as asset_money,
if(p.bank_category = 1, format(pd.asset_product * e.onshore_exchange_rate,2),format(pd.asset_product,2)) as asset_product,
if(p.bank_category = 1, format(pd.finance_debt * e.onshore_exchange_rate,2),format(pd.finance_debt,2)) as finance_debt,
if(p.bank_category = 1, format(pd.receivable * e.onshore_exchange_rate,2),format(pd.receivable,2)) as receivable,
if(p.bank_category = 1, format(pd.payable * e.onshore_exchange_rate,2),format(pd.payable,2)) as payable,
pd.remark,p.bank_category,e.onshore_exchange_rate
from branch b,perday_data_item pd,project p,exchange_rate e
where pd.project_id = p.id and pd.branch_id = b.id and pd.effect_date = e.effect_date
and e.effect_date='datetime'
ORDER BY b.id";

                //计算运作资金
                foreach($date as $key=>$value){
                    $maps['effect_date'] = array('elt',$value);
                    $effect_date = $working_capital->where($maps)->order('effect_date desc')->getField('effect_date',true);
                    $maps['effect_date'] = array('eq',$effect_date[0]);
                    $accumulate = $working_capital->where($maps)->order('effect_date desc')->getField('accumulate',true);
                    $working_capital_array [] = array_sum($accumulate);
                }

            }else{
                //计算运作资金
                foreach($date as $key=>$value){
                    $maps['effect_date'] = array('elt',$value);
                    $maps['branch_id'] = $branch_id;
                    $accumulate = $working_capital->where($maps)->order('effect_date desc')->getField('accumulate',true);
                    $working_capital_array [] = $accumulate[0];
                }
            }
            $sum = [];
            foreach($date as $key=>$value){
                $result = $Model->query(str_replace('branchname',$branch_name,str_replace('datetime',$value,$select)));
                $sum[] = $this->sumBranch($result);
            }
            $this->assign('date',$date);
            $this->assign('sum',$sum);
            $this->assign('working_capital',$working_capital_array);
            $this->assign('branch_name1',$branch_name);
            $this->assign('branch_name',$branch_name_array);
            $this->display('linechart');
        }else{
            $this->assign('branch_name',$branch_name_array);
            $this->display('linechart');
        }
    }
    //运营资金输入
    public function workingCapital(){
        $working_capital = M('working_capital');

        $branch = M('branch');
        $branch_name_array = $branch->where('id not in (10,11,12)')->getField('name',true);
        if($_POST){
            $date =  date('Y-m-d',I('s_time'));
            $branch_id = M('branch')->where('name='.'\''.I('level').'\'')->getField('id',true);
            $map['effect_date'] = $date;
            $map['branch_id'] = $branch_id[0];
            if(!$working_capital->where($map)->select()){
                $data = I('post.');
                unset($data['s_time']);
                $data['effect_date'] = $date;//生效时间
                $data['branch_id'] = $branch_id[0];
                $where_accumulate['branch_id'] = $branch_id[0];
                $where_accumulate['effect_date'] = array('elt',$date);

                $accumulate = M('working_capital')->where($where_accumulate)->order('effect_date desc')->find();
                if($data['direction'] == "入金"){
                    $data['accumulate'] = $accumulate['accumulate'] + $data['sum'];
                }else{
                    $data['accumulate'] = $accumulate['accumulate'] - $data['sum'];
                }
                $data['user_id'] = session('admin')['id'];
                $working_capital->add($data);

                $result = $working_capital->join('branch on working_capital.branch_id = branch.id')
                    ->order('effect_date desc')
                    ->field('branch.`name`,working_capital.*')
                    ->select();

                $this->assign('result',$result);
                $this->assign('branch_name',$branch_name_array);
                $this->success("数据插入成功");
            }else{
                $this->error("数据库中已经存在，不能插入");
            }
        }else{
            $result = $working_capital->join('branch on working_capital.branch_id = branch.id')
                ->order('effect_date desc')
                ->field('branch.`name`,working_capital.*')
                ->select();
            $this->assign('result',$result);
            $this->assign('branch_name',$branch_name_array);
            $this->display();
        }
    }
    //更新运营资金数据
    public function updateWorkingCapital(){
        $map['id'] = I('id');
        $data = I('get.');
        unset($data['id']);
        $chang = M('working_capital')->where($map)->find();

        if($chang['direction'] == '入金'){
            $accumulate = $chang['accumulate'] - $chang['sum'];
        }else{
            $accumulate = $chang['accumulate'] + $chang['sum'];
        }

        if($data['direction'] == '入金'){
            $data['accumulate'] = $accumulate + $data['sum'];
        }else{
            $data['accumulate'] = $accumulate - $data['sum'];
        }

        $working_capital = M('working_capital')->where($map)->save($data);
        if($working_capital==false){
            $json['code']=0;//说明更新失败
        }else{
            $json['code']=1;//说明更新成功
        }
        exit(json_encode($json));
    }
    //计算一个部门的资产现金、资产品总和
    public function sumBranch($result){
        $sum = 0.0;
        foreach($result as $key=>$value){
            $sum = $sum + doubleval(str_replace(',','',$value['asset_money'])) + doubleval(str_replace(',','',$value['asset_product']));
        }
        return $sum;
    }
    //获得个人负责项目的数据（银行人员）
    public function getUserDataBank(){
        session('s_time',I('s_time'));
        $date = date('Y-m-d',I('s_time'));//生效时间
        $Model = new Model();
        if(I('bank')){
            $select = "select b.id,pd.id as project_id,p.category,p.`name`,b.`name` as name1,pd.asset_money,pd.asset_product,pd.finance_debt,pd.receivable,pd.payable,pd.remark,p.bank_category
from branch b,perday_data_item pd,project p
where pd.project_id = p.id and pd.branch_id = b.id
and pd.effect_date='$date' and p.category = 1
ORDER BY b.id;";
        }else{
            $select = "select b.id,pd.id as project_id,p.category,p.`name`,b.`name` as name1,pd.asset_money,pd.asset_product,pd.finance_debt,pd.receivable,pd.payable,pd.remark,p.bank_category
from branch b,perday_data_item pd,project p
where pd.project_id = p.id and pd.branch_id = b.id
and pd.effect_date='$date'
ORDER BY b.id;";
        }
        $result = $Model->query($select);
        if(count($result) == 0){
            $this->bank('1',I('bank'),$submit_flag=1);
        }else{
            $this->assign('userdata',$result);
            $flag = 1;
            $submit_flag=1;
            $this->assign('submit_flag',$submit_flag);
            $this->assign('flag',$flag);
            $this->display('bank');
        }
    }
    //更新数据
    public function updateData(){
        $map['id'] = I('id');
        $data = I('get.');
        unset($data['id']);
        $perday_data_item = M('perday_data_item')->where($map)->save($data);
        if($perday_data_item==false){
            $json['code']=0;//说明更新失败
        }else{
            $json['code']=1;//说明更新成功
        }
        exit(json_encode($json));
    }
    //删除数据
    public function deleteData(){
        $maps['id'] = I('id');
        if(M('perday_data_item')->where($maps)->delete()){
            $json['code']=1;//说明删除成功
        }else{
            $json['code']=0;//说明删除失败
        }
        exit(json_encode($json));
    }

    public function bank($date=null,$bank=null,$submit_flag=null){
        if($date!=1){
            session('s_time',null);
        }
        $project = M('project');
        if($bank){
            $result = $project->join('branch on project.branch_id = branch.id')
                ->where('project.category=1')->field('project.*,branch.name as branch_name')->select();
        }else{
            $result = $project->join('branch on project.branch_id = branch.id')
                ->field('project.*,branch.name as branch_name')->select();
        }
        $branch_name = M('branch')->getField('name',true);
        $formatData = [];
        foreach($branch_name as $key=>$value){
            $formatData[$value] = $this->getBankData($result,$value,'branch_name');
        }
        $this->assign('bankdata',$formatData);
        $this->assign('bank',$bank);
        $this->assign('submit_flag',$submit_flag);
        $this->display('bank');
    }
    //插入汇率
    public function rate(){
        $exchange_rate = M('exchange_rate');
        if($_POST){
            $data['effect_date']=date('Y-m-d',I('s_time'));//生效时间
            $maps['effect_date'] = array('eq',date('Y-m-d',I('s_time')));

            $fgf = $exchange_rate->where($maps)->select();
            if($fgf){
                $this->error('同一天不能插入两条数据');
            }else{
                $data['onshore_exchange_rate'] = I('onshore_exchange_rate');
                $data['offshore_exchange_rate'] = I('offshore_exchange_rate');
                $exchange_rate->add($data);
                $result = $exchange_rate->order('effect_date desc')->select();
                $this->assign('rate',$result);
                $this->display();

            }
        }else{
            $result = $exchange_rate->order('effect_date desc')->select();
            $this->assign('rate',$result);
            $this->display();
        }
    }
    //更新汇率
    public function updateRate(){
        $map['id'] = I('id');
        $data = I('get.');
        unset($data['id']);
        $exchange_rate = M('exchange_rate')->where($map)->save($data);
        if($exchange_rate==false){
            $json['code']=0;//说明更新失败
        }else{
            $json['code']=1;//说明更新成功
        }
        exit(json_encode($json));
    }
    //银行数据的呈现
    public function showData(){
        session('s_time',I('s_time'));

        if(I('s_time')){
            $date = date('Y-m-d',I('s_time'));//日报日期
            //计算上一个交易日的日期
            $perday_data_item = M('perday_data_item');
            $where_last_date['branch_id'] = session('admin')['branch_id'];
            if(session('admin')['power'] >= 2){
                unset($where_last_date['branch_id']);
            }
            $where_last_date['effect_date'] = array('lt',$date);
            $last_date = $perday_data_item->where($where_last_date)->order('effect_date desc')->find();
            $last_date = $last_date['effect_date'];
        }else{
            $date = date('Y-m-d',time());
            $last_date = date('Y-m-d',time()-86400);
            session('s_time',null);
        }
        $branch_id = session('admin')['branch_id'];
        $Model = new Model();

        if(session('admin')['power'] >=2 ){
            $select = "select p.id as project_id,b.id,p.`name`,b.`name` as name1,
if(p.bank_category = 1, format(pd.asset_money * e.onshore_exchange_rate,2),format(pd.asset_money,2)) as asset_money,
if(p.bank_category = 1, format(pd.asset_product * e.onshore_exchange_rate,2),format(pd.asset_product,2)) as asset_product,
if(p.bank_category = 1, format(pd.finance_debt * e.onshore_exchange_rate,2),format(pd.finance_debt,2)) as finance_debt,
if(p.bank_category = 1, format(pd.receivable * e.onshore_exchange_rate,2),format(pd.receivable,2)) as receivable,
if(p.bank_category = 1, format(pd.payable * e.onshore_exchange_rate,2),format(pd.payable,2)) as payable,
pd.remark,p.bank_category,e.onshore_exchange_rate,
pd.asset_money as asset_money_navtive,
pd.asset_product as asset_product_navtive,
pd.finance_debt as finance_debt_navtive,
pd.receivable as receivable_navtive,
pd.payable as payable_navtive,
u.user_name
from branch b,perday_data_item pd,project p,exchange_rate e,user u
where pd.project_id = p.id and pd.branch_id = b.id and pd.effect_date = e.effect_date and u.id = pd.user_id
and e.effect_date='datetime'
ORDER BY b.id";
        }else{
            $select = "select p.id as project_id,b.id,p.`name`,b.`name` as name1,
if(p.bank_category = 1, format(pd.asset_money * e.onshore_exchange_rate,2),format(pd.asset_money,2)) as asset_money,
if(p.bank_category = 1, format(pd.asset_product * e.onshore_exchange_rate,2),format(pd.asset_product,2)) as asset_product,
if(p.bank_category = 1, format(pd.finance_debt * e.onshore_exchange_rate,2),format(pd.finance_debt,2)) as finance_debt,
if(p.bank_category = 1, format(pd.receivable * e.onshore_exchange_rate,2),format(pd.receivable,2)) as receivable,
if(p.bank_category = 1, format(pd.payable * e.onshore_exchange_rate,2),format(pd.payable,2)) as payable,
pd.remark,p.bank_category,e.onshore_exchange_rate,
pd.asset_money as asset_money_navtive,
pd.asset_product as asset_product_navtive,
pd.finance_debt as finance_debt_navtive,
pd.receivable as receivable_navtive,
pd.payable as payable_navtive
from branch b,perday_data_item pd,project p,exchange_rate e
where pd.project_id = p.id and pd.branch_id = b.id and pd.effect_date = e.effect_date
and e.effect_date='datetime' and b.id = $branch_id
ORDER BY b.id";
        }
        $result = $Model->query(str_replace('datetime',$date,$select));
        //上一个交易日的数据
        $last_result = $Model->query(str_replace('datetime',$last_date,$select));
        $maps['id'] = array('eq',$branch_id);
        if(session('admin')['power'] >= 2){
            unset($maps['id']);
            $maps['id'] = array('not in',array('10','11','12'));//去掉一些无用的部门
        }
        $branch_name = M('branch')->where($maps)->getField('name',true);
        $formatData = [];
        $formatData_last = [];
        foreach($branch_name as $key=>$value){
            $formatData[$value] = $this->getBankData($result,$value,'name1');
            $formatData_last[$value] = $this->getBankData($last_result,$value,'name1');
        }
        //完善数据，如果董事长当天查看数据，但是没有录入，会无法查看，不知道数据是否输入
        $formatData = $this->formatShowData($formatData);
        $formatData_last = $this->formatShowData($formatData_last);

        $formatData_last = $this->sumFuck($formatData_last);
        $formatData = $this->sumFuck($formatData);
        if(session('admin')['power'] >= 2){
            $formatData = $this->subtractDataAll($formatData,$formatData_last);
        }else{
            $formatData = $this->subtractData($formatData, $formatData_last);
        }
        //数据缓存
        S(session('admin')['id'].'formatData',array_reverse($formatData));
        $this->assign('formatdata',array_reverse($formatData));
        $this->display();
    }

    //完善数据，如果董事长当天查看数据，但是没有录入，会无法查看，不知道数据是否输入
    public function formatShowData($formatData){
        $formatData_temp = $formatData;
        foreach($formatData as $key=>$value){
            $k_maps['branch.name'] = $key;
            $result = M('project')->join('branch on branch.id = project.branch_id')
                ->where($k_maps)
                ->field('project.name')
                ->select();
            $form_result = [];
            foreach($result as $k=>$v){
                $form_result[] = $v['name'];
            }
            $temp = [];
            foreach($value as $k=>$v){
                $temp[] = $v['name'];
            }
            $diff_array = array_diff($form_result,$temp);
            foreach($diff_array as $v){
                $project_result = M('project')->where(array('name'=>$v))->find();
                $t['project_id'] = $project_result['id'];
                $t['id'] = $project_result['branch_id'];
                $t['name'] = $v;
                $t['name1'] = $key;
                $t['asset_money'] = '0.00';
                $t['asset_product'] = '0.00';
                $t['finance_debt'] = '0.00';
                $t['receivable'] = '0.00';
                $t['payable'] = '0.00';
                $t['remark'] = '';
                $t['bank_category'] = $project_result['bank_category'];
                $t['onshore_exchange_rate'] = '0.0000';
                $t['asset_money_navtive'] = '0.0000';
                $t['asset_product_navtive'] = '0.0000';
                $t['finance_debt_navtive'] = '0.0000';
                $t['receivable_navtive'] = '0.0000';
                $t['payable_navtive'] = '0.0000';
                $formatData_temp[$key][] = $t;
            }
        }
        return $formatData_temp;
    }
    //数据导出
    public function exportExcel(){
        Vendor('PHPExcel.PHPExcel');
        $formatData = S(session('admin')['id'].'formatData');
        $objPHPExcel=new \PHPExcel();//实例化PHPExcel类， 等同于在桌面上新建一个excel

        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

        $objSheet=$objPHPExcel->getActiveSheet();//获得当前活动sheet

        $objSheet->setCellValue("A1","账号")->setCellValue("B1","资产现金")->setCellValue("C1","资产品")->setCellValue("D1","融资负债/授信")
                    ->setCellValue("E1","应收")->setCellValue("F1","应付")->setCellValue("G1","资产现金+-")
                    ->setCellValue("H1","资产品+-")->setCellValue("I1","持仓授信+-")->setCellValue("J1","备注")->setCellValue("K1","制单人");
        $objSheet->setCellValue("A2",'所有部门总和')->setCellValue("B2",' '.number_format($formatData['所有部门总和'][0]['asset_money'],2,'.',''))
                ->setCellValue("C2",' '.number_format($formatData['所有部门总和'][0]['asset_product'],2,'.',''))
                ->setCellValue("D2",' '.number_format($formatData['所有部门总和'][0]['finance_debt'],2,'.',''))
                ->setCellValue("E2",' '.number_format($formatData['所有部门总和'][0]['receivable'],2,'.',''))
                ->setCellValue("F2",' '.number_format($formatData['所有部门总和'][0]['payable'],2,'.',''))
                ->setCellValue("G2","")
                ->setCellValue("H2",' '.number_format($formatData['所有部门总和'][0]['sum_asset_money'],2,'.',''))
                ->setCellValue("I2",' '.number_format($formatData['所有部门总和'][0]['sum_asset_product'],2,'.',''))
                        ->setCellValue("J2",' '.number_format($formatData['所有部门总和'][0]['sum_finance_debt'],2,'.',''));
        unset($formatData['所有部门总和']);
        $i = 3;
        foreach($formatData as $key=>$value){
            $objSheet->setCellValue("A".$i,$key);
            $objSheet->getStyle('A'.$i.':J'.$i)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $objSheet->getStyle('A'.$i.':J'.$i)->getFill()->getStartColor()->setRGB('00ffff');
            foreach($value as $k=>$v){
                $i += 1;
                if($v['bank_category'] == 1){
                    $objSheet->setCellValue("A".$i,$v['name'])
                        ->setCellValue("B".$i,str_replace(',','','$'.number_format($v['asset_money_navtive'],2)))
                        ->setCellValue("C".$i, str_replace(',','','$'.number_format($v['asset_product_navtive'],2)))
                        ->setCellValue("D".$i,str_replace(',','','$'.number_format($v['finance_debt_navtive'],2)))
                        ->setCellValue("E".$i,str_replace(',','','$'.number_format($v['receivable_navtive'],2)))
                        ->setCellValue("F".$i,str_replace(',','','$'.number_format($v['payable_navtive'],2)))
                        ->setCellValue("G".$i,'$'.str_replace(',','',number_format($v['sum_asset_money_navtive'],2)))
                        ->setCellValue("H".$i,'$'.str_replace(',','',number_format($v['sum_asset_product_navtive'],2)))
                        ->setCellValue("I".$i,'$'.str_replace(',','',number_format($v['sum_finance_debt_navtive'],2)))
                        ->setCellValue("J".$i,$v['remark'])
                        ->setCellValue("K".$i,$v['user_name']);
                }else {
                    if ($v['name'] == '部门总和') {
                        $objSheet->setCellValue("A".$i,$v['name'])
                            ->setCellValue("B".$i,' '.number_format($v['asset_money'],2,'.',''))
                            ->setCellValue("C".$i, ' '.number_format($v['asset_product'],2,'.',''))
                            ->setCellValue("D".$i,' '.number_format($v['finance_debt'],2,'.',''))
                            ->setCellValue("E".$i,' '.number_format($v['receivable'],2,'.',''))
                            ->setCellValue("F".$i,' '.number_format($v['payable'],2,'.',''))
                            ->setCellValue("G".$i,' '.number_format($v['sum_asset_money'],2,'.',''))
                            ->setCellValue("H".$i,' '.number_format($v['sum_asset_product'],2,'.',''))
                            ->setCellValue("I".$i,' '.number_format($v['sum_finance_debt'],2,'.',''))
                            ->setCellValue("J".$i,$v['remark'])
                            ->setCellValue("K".$i,$v['user_name']);
                    } else {
                        $objSheet->setCellValue("A".$i,$v['name'])
                            ->setCellValue("B".$i,' '.str_replace(',','',$v['asset_money']))
                            ->setCellValue("C".$i,' '.str_replace(',','',$v['asset_product']))
                            ->setCellValue("D".$i,' '.str_replace(',','',$v['finance_debt']))
                            ->setCellValue("E".$i,' '.str_replace(',','',$v['receivable']))
                            ->setCellValue("F".$i,' '.str_replace(',','',$v['payable']))
                            ->setCellValue("G".$i,' '.number_format($v['sum_asset_money'],2,'.',''))
                            ->setCellValue("H".$i,' '.number_format($v['sum_asset_product'],2,'.',''))
                            ->setCellValue("I".$i,' '.number_format($v['sum_finance_debt'],2,'.',''))
                            ->setCellValue("J".$i,$v['remark'])
                            ->setCellValue("K".$i,$v['user_name']);
                    }
                }
            }
            $i += 1;
        }
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
        $this->browser_export('Excel2007','源庐.xlsx');//输出到浏览器
        $objWriter->save("php://output");
    }
    function browser_export($type,$filename){
        if($type=="Excel5"){
            header('Content-Type: application/vnd.ms-excel');//告诉浏览器将要输出excel03文件
        }else{
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器数据excel07文件
        }
        header('Content-Disposition: attachment;filename="'.$filename.'"');//告诉浏览器将输出文件的名称
        header('Cache-Control: max-age=0');//禁止缓存
    }

    public function sumFuck($formatData){
        $formatData['所有部门总和']['fuck'] = [];
        foreach($formatData as $key=>$value){
            $i = count($value);
            if($key!='所有部门总和'){
                $formatData[$key][$i] = [];
                $formatData[$key][$i]['name'] = '部门总和';
            }
            foreach($value as $k=>$v){
                if($key!='所有部门总和'){
                    $formatData[$key][$i]['asset_money'] =  $formatData[$key][$i]['asset_money'] +  str_replace(',','',$v['asset_money']);
                    $formatData[$key][$i]['asset_product'] =  $formatData[$key][$i]['asset_product'] +  str_replace(',','',$v['asset_product']);
                    $formatData[$key][$i]['finance_debt'] =  $formatData[$key][$i]['finance_debt'] +  str_replace(',','',$v['finance_debt']);
                    $formatData[$key][$i]['receivable'] =  $formatData[$key][$i]['receivable'] +  str_replace(',','',$v['receivable']);
                    $formatData[$key][$i]['payable'] =  $formatData[$key][$i]['payable'] +  str_replace(',','',$v['payable']);
                }
                $formatData['所有部门总和']['fuck']['asset_money'] =  $formatData['所有部门总和']['fuck']['asset_money'] + str_replace(',','',$v['asset_money']);
                $formatData['所有部门总和']['fuck']['asset_product'] =  $formatData['所有部门总和']['fuck']['asset_product'] + str_replace(',','',$v['asset_product']);
                $formatData['所有部门总和']['fuck']['finance_debt'] =  $formatData['所有部门总和']['fuck']['finance_debt'] + str_replace(',','',$v['finance_debt']);
                $formatData['所有部门总和']['fuck']['receivable'] =  $formatData['所有部门总和']['fuck']['receivable'] + str_replace(',','',$v['receivable']);
                $formatData['所有部门总和']['fuck']['payable'] =  $formatData['所有部门总和']['fuck']['payable'] + str_replace(',','',$v['payable']);
            }
        }
        return $formatData;
    }
    //计算两个交易日的差值
    public function subtractData($result,$last_result){
        $sub_data = [];
        foreach($result as $key=>$value){
            $sub_data[$key] = [];
        }
        reset($last_result);
        $first = key($last_result);
        if(count($last_result[$first])<=1){
            //如果上一个交易日没有数据
            foreach($result as $key=>$value){
                foreach($value as $k=>$v){
                                $v['sum_asset_money'] = round(str_replace(',','',$v['asset_money']),2);
                                $v['sum_asset_money_navtive'] = round(str_replace(',','',$v['asset_money_navtive']),2);

                                $v['sum_asset_product'] = round(str_replace(',','',$v['asset_product']),2);
                                $v['sum_asset_product_navtive'] = round(str_replace(',','',$v['asset_product_navtive']),2);

                                $v['sum_finance_debt'] = round(str_replace(',','',$v['finance_debt']),2);
                                $v['sum_finance_debt_navtive'] = round(str_replace(',','',$v['finance_debt_navtive']),2);
                                array_push($sub_data[$key], $v);
                }
            }
        }else{
            foreach($result as $key=>$value){
                foreach($value as $k=>$v){
                    foreach($last_result as $key_last=>$value_last){
                        foreach($value_last as $k_l=>$v_l){
                            if($v['name'] == $v_l['name']){
                                $v['sum_asset_money'] = round(str_replace(',','',$v['asset_money'])  - str_replace(',','',$v_l['asset_money']),2);
                                $v['sum_asset_money_navtive'] = round(str_replace(',','',$v['asset_money_navtive'])  - str_replace(',','',$v_l['asset_money_navtive']),2);

                                $v['sum_asset_product'] = round(str_replace(',','',$v['asset_product'])  - str_replace(',','',$v_l['asset_product']),2);
                                $v['sum_asset_product_navtive'] = round(str_replace(',','',$v['asset_product_navtive'])  - str_replace(',','',$v_l['asset_product_navtive']),2);

                                $v['sum_finance_debt'] = round(str_replace(',','',$v['finance_debt'])  - str_replace(',','',$v_l['finance_debt']),2);
                                $v['sum_finance_debt_navtive'] = round(str_replace(',','',$v['finance_debt_navtive'])  - str_replace(',','',$v_l['finance_debt_navtive']),2);
                                array_push($sub_data[$key], $v);
                            }
                        }
                    }
                }
            }
        }
        return $sub_data;
    }

    //计算两个交易日的差值，所有部门
    public function subtractDataAll($result,$last_result){
        $sub_data = [];
        foreach($result as $key=>$value){
            $sub_data[$key] = [];
        }
        reset($last_result);
        $first = key($last_result);
        foreach($result as $key=>$value){
            foreach($value as $k=>$v){
                $v['sum_asset_money'] = str_replace(',','',$v['asset_money']) -$this->getLastDateData($key,$v['name'],$last_result,'asset_money');
                $v['sum_asset_money_navtive'] = str_replace(',','',$v['asset_money_navtive']) -$this->getLastDateData($key,$v['name'],$last_result,'asset_money_navtive');

                $v['sum_asset_product'] = str_replace(',','',$v['asset_product']) -$this->getLastDateData($key,$v['name'],$last_result,'asset_product');
                $v['sum_asset_product_navtive'] = str_replace(',','',$v['asset_product_navtive']) -$this->getLastDateData($key,$v['name'],$last_result,'asset_product_navtive');

                $v['sum_finance_debt'] = str_replace(',','',$v['finance_debt']) -$this->getLastDateData($key,$v['name'],$last_result,'finance_debt');
                $v['sum_finance_debt_navtive'] = str_replace(',','',$v['finance_debt_navtive']) -$this->getLastDateData($key,$v['name'],$last_result,'finance_debt_navtive');
                array_push($sub_data[$key], $v);
            }
        }
        return $sub_data;
    }
    //获得上一个交易日数据
    public function getLastDateData($key,$name,$last_result,$str){
        $item_array = $last_result[$key];
        foreach($item_array as $key=>$value){
            if($value['name'] == $name){
                return str_replace(',','',$value[$str]);
            }
        }
    }

    public function getBankData($result,$value,$key_name){
        $data = [];
        foreach($result as $key=>$item) {
            if ($item[$key_name] == $value) {
                $data[] = $item;
            }
        }
        return $data;
    }
    //插入业务数据
    public function saveData(){
        if(I('s_time1')){
            $date = date('Y-m-d',I('s_time1'));//日报日期
        }else if(I('s_time2')){
            $date = date('Y-m-d',I('s_time2'));//日报日期
        }

        $perday_data_item = M("perday_data_item");
        $data = I('post.');
        unset($data['s_time1']);
        $form_data = $this->formatData($data,$date);
        foreach($form_data as $key=>$value){
           $perday_data_item->add($value);
        }
        $this->success("插入数据成功");
    }
    //对前台传递过来的数据进行处理
    public function formatData($data,$date){
        $formatdata = [];
        $item = [];
        $i = 0;
        $project = M('project');
       foreach($data as $key=>$value){
           $item[$this->pickKey($key)] = $value;
           $i++;
           if($i%6 == 0){
               $item['project_id'] = preg_replace('/\D/s', '', $key);
               $item['user_id'] = session('admin')['id'];
               $item['branch_id'] = $project->where('id='.$item['project_id'])->getField('branch_id',true)[0];
               $item['effect_date'] = $date;
               $formatdata[$i/6] = $item;
               $item = [];
           }
       }
        return $formatdata;
    }
    //将字符串中的key提取出来
    public function pickKey($str){
        return substr($str,0,strpos($str,preg_replace('/\D/s', '', $str)));
    }
}