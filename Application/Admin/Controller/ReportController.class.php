<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/9
 * Time: 0:01
 */
namespace Admin\Controller;

class ReportController extends ChannelsController{
    public function index(){
        $project = M('branch');
        $maps['user.id'] = session('admin')['id'];
        $maps['project.category'] = array('eq','2');
        //期货数据
        $result_qihuo = $project->join('user on user.branch_id = branch.id')
                          ->join('project on project.branch_id = branch.id')
                          ->field('project.id,project.name as name_project,project.branch_id,branch.name as branch_name')
                          ->where($maps)
                          ->select();
        //银行数据
        $maps['project.category'] = array('eq','1');
        $result_bank = $project->join('user on user.branch_id = branch.id')
            ->join('project on project.branch_id = branch.id')
            ->field('project.id,project.name as name_project,project.branch_id,branch.name as branch_name,project.category')
            ->where($maps)
            ->select();
        //现货库存结存
        $maps['project.category'] = array('eq','3');
        $result_cunhuo = $project->join('user on user.branch_id = branch.id')
            ->join('project on project.branch_id = branch.id')
            ->field('project.id,project.name as name_project,project.branch_id,branch.name as branch_name,project.category')
            ->where($maps)
            ->select();

        $branch = M('branch')->where('id='.session('admin')['branch_id'])->getField('name');
        $this->assign('power',session('admin')['power']);
        $this->assign('branch',$branch);
        $this->assign('project_qihuo', $result_qihuo);
        $this->assign('project_bank', $result_bank);
        $this->assign('project_cunhuo',$result_cunhuo);
        $this->display();
}

    public function bank(){
        $project = M('project');
        $result = $project->join('branch on project.branch_id = branch.id')
            ->field('project.*,branch.name as branch_name')->select();

        $branch_name = M('branch')->getField('name',true);

        $formatData = [];

        foreach($branch_name as $key=>$value){
            $formatData[$value] = $this->getBankData($result,$value);
        }
        $this->assign('bankdata',$formatData);
        $this->display();
    }

    public function getBankData($result,$value){
        $data = [];
        foreach($result as $key=>$item) {
            if ($item['branch_name'] == $value) {
                $data[] = $item;
            }
        }

        return $data;

    }

    public function saveData(){
        $perday_data_item = M("perday_data_item");
        $data = I('post.');
        $form_data = $this->formatData($data);

        foreach($form_data as $key=>$value){
           $perday_data_item->add($value);
        }
        $this->success("插入数据成功");
        die();

    }

    //对前台传递过来的数据进行处理
    public function formatData($data){
        $formatdata = [];
        $item = [];
        $i = 0;
       foreach($data as $key=>$value){
           $item[$this->pickKey($key)] = $value;
           $i++;
           if($i%6 == 0){
               $item['project_id'] = preg_replace('/\D/s', '', $key);
               $item['user_id'] = session('admin')['id'];
               $item['branch_id'] = session('admin')['branch_id'];
               $formatdata[$i/6] = $item;
               $item = [];
           }
       }
        return $formatdata;
    }

    //将字符串中的key提取出来
    public function pickKey($str){
        return substr($str,0,strpos($str,preg_replace('/\D/s', '', $str)));
    }

}