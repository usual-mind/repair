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
     * 如果学号存在直接登录到个人中心
     * 如果不存在 到第二步完善用户信息 再注册
     */
    public function step1(){
        $this->setTitle("学号");
        if(!empty($_GET['errMes'])){
            $this->assign('showTip',"tipBox('{$_GET['errMes']}');");
        }
        //判断用户是否已经存在
        if(D('user')->hasUser()){
           //TODO 用户存在直接跳转到个人中心
        }
        //将下一步按钮URL分配过去
        $this->assign('nextUrl',U('Login/step2'));
        $this->display();
    }

    /**
     * 第二步
     * 完善用户信息
     */
    public function step2(){
        $studentId = isset($_GET['studentId'])?$_GET['studentId']:NULL;
        if(!isset($_GET['studentId'])){
            //如果没有传入studentId返回第一步
            $this->redirect('step1',array('errMes'=>'学号没有传入'));
        }
        //获取从第一步传来的学号
        $studentId = intval($_GET['studentId']);
        $this->assign('studentId',$studentId);
        $this->display();
        //调用选择班级控制器
        $SetClassController = new SetClassController();
        $SetClassController->index();
    }

    /**
     * 处理注册
     */
    public function doRegister(){
        //获取用户信息
        $user['student_id'] = I('post.studentId',0);
        $user['name'] = I('post.name','','htmlspecialchars');
        $user['classes_id'] = I('post.classID',0);
        $user['tel_num'] = I('post.tel',0);
        //开始注册
        D('user')->addUser($user);
        //TODO 注册成功跳转到注册成功页面
    }
}