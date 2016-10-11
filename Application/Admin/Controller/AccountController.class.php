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




    public function indexshowdata(){
        $user=M('user');
        $result=$user->join('branch on branch.id = branch_id')
            ->join('project on project.branch_id = branch.id')
            ->field('user.user_name as username,branch.name as branchname,project.name as projectname')
            ->select();
        //dump($result);
        //显示全部的project表的信息

        $result2=$user->join('branch on branch.id = branch_id')
            ->join('project on project.branch_id = branch.id')
            ->field('user.user_name as username,branch.name as branchname')
            ->select();
        //dump($result2);
        //显示全部的project表的信息（去除项目，只含有人、部门）


        $resuser=$user->field('user_name')->select();
        //dump($resuser);


        $res2=$user->join('branch on branch.id = branch_id')
            ->field('user_name,branch.name')->select();
        //dump($res2);

        $jj=0;
        for($i=0;$i<count($res2);$i++){
            $temp=$res2[$i]['user_name'];
            //dump($temp);
            $jj=0;
            for($j=0;$j<count($result);$j++){
                if($result[$j]['username'] == $temp){
                    $res[$i][$jj]['projectname']=$result[$j]['projectname'];
                    $jj++;
                }
            }
        }

        //dump($res);
        //显示  每个部门的所有的项目 （按project顺序来）



        //$jj2=0;
        for($i2=0;$i2<count($res2);$i2++){
            $temp=$res2[$i2]['user_name'];
            //dump($temp);
            //$jj2=0;
            for($j2=0;$j2<count($result);$j2++){
                if($result[$j2]['username'] == $temp){
                    //$resall[$i2][$jj2]['username']=$result[$j2]['username'];
                    //$resall[$i2][$jj2]['branchname']=$result[$j2]['branchname'];
                    //$jj2++;
                    $resall[$i2]['username']=$result[$j2]['username'];
                    $resall[$i2]['branchname']=$result[$j2]['branchname'];
                    break;
                }
            }
        }

        //dump($resall);
        //显示 人员列表，对应部门   （按project顺序来）（与上面部门对应）



        for($k=1;$k<=count($resall);$k++){
            $tempres=$res[$k];
            //dump($tempres);
            $resall[$k]['projectlist']=$tempres;
        }
        //dump($resall);
        //显示  人员、部门、对应的好几个项目

        //dump($resall['projectlist']);   显示是NULL

        $this->assign('resall',$resall);

        //$this->assign('result',$result);
        //呈现页面没有用到

        $this->display();

    }


}