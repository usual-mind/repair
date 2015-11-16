<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class SetClassController extends BaseController{
    public function index(){
        //获取所有学院
        $departments =D('Classes')->getAllDepartment();
        //在数组中加入一个URL
        foreach($departments as &$v){
            $v['url'] = U('SetClass/classInfowidget',array('pid'=>$v['id'],'type'=>'department'));
        }
        $this->assign('classInfoWidgetparam',array($departments));
        $this->display('common/selectclass');
    }

    /**
     * ajax返回
     * 班级信息widget
     */
    public function classInfowidget(){
        $pid = empty($_GET['pid'])?0:intval($_GET['pid']);
        $type = empty($_GET['type'])?'department':trim($_GET['type']);
        switch($type){
            case:
                break;
        }
        //获取所有学院
        $department = D('Classes')->getChildByPid($pid);
        //在数组中加入一个URL
        foreach($department as &$v){
            $v['url'] = U('SetClass/classInfowidget?pid='.$v['id']);
        }
        W('SetClass/classesInfo',array($department));
    }
}