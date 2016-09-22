<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/11
 * Time: 18:46
 */
namespace Admin\Controller;

class AccountController extends ChannelsController{
    public function index(){
        $branch = M('branch');
        $branchlist=$branch->field('brancd.name','branch.id')->select();
        $this->assign('branchlist',$branchlist);
        $this->display();
    }

    //添加的账号信息获取
    public function saveData(){
        $data = I('post.');
        $project = M('branch');
        $maps['name'] = $data['branch_id'];
        //本想着计算该账户表中有几条数据
        $countnum=$project->count();
        $result = $project->field('id')
            ->where($maps)
            ->select();
        //将部门名字转化为ID
        $data['branch_id']=$result[0]["id"];
        //dump($data);
        $user = M("user");
        //dump($data['user_password']);
        $user->add($data);
        $this->success("插入数据成功");
    }
    //添加的公司信息获取
    public function saveData2(){
        $data = I('post.');
        $branch = M('branch');
        $branch->add($data);
        $this->success("插入数据成功");
    }

    //添加的项目信息获取
    public function saveData3(){
        $data = I('post.');
        $project = M('branch');
        $maps['name'] = $data['branch_id'];
        $result = $project->field('id')
            ->where($maps)
            ->select();
        //将部门名字转化为ID
        $data['branch_id']=$result[0]["id"];

        //将所选汉子转为数字存入数据库
        if ($data['category']=='银行')
            $data['category']='1';
        if ($data['category']=='期货')
            $data['category']='2';
        if ($data['category']=='存货')
            $data['category']='3';
        if ($data['bank_category']=='中国')
            $data['bank_category']='0';
        if ($data['bank_category']=='美国')
            $data['bank_category']='1';

        $project = M("project");
        $project->add($data);
        $this->success("插入数据成功");
    }
}