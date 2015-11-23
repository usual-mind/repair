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
        $this->assign('department',array($departments));
        //分配个人信息
        $this->assign('userInfo',$GLOBALS['e8']['user']);
        $this->display('Widget/persondata');
    }
    public function doModifyData(){

    }
}