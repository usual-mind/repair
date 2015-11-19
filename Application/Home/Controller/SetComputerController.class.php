<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class SetComputerController extends BaseController{

    public function ComputerInfoWidget(){
        $pid = empty($_GET['pid'])?0:intval($_GET['pid']);
        $type = empty($_GET['type'])?'computerBrand':trim($_GET['type']);
        $back = empty($_GET['back'])?false:true;
        switch($type){
            case 'computerBrand':
                $computer = D('Computer')->getChildByPid($pid);
                $title = '选择型号';
                $type = 'computerModel';
                if($back){
                    $type = 'computerBrand';
                }
                $backParam = array('pid'=>0,'type'=>'computerBrand','back'=>'back');
                break;
            case 'computerModel':
                $computer = D('Computer')->getChildByPid(0);
                $type = 'computerBrand';
                $title = '选择品牌';
                echo '<script>callBackSelectEnd('.$pid.')</script>';
                break;

        }
        foreach($computer as &$v){
            $v['url'] = U('SetComputer/ComputerInfoWidget',array('pid'=>$v['id'],'type'=>$type));
        }
        unset($v);
        $header['title'] = $title;
        $header['backUrl'] = U('SetComputer/ComputerInfoWidget',$backParam);

        $this->assign('header',$header);

        W('selector/selectList',array($computer));
    }
}