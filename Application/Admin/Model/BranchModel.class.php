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
    public function getBranchProjectByUserId($user_id,$category){
        $maps['user.id'] = $user_id;
        $maps['project.category'] = array('eq',$category);
        $result = $this->join('user on user.branch_id = branch.id')
            ->join('project on project.branch_id = branch.id')
            ->field('project.id,project.name as name_project,project.branch_id,branch.name as branch_name')
            ->where($maps)
            ->select();
        return $result;
    }
}