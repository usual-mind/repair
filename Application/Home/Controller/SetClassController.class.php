<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class SetClassController extends BaseController{
    public function index(){
        $pid = empty($_GET['pid'])?0:$_GET['pid'];

        $ClassesModel = D('Classes');
        //��ȡ����ѧԺ
        $department = $ClassesModel->getChildByPid($pid);
        //�������м���һ��URL
        foreach($department as &$v){
            $v['url'] = U('SetClass/classInfowidget?pid='.$v['id']);
        }
        $this->assign('classInfoWidgetparam',array($department));
        $this->display('common/selectclass');
    }

    /**
     * widget
     */
    public function classInfowidget(){
        $pid = empty($_GET['pid'])?0:$_GET['pid'];

        $ClassesModel = D('Classes');
        //��ȡ����ѧԺ
        $department = $ClassesModel->getChildByPid($pid);
        //�������м���һ��URL
        foreach($department as &$v){
            $v['url'] = U('SetClass/classInfowidget?pid='.$v['id']);
        }
        W('SetClass/classesInfo',array($department));
    }
}