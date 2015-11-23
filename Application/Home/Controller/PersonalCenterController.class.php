<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class PersonalCenterController extends BaseController{
    public function index(){
        $this->setHeader('个人中心');
        if(!D('Passport')->isLogged()){
            $this->redirect(U('Login/index'));
        }

        $userInfo = D('User')->getUserInfo($GLOBALS['e8']['mid']);
        $records = D('RepairRecord')->getUserRepairRecords($GLOBALS['e8']['mid']);
        $this->assign('userInfo',$userInfo);
        $this->assign('records',$records);
        $this->display();
    }
}