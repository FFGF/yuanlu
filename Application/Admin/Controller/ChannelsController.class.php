<?php

namespace Admin\Controller;
use Think\Controller;

class ChannelsController extends Controller {

    public $adminInfo;

    public function _initialize() {
        header("Content-type:text/html;charset=utf-8");
        $this->checkLogin();
        $this->assign('is_root',$this->adminInfo['root']);
    }

    /* 空操作，用于输出404页面 */

    public function _empty() {
        $this->display('Common:404');
    }

    //检测用户是否登录
    private function checkLogin() {
        $this->adminInfo=session('admin');
        if (empty($this->adminInfo)) {
            if(CONTROLLER_NAME != 'Index'){
                $this->error('您还未登录，请先登录。', __ROOT__.'/Admin/');
            }
        }else{
            if(CONTROLLER_NAME == 'Index' && ACTION_NAME!='logout')
                redirect(__ROOT__.'/Admin/report');
        }
    }

    //分页
    protected function page($total) {
        $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : C('PAGESIZE');
        $page = new \Think\Page($total, $listRows);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $this->assign('_page', $page->show());
        return $page;
    }
}

