<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class SetClassController extends BaseController{
    public function index(){
        //获取所有学院
        $departments = D('Classes')->getAllDepartment();
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
            case 'department':
                $type = 'grade';
                $department = D('Classes')->getGradeByPid($pid);
                break;
            case 'grade':
                $type = 'major';
                $department = D('Classes')->getMajorByPid($pid);
                break;
            case 'major':
                $type = 'class';
                $department = D('Classes')->getClassByPid($pid);
                break;
            case 'class':
                $department = D('Classes')->getAllDepartment();
                $type = 'department';
                echo '<script>callBackSelectEnd('.$pid.')</script>';
                break;
        }
        //在数组中加入一个URL和级，班
        foreach($department as &$v){
            if($type == 'grade'){
                $v['title'] .= '级';
            } else if($type == 'class'){
                $v['title'] .= '班';
            }
            $v['url'] = U('SetClass/classInfowidget',array('pid'=>$v['id'],'type'=>$type));
        }
        W('SetClass/classesInfo',array($department));
    }
}