<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/9
 * Time: 17:03
 */
namespace Admin\Model;
use Think\Model;

class UserModel extends Model{
    protected $tableName = "user";
    /** 获得操作员 部门 项目数据格式
    ["严一祥"=>['海外现货一部','现货库存结存','现货库存结存'],
    "严二祥"=>['海外现货二部',"现货库存结存","现货库存结存"]];*/

    public function getUserManage(){
        $maps['power'] = array('in',['1','2']);
        $user_list = $this->where($maps)->getField("user_name",true);
        $formatData = [];
        $branch = M('branch');
        foreach($user_list as $value){
            $temp_maps['user.user_name'] = $value;
            $branch_name = $this->join("branch on user.branch_id = branch.id")->where($temp_maps)->field('branch.name')->find()['name'];
            $formatData[$value][] = $branch_name;
            $temp_branch['branch.name'] = $branch_name;
            $project_name_list = $branch->join('project on branch.id=project.branch_id')->where($temp_branch)->field('project.name')->select();
            foreach($project_name_list as $k=>$v){
                $formatData[$value][] = $v['name'];
            }
        }
        return $formatData;
    }
}
