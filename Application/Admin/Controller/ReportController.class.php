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
    public function index(){
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
        $this->display();
}

    public function bank(){
        $project = M('project');
        $result = $project->join('branch on project.branch_id = branch.id')
            ->field('project.*,branch.name as branch_name')->select();
        $branch_name = M('branch')->getField('name',true);
        $formatData = [];
        foreach($branch_name as $key=>$value){
            $formatData[$value] = $this->getBankData($result,$value,'branch_name');
        }
        $this->assign('bankdata',$formatData);
        $this->display();
    }

    public function showData(){
        $Model = new Model();
        $select = "select b.id,p.`name`,b.`name` as name1,pd.asset_money,pd.asset_product,pd.finance_debt,pd.receivable,pd.payable,pd.remark,p.bank_category,e.onshore_exchange_rate
from branch b,perday_data_item pd,project p,exchange_rate e
where pd.project_id = p.id and pd.branch_id = b.id and SUBSTRING(pd.date,1,10) = e.date
and e.date='datetime'
ORDER BY b.id;";
        $result = $Model->query(str_replace('datetime','2016-09-16',$select));

        $branch_name = M('branch')->where('id != 10')->getField('name',true);
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
//        dump($formatData);die();
        $this->assign('formatdata',$formatData);
        $this->display();
//        dump($formatData);
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

    public function saveData(){
        $perday_data_item = M("perday_data_item");
        $data = I('post.');
        $form_data = $this->formatData($data);

        foreach($form_data as $key=>$value){
           $perday_data_item->add($value);
        }
        $this->success("插入数据成功");
        die();

    }



    //对前台传递过来的数据进行处理
    public function formatData($data){
        $formatdata = [];
        $item = [];
        $i = 0;
       foreach($data as $key=>$value){
           $item[$this->pickKey($key)] = $value;
           $i++;
           if($i%6 == 0){
               $item['project_id'] = preg_replace('/\D/s', '', $key);
               $item['user_id'] = session('admin')['id'];
               $item['branch_id'] = session('admin')['branch_id'];
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