<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Common\Model\RegisterModel;

use Think\Controller;

class IndexController extends  BaseController{
    public function index(){
        //$data['grade'] = '5';
        //$data['record_id'] = '1';
        //D('Evaluation')->addEvaluation($data,1,'牛逼！！！！');
        var_dump(D('DutyInfo')->isDuty(1));
    }
}