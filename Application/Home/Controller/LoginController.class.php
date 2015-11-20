<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class LoginController extends BaseController{
    private $images = array();

    public function index(){
        if(D('Passport')->isLogged()){
            //TODO 已经登录 跳转到个人中心
            die("已经登录");
        }else{
            //未登录 跳转到step1
            $this->redirect('step1');
        }
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
            $this->redirect('step1',array('errMes'=>'请输入学号'));
        }
        //获取从第一步传来的学号
        $studentId = trim($_GET['studentId']);
        //判断用户是否已经存在
        if(D('User')->hasUser($studentId)){
            //该用户存在 直接登录
            if(D('Passport')->loginLocalWithoutPassword($studentId,true)){
                //TODO 登录成功 跳转到个人中心
                $this->redirect('Index/index');
            }else{
                E('登录失败!');
            }

        }

        $this->assign('studentId',$studentId);
        $this->setHeader('完善个人信息');
        //分配给Widget
        $departments = D('Classes')->getAllDepartment();
        foreach($departments as &$v){
            $v['url'] = U('SetClass/classInfowidget',array('pid'=>$v['id'],'type'=>'department'));
        }

        $header['title'] = '完善个人信息';
        $header['backUrl'] = U('Login/step1');

        $this->assign('header',$header);

        $this->assign('department',array($departments));
        $this->display();
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
        D('User')->addUser($user);
        //注册成功之后登录
        D('Passport')->loginLocalWithoutPassword($user['student_id'],true);
        //TODO 注册成功跳转到注册成功页面
    }
    /**
     * 退出登录
     */
    public function logout(){
        D('Passport')->logout();
        echo '退出成功!';
    }
}