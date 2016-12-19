<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 12:46
 */
namespace Admin\Controller;

use Think\Model;

class FirstPageController extends ChannelsController{

    public function firstPage(){
        $this->display();
    }
}