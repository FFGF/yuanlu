<?php

namespace Admin\Controller;

class IndexController extends ChannelsController {
    public function index(){
        $this->display();
    }
    //登录
    public function login(){
        $where['user_name'] = I('admin_id');
        $admin = M('user')->where($where)->find();
        if(empty($admin)){
            $this->error('管理员不存在！');
        }
        else{
            $password = I('admin_login_pwd');
            if($admin['user_password'] != $password){
                $this->error('密码错误！');
            }
            else{
                session('admin', $admin);
                redirect(__ROOT__.'/Admin/report');
            }
        }
    }

    public function logout() {
        \session('[destroy]');
        redirect(__ROOT__.'/Admin/index');
    }
}
