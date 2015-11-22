<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class PersonalCenterController extends BaseController{
    public function index(){
        $this->setHeader('个人中心');
        if(!D('Passport')->isLogged()){
            $this->redirect(U('Login/index'));
        }
        p(D('RepairRecord')->getUserRepairRecords($GLOBALS['e8']['mid']));
        die;
        $this->display();
    }
}