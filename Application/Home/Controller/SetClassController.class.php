<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class SetClassController extends BaseController{
    public function index(){
    }

    /**TODO 加注释
     * ajax返回
     * 班级信息widget
     */
    public function classInfowidget(){
        $pid = empty($_GET['pid'])?0:intval($_GET['pid']);
        $type = empty($_GET['type'])?'department':trim($_GET['type']);
        $back = empty($_GET['back'])?false:true;
        switch($type){
            case 'department':
                $type = 'grade';
                if($back){
                    $type = 'department';
                }
                $title = '选择年级';
                $backParam = array('pid'=>0,'type'=>'department','back'=>'back');
                $department = D('Classes')->getGradeByPid($pid);
                break;
            case 'grade':
                $type = 'major';
                $title = '选择专业';
                $backParam = array('pid'=>D('Classes')->getParentId($pid),'type'=>'grade','back'=>'back');
                if($back){
                    $type = 'grade';
                    $backParam = array('pid'=>D('Classes')->getParentId($pid),'type'=>'department','back'=>'back');
                }
                $department = D('Classes')->getMajorByPid($pid);
                break;
            case 'major':
                $type = 'class';
                $backParam = array('pid'=>D('Classes')->getParentId($pid),'type'=>'major','back'=>'back');
                if($back){
                    $type = 'major';
                    $backParam = array('pid'=>D('Classes')->getParentId($pid),'type'=>'grade','back'=>'back');
                }
                $title = '选择班级';

                $department = D('Classes')->getClassByPid($pid);
                break;
            case 'class':
                $department = D('Classes')->getAllDepartment();
                $type = 'department';
                $title = '完善信息';
                $backParam = array('pid'=>D('Classes')->getParentId($pid),'type'=>'class','back'=>'back');
                echo '<script>callBackSelectEnd('.$pid.')</script>';
                break;
        }
        //在数组中加入一个URL和级，班
        foreach($department as &$v){
            $v['url'] = U('SetClass/classInfowidget',array('pid'=>$v['id'],'type'=>$type));
        }
        p($backParam);
        echo $type;
        $header['title'] = $title;
        $header['backUrl'] = U('SetClass/classInfowidget',$backParam);

        $this->assign('header',$header);
        W('selector/selectList',array($department));
    }
}