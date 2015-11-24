<?php
namespace Home\Controller;

use Common\Controller\BaseController;

use Think\Controller;

class IndexController extends  BaseController{
    public function index()
    {
        //p(D('User')->getUserInfo(1));
        //unset($_SESSION['images']);
        p($_SESSION);
    }
}