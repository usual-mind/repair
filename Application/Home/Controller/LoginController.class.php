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
            //获取传入的参数
            isset($_GET['errMes']) && $get['errMes'] = trim($_GET['errMes']);
            isset($_GET['returnUrl']) && $get['returnUrl'] = trim($_GET['returnUrl']);
            //未登录 跳转到step1
            $this->redirect('step1',$get);
        }
    }

    /**
     * 第一步
     * 用户输入学号
     * 如果学号存在直接登录到个人中心
     * 如果不存在 到第二步完善用户信息 再注册
     */
    public function step1(){
        //注册或登录完成后返回的url
        $param = array();
        empty($_GET['returnUrl']) || $param = array('returnUrl'=>$_GET['returnUrl']);

        $this->setTitle('学号');
        if(!empty($_GET['errMes'])){
            $errMes = base64_decode($_GET['errMes']);
            $this->assign('showTip',"tipBox('{$errMes}');");
        }
        //将下一步按钮URL分配过去
        $this->assign('nextUrl',U('Login/step2',$param));
        $this->display();
    }
    /**
     * 第二步
     * 完善用户信息
     *
     * //如果$returnUrl为空 登录或注册完成后跳转到首页 否则跳转到$returnUrl去
    if(empty($_GET['returnUrl'])) {
    $returnUrl = U();
    }else{
    $returnUrl = base64_decode($_GET['returnUrl']);
    }
     */
    public function step2(){
        //注册或登录完成后返回的url
        $param = array();
        empty($_GET['returnUrl']) || $param = array('returnUrl'=>$_GET['returnUrl']);

        $studentId = isset($_GET['studentId'])?$_GET['studentId']:NULL;
        if(!isset($_GET['studentId'])){
            //如果没有传入studentId返回第一步
            $param['errMes'] = base64_encode('请输入学号');
            $this->redirect('step1',$param);
        }
        //获取从第一步传来的学号
        $studentId = trim($_GET['studentId']);
        //判断用户是否已经存在
        if(D('User')->hasUser($studentId)){
            //该用户存在 直接登录
            if(D('Passport')->loginLocalWithoutPassword($studentId,true)){

                //TODO 登录成功
                if(empty($param['returnUrl'])){
                    $this->redirect('Index/index');
                }else{
                    //$this->redirect(base64_decode($param['returnUrl']));
                    $returnUrl = base64_decode($param['returnUrl']);
                    redirect($returnUrl,3,"登录成功！即将为您跳转到<a href='{$returnUrl}'>{$returnUrl}</a>");
                }

            }else{
                //E('登录失败!');
                $this->error('登录失败!');
            }

        }
        //到这里说明是注册

        $this->assign('studentId',$studentId);
        $this->setHeader('完善个人信息');
        //分配给Widget
        $departments = D('Classes')->getAllDepartment();

        foreach($departments as &$v){
            $v['url'] = U('SetClass/classInfowidget',array('pid'=>$v['id'],'type'=>'department'));
        }

        $header['title'] = '完善个人信息';
        $header['backUrl'] = U('Login/step1',$param);

        $this->assign('header',$header);

        $this->assign('department',array($departments));
        $this->display();
    }

    /**
     * 处理注册
     */
    public function doRegister(){
        //注册或登录完成后返回的url
        $param = array();
        empty($_GET['returnUrl']) || $param = array('returnUrl'=>$_GET['returnUrl']);

        //获取用户信息
        $user['student_id'] = I('post.studentId',0);
        $user['name'] = I('post.name','','htmlspecialchars');
        $user['classes_id'] = I('post.classID',0);
        $user['tel_num'] = I('post.tel',0);
        if($user['student_id']== 0 || $user['name']=='' || $user['classes_id']==0 || $user['tel_num']==0){
            $this->error("请将信息填写完整！");
        }
        //开始注册
        D('User')->addUser($user);
        //注册成功之后登录
        if(D('Passport')->loginLocalWithoutPassword($user['student_id'],true)){
            //TODO 注册成功跳转到注册成功页面
            echo '这里需要跳转到注册成功页面';
        }else{
            $this->error('注册失败!');
        }
    }
    /**
     * 退出登录
     */
    public function logout(){
        D('Passport')->logout();
        echo '退出成功!';
    }
}