<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 15:20
 */
namespace Admin\Model;
use Think\Model;

class BranchModel extends Model{
    protected $tableName = "branch";
    /**根据用户id，查询用户负责的项目
    category  1银行数据 2 期货数据 3 现货库存结存
     */
    public function getBranchProjectByUserId($category,$user_id=null){
        $maps['user.id'] = $user_id;
        if($user_id == null) unset($maps['user.id']);
        $maps['project.category'] = array('eq',$category);
        $result = $this->join('user on user.branch_id = branch.id')
            ->join('project on project.branch_id = branch.id')
            ->field('project.id,project.name as name_project,project.branch_id,branch.name as branch_name')
            ->where($maps)
            ->order('branch.name')
            ->select();
        return $result;
    }
    //如果是业务操作员只获得对应部门，如果是管理员或者财务操作员获得所有部门
    public function getBranchNameByPower($power,$branch_id){
        $maps['user.branch_id'] = $branch_id;
        if($power>1)unset($maps['user.branch_id']);
        $result = $this->join('user on branch.id=user.branch_id')
                       ->where($maps)
                       ->field('branch.id,branch.name')
                       ->select();
        if($power>1)$result []['name']="所有部门";
        return $result;
    }
    //根据部门name获得部门的id
    public function getBranchIdByBranchName($branch_name){
        return $this->where('name='.'\''.$branch_name.'\'')->getField('id',true)[0];
    }
    //根据部门id获得部门name
    public function getBranchNameById($branch_id){
        return $this->where('id='.'\''.$branch_id.'\'')->getField('name',true)[0];
    }
}