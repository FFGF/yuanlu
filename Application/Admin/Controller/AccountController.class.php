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

        //dump($data['user_password']);
        $user->add($data);

        $this->success("插入数据成功");
        die();

    }

    //添加的公司信息获取
    public function saveData2(){
        $data = I('post.');
        //dump($data);
        //die();
        //return $data;
        $branch = M('branch');
        $branch->add($data);
        $this->success("插入数据成功");
        die();
    }

    //添加的项目信息获取
    public function saveData3(){
        $data = I('post.');
        //dump($data);
        $project = M('branch');
        $maps['name'] = $data['branch_id'];
        $result = $project->field('id')
            ->where($maps)
            ->select();
        //dump($result[0]["id"]);
        //将部门名字转化为ID
        $data['branch_id']=$result[0]["id"];
        //dump($data);

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

        //dump($data);
        $project = M("project");
        $project->add($data);
        $this->success("插入数据成功");
        die();

    }


    //数据呈现页面（人员，所属部门，所负责项目）
    public function index2(){
        //$projectlist=$this->saveBranch_name();
        //$this->assign('projectlist',$projectlist);
        //$this->display();


        $perday_data_item=M('perday_data_item');
        $alldata=$perday_data_item->field('perday_data_item.id','perday_data_item.user_id','perday_data_item.branch_id','perday_data_item.project_id')->select();
        //dump($alldata);



        $project = M('branch');
        for($i=0;$i<count($alldata);$i++){
            $maps['id'] = $alldata[$i]['branch_id'];
            //dump($alldata[$i]['branch_id']);
            $result = $project->field('name')
                ->where($maps)
                ->select();
            //dump($result[0]);
            //dump($result[0]["name"]);
            //将部门名字转化为ID
            $alldata[$i]['branch_name']=$result[0]["name"];

            if($i==1){
                $temp=$alldata[0]['branch_name'];
                if($alldata[$i]['branch_name']==$temp){
                    $temp=$alldata[$i]['branch_name'];
                    $alldata[$i]['branch_name']='';
                }
                else{
                    $temp=$alldata[$i]['branch_name'];
                }
            }
            if($i>1) {
                //$temp = $alldata[0]['branch_name'];
                if ($alldata[$i]['branch_name'] == $temp) {
                    $temp = $alldata[$i]['branch_name'];
                    $alldata[$i]['branch_name'] = '';
                }
                else {
                    $temp = $alldata[$i]['branch_name'];
                    //$alldata[$i]['branch_name'] = '';
                }
            }


        }




        $projectuser = M('user');
        for($j=0;$j<count($alldata);$j++){
            $maps['id'] = $alldata[$j]['user_id'];
            //dump($alldata[$i]['branch_id']);
            $result = $projectuser->field('user_name')
                ->where($maps)
                ->select();
            //dump($result[0]);
            //dump($result[0]["name"]);
            //将部门名字转化为ID
            $alldata[$j]['user_name']=$result[0]["user_name"];


            if($j==1){
                $temp2=$alldata[0]['user_name'];
                if($alldata[$j]['user_name']==$temp2){
                    $temp2=$alldata[$j]['user_name'];
                    $alldata[$j]['user_name']='';
                }
                else{
                    $temp2=$alldata[$j]['user_name'];
                }
            }
            if($j>1) {
                //$temp = $alldata[0]['branch_name'];
                if ($alldata[$j]['user_name'] == $temp2) {
                    $temp2 = $alldata[$j]['user_name'];
                    $alldata[$j]['user_name'] = '';
                }
                else {
                    $temp2 = $alldata[$j]['user_name'];
                    //$alldata[$i]['branch_name'] = '';
                }
            }



        }



        $project2 = M('project');
        for($k=0;$k<count($alldata);$k++){
            $maps['id'] = $alldata[$k]['project_id'];
            //dump($alldata[$i]['branch_id']);
            $result = $project2->field('name')
                ->where($maps)
                ->select();
            //dump($result[0]);
            //dump($result[0]["name"]);
            //将部门名字转化为ID
            $alldata[$k]['project_name']=$result[0]["name"];
        }



        $this->assign('alldata',$alldata);
        $this->display();



    }

}