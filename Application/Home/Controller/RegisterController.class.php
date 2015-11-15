<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class RegisterController extends BaseController{
    private $images = array();
    public function register(){
        $this->setTitle("登记维修记录");
        $this->setHeader("登记维修记录");

        $this->display();
    }
    public function upLoadPic(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        //$rootPath = ROOT_PATH.'data/uploads/'.date('Y/m/d/');
        //if(!is_dir($rootPath))mkdir($rootPath,0777,true);
        $upload->rootPath  =     ROOT_PATH.'data/uploads/'; // 设置附件上传根目录
        $upload->subName   =    '';//设置附件的存储路径
        $upload->savePath  =     date('Y/m/d/');

        // 上传文件
        $info   =   $upload->upload();

        //print_r($info);die;
        if(!$info) {// 上传错误提示错误信息
            die('<script>parent.callbackImageDisplay("","'.$upload->getError().'")</script>');
        }
        $imagePath = __ROOT__.'/data/uploads/'.$info['pic']['savepath'].$info['pic']['savename'];
        $this->images[] = $imagePath;
        session('images',$this->images);
        die('<script>parent.callbackImageDisplay("'.$imagePath.'")</script>');
    }
}