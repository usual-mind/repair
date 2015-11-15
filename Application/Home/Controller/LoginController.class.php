<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class LoginController extends BaseController{
    private $images = array();
    public function Login(){
        $this->setTitle("学号");
        //$this->setHeader("");

        $this->display();
    }
}