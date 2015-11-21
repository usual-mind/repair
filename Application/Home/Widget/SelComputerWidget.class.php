<?php
namespace Home\Widget;
use Think\Controller;
class SelComputerWidget extends Controller{
    public function selComputer(){
        //获取所有电脑品牌
        $allBrand = D('Computer')->getChildByPid(0);
        $this->assign('allBrand',$allBrand);
        $this->display('Widget/selComputer');
    }
}