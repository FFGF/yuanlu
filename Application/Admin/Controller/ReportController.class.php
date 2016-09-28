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
    public function index($date=null){
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
        $this->display('index');
}
    //获得个人负责项目的数据(业务人员)
    public function getUserData(){
        session('s_time',I('s_time'));

        $date = date('Y-m-d',I('s_time'));//生效时间
        $user_id = session('admin')['id'];
        $power = M('user')->where("id=$user_id")->getField('power',true);
        $Model = new Model();
        $select = "select b.id,pd.id as project_id,p.category,p.`name`,b.`name` as name1,pd.asset_money,pd.asset_product,pd.finance_debt,pd.receivable,pd.payable,pd.remark,p.bank_category
from branch b,perday_data_item pd,project p
where pd.project_id = p.id and pd.branch_id = b.id
and pd.effect_date='$date' and pd.user_id=$user_id
ORDER BY b.id;";
        $branch = M('branch')->where('id='.session('admin')['branch_id'])->getField('name');

        $result = $Model->query($select);
        if(count($result) == 0){
           $this->bank('1');
        }else{
            $this->assign('userdata',$result);
            $this->assign('branch',$branch);
            $this->assign('power',$power[0]);
            $flag = 1;
            $this->assign('flag',$flag);
            $this->display('index');
        }
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
            $this->bank('1',I('bank'));
        }else{
            $this->assign('userdata',$result);
            $flag = 1;
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

    public function bank($date=null,$bank=null){
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
        $this->display('bank');
    }

    //插入汇率
    public function rate(){
        $exchange_rate = M('exchange_rate');
        if($_POST){
            $data['effect_date']=date('Y-m-d',I('s_time'));//生效时间
            $data['onshore_exchange_rate'] = I('onshore_exchange_rate');
            $data['offshore_exchange_rate'] = I('offshore_exchange_rate');
            $exchange_rate->add($data);
        }
        $result = $exchange_rate->order('effect_date desc')->select();
        $this->assign('rate',$result);
        $this->display();
    }
    //银行数据的呈现
    public function showData(){
        if(I('s_time')){
            $date = date('Y-m-d',I('s_time'));//日报日期
            $last_date = date('Y-m-d',I('s_time')-86400);
        }else{
            $date = date('Y-m-d',time());
            $last_date = date('Y-m-d',time()-86400);
        }

        $Model = new Model();
        $select = "select p.id as project_id,b.id,p.`name`,b.`name` as name1,pd.asset_money,pd.asset_product,pd.finance_debt,pd.receivable,pd.payable,pd.remark,p.bank_category,e.onshore_exchange_rate
from branch b,perday_data_item pd,project p,exchange_rate e
where pd.project_id = p.id and pd.branch_id = b.id and pd.effect_date = e.effect_date
and e.effect_date='datetime'
ORDER BY b.id;";
        $result = $Model->query(str_replace('datetime',$date,$select));

        $last_result = $Model->query(str_replace('datetime',$last_date,$select));

//        $result = $this->subtractData($result,$last_result);
        $branch_name = M('branch')->where('id != 10 and id != 12')->getField('name',true);
        $formatData = [];
        foreach($branch_name as $key=>$value){
            $formatData[$value] = $this->getBankData($result,$value,'name1');
        }

        $formatData['所有部门总和']['fuck'] = [];

        foreach($formatData as $key=>$value){
           $i = count($value);
            if($key!='所有部门总和'){
                $formatData[$key][$i] = [];
                $formatData[$key][$i]['name'] = '部门总和';
            }
            foreach($value as $k=>$v){
                if($v['bank_category'] == 1){
                    $v['asset_money'] = $v['asset_money'] * $v['onshore_exchange_rate'];
                    $v['asset_product'] =  $v['asset_product'] * $v['onshore_exchange_rate'];
                    $v['finance_debt'] = $v['finance_debt'] * $v['onshore_exchange_rate'];
                    $v['receivable'] =  $v['receivable'] * $v['onshore_exchange_rate'];
                    $v['payable'] =  $v['payable'] * $v['onshore_exchange_rate'];
                }
                if($key!='所有部门总和'){
                    $formatData[$key][$i]['asset_money'] =  $formatData[$key][$i]['asset_money'] +  $v['asset_money'];
                    $formatData[$key][$i]['asset_product'] =  $formatData[$key][$i]['asset_product'] +  $v['asset_product'];
                    $formatData[$key][$i]['finance_debt'] =  $formatData[$key][$i]['finance_debt'] +  $v['finance_debt'];
                    $formatData[$key][$i]['receivable'] =  $formatData[$key][$i]['receivable'] +  $v['receivable'];
                    $formatData[$key][$i]['payable'] =  $formatData[$key][$i]['payable'] +  $v['payable'];
                }
                $formatData['所有部门总和']['fuck']['asset_money'] =  $formatData['所有部门总和']['fuck']['asset_money'] + $v['asset_money'];
                $formatData['所有部门总和']['fuck']['asset_product'] =  $formatData['所有部门总和']['fuck']['asset_product'] + $v['asset_product'];
                $formatData['所有部门总和']['fuck']['finance_debt'] =  $formatData['所有部门总和']['fuck']['finance_debt'] + $v['finance_debt'];
                $formatData['所有部门总和']['fuck']['receivable'] =  $formatData['所有部门总和']['fuck']['receivable'] + $v['receivable'];
                $formatData['所有部门总和']['fuck']['payable'] =  $formatData['所有部门总和']['fuck']['payable'] + $v['payable'];
            }
        }
        $this->assign('formatdata',$formatData);
        $this->display();
    }

    public function subtractData($result,$last_result){
        $sub_data = [];
        foreach($result as $key=>$value){
            foreach($last_result as $k=>$v){
                if($value['project_id'] == $v['project_id']){
                    $value['sub_asset_money'] = $value['asset_money'] - $v['asset_money'];
                    $value['sub_asset_product'] = $value['asset_product'] - $v['asset_product'];
                    $value['sub_finance_debt'] = $value['finance_debt'] - $v['finance_debt'];
                    $value['sub_receivable'] = $value['receivable'] - $v['receivable'];
                    $value['sub_payable'] = $value['payable'] - $v['payable'];
                    $sub_data[] = $value;
//                    dump($v);
                    break;
                }else{
                    $sub_data[] = $value;
//                    dump($value);
                    break;
                }
            }
        }
//        die();
        return $sub_data;
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
        }else{
            $date = date('Y-m-d',time());
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