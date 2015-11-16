<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class SetClassController extends BaseController{
    public function index(){
        //��ȡ����ѧԺ
        $departments =D('Classes')->getAllDepartment();
        //�������м���һ��URL
        foreach($departments as &$v){
            $v['url'] = U('SetClass/classInfowidget',array('pid'=>$v['id'],'type'=>'department'));
        }
        $this->assign('classInfoWidgetparam',array($departments));
        $this->display('common/selectclass');
    }

    /**
     * ajax����
     * �༶��Ϣwidget
     */
    public function classInfowidget(){
        $pid = empty($_GET['pid'])?0:intval($_GET['pid']);
        $type = empty($_GET['type'])?'department':trim($_GET['type']);
        switch($type){
            case:
                break;
        }
        //��ȡ����ѧԺ
        $department = D('Classes')->getChildByPid($pid);
        //�������м���һ��URL
        foreach($department as &$v){
            $v['url'] = U('SetClass/classInfowidget?pid='.$v['id']);
        }
        W('SetClass/classesInfo',array($department));
    }
}