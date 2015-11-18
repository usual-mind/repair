<?php
namespace Home\Widget;
use Think\Controller;
class SetClassWidget extends Controller{

    public function setClass(){
        $this->display('Widget/setClass');
    }
    //班级信息 Widget
    public function classesList($department){
        $this->assign('classData', $department);
        $this->display('Widget/classesList');
    }

}