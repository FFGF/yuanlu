<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 15:20
 */
namespace Admin\Model;
use Think\Model;

class PerdayDataItemModel extends Model{
    protected $tableName = "perday_data_item";
    //判断某一部门某一天是否已经录入数据,和操作员的有关，业务和银行操作员返回的数据不同
    public function judgeDataInput($brach_id,$date,$bank){
        if($bank){$maps['p.category'] = array('eq','1');}else{$maps['p.category'] = array('neq','1');$maps['pd.branch_id'] = $brach_id;}
        $maps['pd.effect_date'] = $date;
        $result = $this->alias('pd')->join('project p on pd.project_id=p.id')->where($maps)->select();
        $flag = true;
        is_null($result)&&$flag = false;
        return $flag;
    }
    //获得莫某一天，某一个部门的数据，根据project的category,用户的power。如果power为2银行人员，则不按照部门来查找，因为没有项目属于银行部门
    public function getOneDayOneBranchByCategory($date,$branch_id,$category,$power){
        $maps['pd.branch_id'] = $branch_id;
        if($power == 2){
            unset($maps['pd.branch_id']);
        }
        $maps['pd.effect_date'] = $date;
        $maps['p.category'] = $category;
        $result = $this->alias('pd')
                    ->join('project p on pd.project_id=p.id')
                    ->field('pd.asset_money,pd.asset_product,pd.finance_debt,pd.receivable,pd.payable,pd.remark,p.name as project_name,pd.id,p.name as project_name,pd.remark,pd.project_id')
                    ->where($maps)
                    ->select();
        return $result;
    }
    //如果是银行人员，则搜索业务数据；如果是业务人员，则搜索财务数据
    public function getPreviewConfirmData($date,$branch_id,$formatData,$power){
        if($power == 1){
            //需要获得当天的银行数据
            $result_bank =  $this->getOneDayOneBranchByCategory($date,$branch_id,'1',$power);
            $result_qihuo = getValueByKey('category',2,$formatData,$power);
            $result_cunhuo = getValueByKey('category',3,$formatData,$power);
        }else if($power == 2){
            //需要获得当天的业务数据
            $result_qihuo = $this->getOneDayOneBranchByCategory($date,$branch_id,'2',$power);
            $result_cunhuo = $this->getOneDayOneBranchByCategory($date,$branch_id,'3',$power);
            $result_bank = $formatData;
        }
        return [$result_qihuo,$result_cunhuo,$result_bank];
    }
    //保存数据
    public function saveData($formatData){
        foreach($formatData as $value){
            $temp_maps['project_id'] = $value['project_id'];
            $temp_maps['effect_date'] = $value['effect_date'];
            $exit = $this->where($temp_maps)->find();
            if($exit){
                $this->where($temp_maps)->save($value);//存在数据就更新
            }else{
                $this->add($value);//不存在就插入数据
            }
        }
    }
    //获得某一天，某一个部门所有的数据.
    public function getOneDayOneBranchData($date,$branch_id,$power){
        $maps['pd.effect_date'] = $date;
        $maps['pd.branch_id'] = $branch_id;
        if($power==2) unset($maps['pd.branch_id']);
        $result = $this->alias('pd')
                        ->join('project p on pd.project_id=p.id')
                        ->join('branch b on pd.branch_id=b.id')
                        ->field('pd.*,p.category,p.name as project_name,b.name as branch_name')
                        ->where($maps)->select();
        return $result;
    }
    //判断某一部门，某一天是否录入数据，只和部门，时间有关
    public function judgeOneDayOneBranchData($date,$branch_id){
        $maps['effect_date'] = $date;
        $maps['branch_id'] = $branch_id;
        //如果branch_id为空，说明查找的是所有的部门
        if($branch_id == null)unset($maps['branch_id']);
        $flag = $this->where($maps)->select();
        $flag = $flag?true:false;
        return $flag;
    }
    //获得某一个部门某天asset_money,asset_product的和
    public function getAddMoneyProduct($date_array,$branch_name){
        $sum = [];
        $Model = new Model();
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
        }else{
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
        }
        foreach($date_array as $key=>$value){
            $result = $Model->query(str_replace('branchname',$branch_name,str_replace('datetime',$value,$select)));
            $sum[] = $this->sumBranch($result);
        }
        return $sum;
    }
    //计算一个数组asset_money，asset_product的和
    public function sumBranch($result){
        $sum = 0.0;
        foreach($result as $key=>$value){
            $sum = $sum + doubleval(str_replace(',','',$value['asset_money'])) + doubleval(str_replace(',','',$value['asset_product']));
        }
        return $sum;
    }

}