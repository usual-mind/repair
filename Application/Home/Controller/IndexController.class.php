<?php
namespace Home\Controller;

use Common\Controller\BaseController;

use Think\Controller;

class IndexController extends  BaseController{
    public function index(){

        //$this->show('<form action="'.U('Index/up').'" method="post" enctype="multipart/form-data"><input name="file" type="file" /><input type="submit"></form>');
        //D('Face')->init(1)->buildFacePic();
        //unset($_SESSION['images']);die;
        p($_SESSION);die;

    }
    public function up(){
        $configs = array(
            'lg'=>array(
                'width' => 'auto',
                'height'=> '500'
            ),
            'sm'=>array(
                'width' => '500',
                'height'=> ''
            ),
        );
        p( D('UploadPic')->savePic('./data/uploads/face/c4/ca/42/original.jpg',$configs,$rootPath=USER_UPLOAD_PATH.'/face/','c3/41/12/'));
    }
}