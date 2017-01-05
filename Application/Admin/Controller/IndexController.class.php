<?php

namespace Admin\Controller;

class IndexController extends ChannelsController {
    public function index(){
        $this->display();
    }
    //登录
    public function login(){
        $where['user_name'] = I('admin_id');
        $where['finish'] = 0;
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
                if($admin['power'] == 2){
                    redirect(__ROOT__.'/Admin/first_page-firstpage');
                }if($admin['power'] == 3){
                    redirect(__ROOT__.'/Admin/first_page-firstpage');
                }else{
                    redirect(__ROOT__.'/Admin/first_page-firstpage');
                }

            }
        }
    }

    public function logout() {
        \session('[destroy]');
        redirect(__ROOT__.'/Admin/index');
    }
}
