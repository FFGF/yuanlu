<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/23
 * Time: 13:58
 */
namespace Admin\Model;
use Think\Model;

class WorkingCapitalModel extends Model{
    protected $tableName = "working_capital";
    //获得某一个部门，一段时期内的资产运营数据,如果branch_id为null则获得所有的部门
    public function getDataByBranchId($branch_id,$date){
        $working_capital_array = [];
        if($branch_id == null){
            foreach($date as $key=>$value){
                $maps['effect_date'] = array('elt',$value);
                $effect_date = $this->where($maps)->order('effect_date desc')->getField('effect_date',true);
                $maps['effect_date'] = array('eq',$effect_date[0]);
                $accumulate = $this->where($maps)->order('effect_date desc')->getField('accumulate',true);
                $working_capital_array [] = array_sum($accumulate);
            }
        }else{
            foreach($date as $key=>$value){
                $maps['effect_date'] = array('elt',$value);
                $maps['branch_id'] = $branch_id;
                $accumulate = $this->where($maps)->order('effect_date desc')->getField('accumulate',true);
                $working_capital_array [] =  intval($accumulate[0]);
            }
        }
        return $working_capital_array;
    }

}