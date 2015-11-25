<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class PersonalCenterController extends BaseController{
    public function index(){
        $this->setTitle('个人中心');
        $this->setHeader('个人中心');
        $header['title'] = '个人中心';
        $header['backUrl'] = '';
        $this->assign('header',$header);
//        if(!D('Passport')->isLogged()){
//            $this->redirect(U('Login/index'));
//        }
        $userInfo = D('User')->getUserInfo($GLOBALS['e8']['mid']);
        $records = D('RepairRecord')->getUserRepairRecords($GLOBALS['e8']['mid']);
        $this->assign('userInfo',$userInfo);
        $this->assign('records',$records);
        $this->display();
    }
    /**
     * 伪异步上传头像
     */
    public function uploadFace(){
        if(!$faceUrl = D('Face')->uploadToTemp('face')){
            die('<script>parent.callBackUploadFace("","上传头像失败")</script>');
        }else{
            die(die('<script>parent.callBackUploadFace("'.$faceUrl.'","")</script>'));
        }
    }
    /**
     * 用户修改个人资料
     */
    public function doModifyData(){
        $studentId = empty($_POST['student_id'])?0:$_POST['student_id'];
        $name =  empty($_POST['name'])?'':$_POST['name'];
        $classId = empty($_POST['class_id'])?0:$_POST['class_id'];
        $telNum = empty($_POST['tel_num'])?0:$_POST['tel_num'];

        if($studentId == 0 || $name == '' || $classId == 0 || $telNum == 0){
            $this->error("你输入的信息有误，请重新输入！");
        }
        //上传头像
        D('Face')->saveFace();

        $user = $user = array(
                    'student_id' =>$studentId,
                    'name'       =>$name,
                    'classes_id' =>$classId,
                    'tel_num'    =>$telNum);
        D('User')->saveUser($GLOBALS['e8']['mid'],$user);
        //修改成功 跳转到个人中心
        $this->redirect('index');
    }
}