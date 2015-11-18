<?php
namespace Home\Widget;
use Think\Controller;
class selectorWidget extends Controller{

    public function selector(){
        $this->display('Widget/selector');
    }
    //班级信息 Widget
    public function selectList($department){
        $this->assign('classData', $department);
        $this->display('Widget/selectList');
    }

}