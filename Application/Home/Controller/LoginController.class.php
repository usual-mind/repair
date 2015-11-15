<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class LoginController extends BaseController{
    private $images = array();

    public function index(){
        //重定向到step1
        $this->redirect('step1');
    }

    /**
     * 第一步
     * 用户输入学号
     */
    public function step1(){
        $this->setTitle("学号");
        if(!empty($_GET['errMes'])){
            $this->assign('showTip',"tipBox('{$_GET['errMes']}');");
        }
        //将下一步按钮URL分配过去
        $this->assign('nextUrl',U('Login/step2'));
        $this->display();
    }

    /**
     * 第二步
     * 完善信息
     */
    public function step2(){
        $studentId = isset($_GET['studentId'])?$_GET['studentId']:NULL;
        if(!isset($_GET['studentId'])){
            //如果没有传入studentId返回第一步
            $this->redirect('step1',array('errMes'=>'学号没有传入'));
        }
        $studentId = intval($_GET['studentId']);
        //TODO 对$studentId进行验证及注册
        $this->display();
    }
}