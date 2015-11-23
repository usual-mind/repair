<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/22
 * Time: 12:46
 */

namespace Common\Model;

class UploadPicModel
{
    private $error = '';

    public function init(){
        unset($_SESSION['images']);
    }
    /**
     * 上传图片到临时目录并且保存到session
     * @param string $file 上传文件控件的name值
     * @return bool|string 返回上传的缩略图url
     */
    public function uploadToTemp($file = 'file'){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize    =       3145728 ;// 设置附件上传大小
        $upload->autoSub    =       false;
        $upload->exts       =       array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->replace    =       true;//如果存在同名文件直接覆盖
        $upload->rootPath   =       USER_UPLOAD_PATH.'/Temp_pic/';
        $upload->savePath   =       date('Y/m/d/');//保存路径
        $info = $upload->uploadOne($_FILES[$file]);
        if(!$info) {
            // 上传错误提示错误信息
            $this->error = $upload->getError();
            return false;
        }else{
            //上传成功
            $tempPic = $upload->rootPath.$info['savepath'].$info['savename'];
            // GD库 打开图片
            $image = new \Think\Image(\Think\Image::IMAGE_GD,$tempPic);
            //生成缩略图返回
            $tempThumbPic = $upload->rootPath.$info['savepath'].basename($info['savename'],'.'.$info['ext']).'_thumb.'.$info['ext'];
            $image->thumb(100,100)->save($tempThumbPic);
            //把上传的临时文件保存到session中
            $_SESSION['images'][$tempPic] = array('original'=>$tempPic,'thumb'=>$tempThumbPic);
            return path2url($tempThumbPic);
        }
    }

    /**
     * 保存session中的所有临时图片
     * @param $configs 保存文件的尺寸信息
     * $configs['lg'] = array('width'=>'auto','hieght'=>200);
     * $configs['mid'] = array('width'=>100,'hieght'=>100);
     * $configs['sm'] = array('width'=>50,'hieght'=>'auto');
     * @param $rootPath 保存图片的根目录
     * @param string $subPath 保存图片的子目录 默认为date('Y/m/d/')
     * @param string $ext     保存图片的扩展名 默认和临时文件一样
     * @param string $fileName 保存后的文件名 默认为uniqid() 如果留空则获取临时文件名
     * @return mixed
     */
    public function saveAllTempPic($configs,$rootPath,$subPath='',$ext='',$fileName='uniqid'){
        $tempFiles = $_SESSION['images'];
        if(empty($tempFiles)) reutrn;
        //子目录
        empty($subPath) && $subPath = date('Y/m/d/');
        $return = array();
        foreach($tempFiles as $tempFile){
            $return[] = $this->savePic($tempFile['original'],$configs,$rootPath,$subPath,$ext,$fileName);
            //保存图片后需要删除原来的图片
            $this->delTempPic($tempFile['original']);
        }
        return $return;
    }
    /**
     * 把上传的临时文件保存起来 //支持按比例缩放
     * @param $tempFile 临时文件
     * @param $configs 保存文件的尺寸信息
     * $configs['lg'] = array('width'=>'auto','hieght'=>200);
     * $configs['mid'] = array('width'=>100,'hieght'=>100);
     * $configs['sm'] = array('width'=>50,'hieght'=>'auto');
     * @param $rootPath 保存图片的根目录
     * @param string $subPath 保存图片的子目录 默认为date('Y/m/d/')
     * @param string $ext     保存图片的扩展名 默认和临时文件一样
     * @param string $fileName 保存后的文件名 默认为uniqid() 如果留空则获取临时文件名
     * @return mixed
     */
    public function savePic($tempFile,$configs,$rootPath,$subPath,$ext='',$fileName='uniqid'){

        //扩展名
        empty($ext) && $ext = '.'.pathinfo($tempFile,PATHINFO_EXTENSION);
        //图片名
        if(empty($fileName)){
            $fileName = basename($tempFile,$ext);
        }elseif(function_exists($fileName)){
            $fileName = $fileName();
        }
        //图片的保存路径
        $savePath = $rootPath.$subPath;

        if(!is_dir($savePath)){
            mkdir($savePath,0777,true);
        }
        $original = $savePath.$fileName.$ext;
        //把原图从temp文件夹中移动到$savePath中
        if(!rename($tempFile,$original)){
            return null;
        }

        //获取图片的尺寸
        list($width,$height) = getimagesize($original);
        //纵宽比
        $spectRatio = $height/$width;
        // GD库 打开图片
        $image = new \Think\Image();
        $image->open($original);
        foreach($configs as $k=>&$config){
            if(empty($config['width']) || 'auto' == $config['width']){
                //按照给定的高度等比例计算出宽度
                $config['width'] = $config['height']/$spectRatio;
            }else if(empty($config['height']) || 'auto' == $config['height']){
                //按照宽度等比例缩放
                $config['height'] = $config['width']*$spectRatio;
            }
            $config['path'] = $savePath.$fileName.'_'.$k.$ext;
            $config['url'] = path2url($config['path']);
            $image->thumb($config['width'],$config['height'],\Think\Image::IMAGE_THUMB_FIXED)
                ->save($config['path']);
        }
        //原图信息
        $configs['original'] = array('width'=>$width,'hieght'=>$height,'path'=>$original,'url'=>path2url($original));
        unset($config);
        return $configs;
    }

    /**
     * 删除临时图片
     * @param $tempPic
     */
    public function delTempPic($tempPic){
        if(file_exists($tempPic)){
            unlink($tempPic);
        }
        unlink($_SESSION['images'][$tempPic]['thumb']);//删除缩略图
        unset($_SESSION['images'][$tempPic]);
    }
    public function getError(){
        return $this->error;
    }
}