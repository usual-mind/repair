<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Common\Model\RegisterModel;

use Think\Controller;

class IndexController extends  BaseController{
    public function index(){
        $data['content'] = '12123123213';
        D('Comment')->addComment($data);
    }
}