<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class RegisterController extends BaseController{
    private $images = array();
    public function index(){
        $this->setTitle("登记维修记录");
        $this->setHeader("登记维修记录");

        $this->display();
    }

    /**
     * 处理图片上传
     */
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
        $imagePath = __ROOT__.'/data/uploads/'.$info['pic']['savepath'].$info['pic']['savename'];//原图的路径和文件名
        $absoluteImagePath =ROOT_PATH.'data/uploads/'.$info['pic']['savepath'].$info['pic']['savename'];//盘符\+目录的形式
        //生成缩略图
        $imageConfig = C('thumbnail');//获取生成缩略图的配置
        $filePath = pathinfo($absoluteImagePath,PATHINFO_DIRNAME).'/';//获取原图的路径
        $pahtInfo = pathinfo($imagePath);//获得原图的路径信息
        $fileName = $pahtInfo['filename'];//原图的文件名
        $filePostfix = $pahtInfo['extension'];//原图的后缀名
        //分别生成大图 中图 小图 的缩略图
        $smImagePath = $filePath.$fileName.$imageConfig['smThumbnail']['suffix'].'.'.$filePostfix;
        $mdImagePath = $filePath.$fileName.$imageConfig['mdThumbnail']['suffix'].'.'.$filePostfix;
        $lgImagePath = $filePath.$fileName.$imageConfig['lgThumbnail']['suffix'].'.'.$filePostfix;

        $image = new \Think\Image(\Think\Image::IMAGE_GD,$absoluteImagePath); // GD库 打开图片
        $image->thumb($imageConfig['smThumbnail']['width'], $imageConfig['smThumbnail']['width'])->save($smImagePath);
        $image = new \Think\Image(\Think\Image::IMAGE_GD,$absoluteImagePath); // GD库 打开图片
        $image->thumb($imageConfig['mdThumbnail']['width'], $imageConfig['mdThumbnail']['width'])->save($mdImagePath);
        $image = new \Think\Image(\Think\Image::IMAGE_GD,$absoluteImagePath); // GD库 打开图片
        $image->thumb($imageConfig['lgThumbnail']['width'], $imageConfig['lgThumbnail']['width'])->save($lgImagePath);

        //将图片路径放入session
        $this->images[] = array('originalImagePath'=>$imagePath,'smImagePath'=>$smImagePath,'mdImagePath'=>$mdImagePath,'lgImagePath'=>$lgImagePath);
        session('images',$this->images);
        p($this->images);
        die('<script>parent.callbackImageDisplay("'.$imagePath.'")</script>');
    }
}