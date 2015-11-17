<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Common\Model\RegisterModel;

use Think\Controller;

class IndexController extends  BaseController{
    public function index(){
        p(D('Notify')->sendNotify(1,'received_evaluation',
            array('name'=>'taoyu','computer_name'=>'东芝 L800','record_link'=>'http://www.baidu.com'),2));
    }
}