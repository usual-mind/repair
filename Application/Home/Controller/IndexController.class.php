<?php
namespace Home\Controller;

use Common\Controller\BaseController;
use Common\Model\RegisterModel;

use Think\Controller;

class IndexController extends  BaseController{
    public function index(){
        //print_r(D('RepairRecord')->addRepairRecord(array('problem_desc'=>'电脑爆炸了','computer_id'=>'3')));
        p(D('RepairRecord')->getUserRepairRecords('1'));
    }
}