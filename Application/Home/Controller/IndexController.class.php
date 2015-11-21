<?php
namespace Home\Controller;

use Common\Controller\BaseController;

use Think\Controller;

class IndexController extends  BaseController{
    public function index(){
        /*$data['problem_desc'] = '电脑爆炸了';
        $data['computer_id'] = '4';
        var_dump(D('RepairRecord')->addRepairRecord($data));*/
        $data['problem_desc']  = '电脑爆炸了';
        $data['computer_id']  = '4';
        //$data['image_set_id']  = '';
        var_dump(D('RepairRecord')->addRepairRecord($data));
    }
}