<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class SetComputerController extends BaseController{

    public function ComputerInfoWidget(){
        $pid = empty($_GET['pid'])?0:intval($_GET['pid']);
        //$type = empty($_GET['type'])?'department':trim($_GET['type']);

    }
}