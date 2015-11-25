<?php
namespace Home\Widget;
use Think\Controller;
class PersonDataWidget extends Controller{
    public function personData(){
        //分配给选择班级Widget
        $departments = D('Classes')->getAllDepartment();
        foreach($departments as &$v){
            $v['url'] = U('SetClass/classInfowidget',array('pid'=>$v['id'],'type'=>'department'));
        }
        //分配提交按钮Url
        $this->assign('personalDataModify',U('PersonalCenter/doModifyData'));
        //分配伪异步上传头像处理的URL
        $this->assign('uploadFaceUrl',U('PersonalCenter/uploadFace'));
        $this->assign('department',array($departments));
        //分配个人信息
        $this->assign('userInfo',$GLOBALS['e8']['user']);
        $this->display('Widget/persondata');
    }


}