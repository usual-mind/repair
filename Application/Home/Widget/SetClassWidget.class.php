<?php
namespace Home\Widget;
use Think\Controller;
class SetClassWidget extends Controller{
    //班级信息 Widget
    public function classesInfo($department){
        $this->assign('classData', $department);
        $this->display('Widget/selectclass');
    }

}