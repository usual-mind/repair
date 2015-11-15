<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class SetClassController extends BaseController{
    public function index(){
        $this->display('Common/selectclass');
    }
}