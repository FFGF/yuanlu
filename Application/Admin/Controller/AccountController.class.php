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
        //dump($countnum);
        $result = $project->field('id')
            ->where($maps)
            ->select();
        //dump($result[0]["id"]);

        //将部门名字转化为ID
        $data['branch_id']=$result[0]["id"];
        //dump($data);
        $user = M("user");
        $user->add($data);


    }

    //添加的公司信息获取
    public function saveData2(){
        $data = I('post.');
        //dump($data);
        //die();
        //return $data;
        $branch = M('branch');
        $branch->add($data);
    }

    //添加的项目信息获取
    public function saveData3(){
        $data = I('post.');
        dump($data);
        $project = M('branch');
        $maps['name'] = $data['branch_id'];
        $result = $project->field('id')
            ->where($maps)
            ->select();
        //dump($result[0]["id"]);
        //将部门名字转化为ID
        $data['branch_id']=$result[0]["id"];
        dump($data);
        $project = M("project");
        $project->add($data);
    }
}