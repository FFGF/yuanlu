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
        $data['branch_id']=$result[0]["id"];
        $user = M("user");
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
    }
    public function indexshowdata(){
        $user=M('user');
        $result=$user->join('branch on branch.id = branch_id')
            ->join('project on project.branch_id = branch.id')
            ->field('user.user_name as username,branch.name as branchname,project.name as projectname')
            ->select();
        $result2=$user->join('branch on branch.id = branch_id')
            ->join('project on project.branch_id = branch.id')
            ->field('user.user_name as username,branch.name as branchname')
            ->select();
        $resuser=$user->field('user_name')->select();

        $res2=$user->join('branch on branch.id = branch_id')
            ->field('user_name,branch.name')->select();

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

        //$jj2=0;
        for($i2=0;$i2<count($res2);$i2++){
            $temp=$res2[$i2]['user_name'];
            //dump($temp);
            //$jj2=0;
            for($j2=0;$j2<count($result);$j2++){
                if($result[$j2]['username'] == $temp){
                    $resall[$i2]['username']=$result[$j2]['username'];
                    $resall[$i2]['branchname']=$result[$j2]['branchname'];
                    break;
                }
            }
        }

        for($k=1;$k<=count($resall);$k++){
            $tempres=$res[$k];
            //dump($tempres);
            $resall[$k]['projectlist']=$tempres;
        }
        $this->assign('resall',$resall);
        $this->display();
    }


    public function changepwd(){
        //获取当前所登录账户的id，并提取出该条信息
        $maps['user.id'] = session('admin')['id'];
        $user = M('user');
        $before_result=$user->field('user.id,user.user_name,user.user_password')->where($maps)->select();

        $before_result1=$user->where($maps)->select();
        $before_username=$before_result[0]['user_name'];
        if($_POST){
            //获取从页面传来的   原密码和新密码数据
            $data = I('post.');
            //第一个if判断页面新密码有输入
            if(!empty($data['user_password'])) {
                //第二个if 判断  所输入的原密码  和 之前数据库中的原密码是   一样的
                if ($data['user_password'] == $before_result[0]['user_password']) {
                    $data['user_password'] = $data['newuser_password'];
                    $before_result1[0]['user_password'] = $data['user_password'];
                    //修改原密码为新密码
                    $user->data($before_result1[0])->where($maps)->save();
                    $this->success("修改密码成功");
                }
                else{
                    //echo "原密码错误<br>";
                    $this->error("原密码输入错误");
                }
            }
        }else{
            $this->assign('before_username',$before_username);
            $this->display();
        }
    }


    //添加的项目信息获取
    public function piechart(){
        //选择一个部门&所有部门 -------选择
        $branch = M('branch');
        $branchlist=$branch->field('brancd.name','brancd.id')->select();
        //dump(count($branchlist));
        $branchlist[count($branchlist)+1]["name"]="所有部门";
        //dump($branchlist);
        //die();
        $this->assign('branchlist',$branchlist);

        //post  获取的 部门名字&&日期
        $data = I('post.');

        if($data['branch_id']!="所有部门") {

            $maps['name'] = $data['branch_id'];
            $maps['effect_date'] = $data['user_date'];

            //日期减少一天
            $tempdate = $data['user_date'];
            //echo date("Y-m-d",strtotime("$tempdate -1   day"));
            $dateshu = date("Y-m-d", strtotime("$tempdate -1   day"));
            //dump($dateshu);


            $maps2['name'] = $data['branch_id'];
            $maps2['effect_date'] = $dateshu;

            //数据    当天数据
            $result = $branch->join('perday_data_item on perday_data_item.branch_id = branch.id')
                ->field('name,perday_data_item.branch_id,perday_data_item.project_id,perday_data_item.asset_money,perday_data_item.asset_product,perday_data_item.finance_debt,perday_data_item.receivable,perday_data_item.payable,perday_data_item.effect_date')
                ->where($maps)
                ->select();
            $sumassetmoney = 0;
            $sumassetproduct = 0;
            $sumfinancedebt = 0;
            $sumreceivable = 0;
            $sumpayable = 0;
            for ($i = 0; $i <= count($result); $i++) {
                $sumassetmoney = $sumassetmoney + (float)$result[$i]['asset_money'];
                $sumassetproduct = $sumassetproduct + (float)$result[$i]['asset_product'];
                $sumfinancedebt = $sumfinancedebt + (float)$result[$i]['finance_debt'];
                $sumreceivable = $sumreceivable + (float)$result[$i]['receivable'];
                $sumpayable = $sumpayable + (float)$result[$i]['payable'];
            }
            $this->assign('sumfinancedebt', $sumfinancedebt);
            $this->assign('sumreceivable', $sumreceivable);
            $this->assign('sumpayable', $sumpayable);


            //数据    前天数据
            $result2 = $branch->join('perday_data_item on perday_data_item.branch_id = branch.id')
                ->field('name,perday_data_item.branch_id,perday_data_item.project_id,perday_data_item.asset_money,perday_data_item.asset_product,perday_data_item.finance_debt,perday_data_item.receivable,perday_data_item.payable,perday_data_item.effect_date')
                ->where($maps2)
                ->select();
            $sumassetmoney2 = 0;
            $sumassetproduct2 = 0;
            $sumfinancedebt2 = 0;
            $sumreceivable2 = 0;
            $sumpayable2 = 0;
            for ($j = 0; $j <= count($result2); $j++) {
                $sumassetmoney2 = $sumassetmoney2 + (float)$result2[$j]['asset_money'];
                $sumassetproduct2 = $sumassetproduct2 + (float)$result2[$j]['asset_product'];
                $sumfinancedebt2 = $sumfinancedebt2 + (float)$result2[$j]['finance_debt'];
                $sumreceivable2 = $sumreceivable2 + (float)$result2[$j]['receivable'];
                $sumpayable2 = $sumpayable2 + (float)$result2[$j]['payable'];
            }


            $tempassetmoney = $sumassetmoney - $sumassetmoney2;
            $tempassetproduct = $sumassetproduct - $sumassetproduct2;
            $tempfinancedebt = $sumfinancedebt - $sumfinancedebt2;
            $this->assign('tempassetmoney', $tempassetmoney);
            $this->assign('tempassetproduct', $tempassetproduct);
            $this->assign('tempfinancedebt', $tempfinancedebt);


            //$value1=(float)200;
            $value = $sumassetmoney;
            //$value2=(float)400;
            $value2 = $sumassetproduct;
            $this->assign('value1', $value);
            $this->assign('value2', $value2);


            $this->display();


        }
        //if语句结束
        else{

            $maps['effect_date'] = $data['user_date'];
            //dump($data['user_date']);

            //日期减少一天
            $tempdate = $data['user_date'];
            $dateshu = date("Y-m-d", strtotime("$tempdate -1   day"));
            //dump($dateshu);

            $maps2['effect_date'] = $dateshu;

            //所有部门---数据    当天数据
            $alldata = M('perday_data_item');


            $result = $alldata->field('perday_data_item.branch_id,perday_data_item.project_id,perday_data_item.asset_money,perday_data_item.asset_product,perday_data_item.finance_debt,perday_data_item.receivable,perday_data_item.payable,perday_data_item.effect_date')
                ->where($maps)
                ->select();

            $sumassetmoney = 0;
            $sumassetproduct = 0;
            $sumfinancedebt = 0;
            $sumreceivable = 0;
            $sumpayable = 0;
            for ($i = 0; $i <= count($result); $i++) {
                $sumassetmoney = $sumassetmoney + (float)$result[$i]['asset_money'];
                $sumassetproduct = $sumassetproduct + (float)$result[$i]['asset_product'];
                $sumfinancedebt = $sumfinancedebt + (float)$result[$i]['finance_debt'];
                $sumreceivable = $sumreceivable + (float)$result[$i]['receivable'];
                $sumpayable = $sumpayable + (float)$result[$i]['payable'];
            }

            $this->assign('sumfinancedebt', $sumfinancedebt);
            $this->assign('sumreceivable', $sumreceivable);
            $this->assign('sumpayable', $sumpayable);


            //数据    前天数据
            $result2 = $alldata->field('perday_data_item.branch_id,perday_data_item.project_id,perday_data_item.asset_money,perday_data_item.asset_product,perday_data_item.finance_debt,perday_data_item.receivable,perday_data_item.payable,perday_data_item.effect_date')
                ->where($maps2)
                ->select();

            $sumassetmoney2 = 0;
            $sumassetproduct2 = 0;
            $sumfinancedebt2 = 0;
            $sumreceivable2 = 0;
            $sumpayable2 = 0;
            for ($j = 0; $j <= count($result2); $j++) {
                $sumassetmoney2 = $sumassetmoney2 + (float)$result2[$j]['asset_money'];
                $sumassetproduct2 = $sumassetproduct2 + (float)$result2[$j]['asset_product'];
                $sumfinancedebt2 = $sumfinancedebt2 + (float)$result2[$j]['finance_debt'];
                $sumreceivable2 = $sumreceivable2 + (float)$result2[$j]['receivable'];
                $sumpayable2 = $sumpayable2 + (float)$result2[$j]['payable'];
            }

            //计算三个和前一天差值的数据
            $tempassetmoney = $sumassetmoney - $sumassetmoney2;
            $tempassetproduct = $sumassetproduct - $sumassetproduct2;
            $tempfinancedebt = $sumfinancedebt - $sumfinancedebt2;
            $this->assign('tempassetmoney', $tempassetmoney);
            $this->assign('tempassetproduct', $tempassetproduct);
            $this->assign('tempfinancedebt', $tempfinancedebt);


            //$value1=(float)200;
            $value = $sumassetmoney;
            //$value2=(float)400;
            $value2 = $sumassetproduct;
            $this->assign('value1', $value);
            $this->assign('value2', $value2);

            $this->display();
        }

    }

    public function userManage(){
        $User = D("User");
        $user_result = $User->getUserManage();

        //权限等级是3，表示是管理员权限
        //dump($user_result);

        $this->assign('user',$user_result);
        $this->display();
    }




    //添加新账户
    public function newuser(){
        $branch = M('branch');
        $branchlist=$branch->field('brancd.name','branch.id')->select();
        $this->assign('branchlist',$branchlist);

        //使用thinkphp的U函数来传递----参数--人名---点击的是哪个人左上角的修改按钮
        //打算获取人名后，先判断该人名是否存在（理论上存在，若不存在则提示用户重新输入）
        //得到人名后----再数据库中找对应id，，然后update该条数据
        //dump(I('username'));

        $this->display();

    }

    public function newusersave(){

        $data = I('post.');
        $project = M('branch');
        $maps['name'] = $data['branch_id'];
        //本想着计算该账户表中有几条数据
        $countnum=$project->count();
        $result = $project->field('id')
            ->where($maps)
            ->select();
        $data['branch_id']=$result[0]["id"];


        //dump($data);
        //dump($data['user_name']);
        //die();

        $user = M("user");

        $user->add($data);
        $this->success("插入数据成功");

        //if($data['user_name']==NULL){
        //    $user->add($data);
        //    $this->success("插入数据成功");
        //}
        //else{
        //    $user->update($data);
        //    $this->success("编辑数据成功");
        //}

    }



    //更新账户信息
    public function updatenewuser(){
        $branch = M('branch');
        $branchlist=$branch->field('brancd.name','branch.id')->select();
        $this->assign('branchlist',$branchlist);


        //dump(I('username'));
        //使用thinkphp的U函数来传递----参数--人名---点击的是哪个人左上角的修改按钮
        //打算获取人名后，先判断该人名是否存在（理论上存在，若不存在则提示用户重新输入）
        //得到人名后----再数据库中找对应id，，然后update该条数据


        //先判断该人名如否存在nono,,,怎从账户管理页面跳转过来，肯定存在
        $tempusername=I('username');
        //dump($tempusername);
        $user = M('user');

        $maps['user_name']=$tempusername;

        $resultname=$user->field('id')->where($maps)->select();
        //dump($resultname);
        //dump($resultname[0]);

        $this->assign('uname',$resultname[0]['id']);
        $this->display();
    }

    public function updatenewusersave(){

        //dump(updatenewuser());

        $data = I('post.');
        $project = M('branch');
        $maps['name'] = $data['branch_id'];
        //本想着计算该账户表中有几条数据
        $countnum=$project->count();
        $result = $project->field('id')
            ->where($maps)
            ->select();
        $data['branch_id']=$result[0]["id"];

        //dump($data);
        //dump($data['uname']);
        //dump($data['user_name']);
        //die();


        $user = M("user");
        $maps2['id']=$data['uname'];
        $user->where($maps2)->save($data);
        $this->success("修改账户数据成功");

    }


    //public function test(){
    //    dump(updatenewuser());
    //    $this->display();
    //}



    public function newbankaccount(){
        $branch = M('branch');
        $branchlist=$branch->field('brancd.name','branch.id')->select();
        $this->assign('branchlist',$branchlist);

        $this->display();
    }

    public function newbankaccountsave(){


        $data = I('post.');
        $project = M('branch');
        $maps['name'] = $data['branch_id'];
        $result = $project->field('id')
            ->where($maps)
            ->select();
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

        if($data['value']=='添加'){
            $project = M("project");

            $result4 = $project->field('id')
                ->where($data)
                ->select();
            //dump($result4[0]);


            //这个if-else主要是判断该账户是否已经存在，若存在则不插入；若不存在，则插入
            if($result4[0] ==NULL){
                $project->add($data);
                $this->success("插入银行账户成功");
            }
            else{
                $this->success("该账户已存在");
            }

        }
        if($data['value']=='删除'){
            $project = M("project");

            //dump($data['name']);
            $maps4['name'] = $data['name'];
            $result = $project->field('id')
                ->where($maps4)
                ->select();

            //dump($result);
            //dump($result[0]);
            //将项目名字转化为ID

            $project->where($result[0])->delete();
            $this->success("删除银行账户成功");
        }

    }


    //public function test(){
    //    $data = I('post.');
    //   dump($data);
    //$this->display();

    //}




    //从账户页面跳转   进入     建立对应用户的--新的银行账户
    public function newbankaccot(){
        $tepusername=I('username');
        $tepbranchname=I('branchname');
        $this->assign('username',$tepusername);
        $this->assign('branchname',$tepbranchname);


        $this->display();

    }

    public function newbankaccotsave(){

        $data = I('post.');

        $project = M('branch');
        $maps['name'] = $data['branchname'];
        $result = $project->field('id')
            ->where($maps)
            ->select();
        //将部门名字转化为ID
        $data['branch_id']=$result[0]["id"];
        //dump($data);
        $data['category']='1';


        $projectlist = M('project');
        $maps5['branch_id'] = $data['branch_id'];
        $resultlist = $projectlist->field('name')
            ->where($maps5)
            ->select();
        //dump($resultlist);

        //dump($resultlist[0]);
        //dump($resultlist[0]['name']);
        //dump(count($resultlist));


        if ($data['bank_category']=='中国')
            $data['bank_category']='0';
        if ($data['bank_category']=='美国')
            $data['bank_category']='1';

        //dump($data);


        //若进行添加   银行账户
        if($data['value']=='添加'){

            //先循环判断如否存在该银行账户     若存在：提示该账户已存在
            for($i=0;$i<count($resultlist);$i++){
                if($data['name']==$resultlist[$i]['name']){
                    $this->error('该银行账户已存在');
                }
            }

            //若循环查找完了   则证明不存在该银行账户   则插入该条数据
            if($i==count($resultlist)){
                $projectlist->add($data);
                $this->success('银行账户添加成功');
            }
        }


        //若进行删除   银行账户
        //if($data['value']=='删除'){
        else{

            //先循环判断如否存在该银行账户     若存在：则需要删除该条数据
            for($j=0;$j<count($resultlist);$j++){
                if($data['name']==$resultlist[$j]['name']){
                    $mapstep['name']=$data['name'];
                    $mapstep['branch_id']=$data['branch_id'];
                    $mapstep['category']=1;
                    $mapstep['bank_category']=$data['bank_category'];

                    $delproid=$projectlist->field('id')->where($mapstep)->select();
                    //dump($delproid[0]['id']);

                    //删除该条id数据
                    $projectlist->where($delproid[0])->delete();
                    $this->success('该银行账户删除成功');
                    break;
                }
            }

            //若循环查找完了   则证明不存在该银行账户   则提示---不存在该银行账户
            if($j==count($resultlist)){
                $this->error('该银行账户不存在');
            }

        }

    }


    //账户管理页面的   删除用户按钮
    public function deluser(){
        $tempuser=I('username');
        //dump($tempuser);

        $userlist=M('user');
        $map['user_name']=$tempuser;
        $userid=$userlist->field('id')->where($map)->select();
        //dump($userid);
        //dump($userid[0]);
        //dump($userid[0]['id']);

        //删除该id的    user表中的这条数据，即这个用户
        $map2['id']=$userid[0]['id'];
        $userlist->where($map2)->delete();
        $this->success('该用户删除成功');

    }



}