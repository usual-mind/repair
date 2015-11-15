<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class LoginController extends BaseController{
    private $images = array();
    public function index(){
        $this->redirect('step1');
    }
    public function step1(){
        $this->setTitle("学号");
        if(!empty($_GET['errMes'])){
            $this->assign('showTip',"tipBox('{$_GET['errMes']}');");
        }
        //将下一步按钮URL分配过去
        $this->assign('nextUrl',U('Login/step2'));
        $this->display();
    }
    public function step2(){
        $studentId = isset($_GET['studentId'])?$_GET['studentId']:NULL;
        if(!isset($_GET['studentId'])){
            //如果没有传入studentId返回第一步
            $this->redirect('step1',array('errMes'=>'学号没有传入'));
        }
        $studentId = intval($_GET['studentId']);

        $this->display();
    }
}