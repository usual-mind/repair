<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class RegisterController extends BaseController{
    public function index(){
        $this->setTitle("登记维修记录");
        $this->setHeader("登记维修记录");
        //提交的地址
        $this->assign('submitUrl',U('Register/doRegister'));
        //电脑型号的Widget所需的信息
        $computerBrand = D('Computer')->getChildByPid(0);
        foreach($computerBrand as &$v){
            $v['url'] = U('SetComputer/ComputerInfoWidget',array('pid'=>$v['id'],'type'=>'computerBrand'));
        }
        /*
        $header['title'] = '登记维修记录';
        $header['backUrl'] = U('Login/index');
        $this->assign('header',$header);
        $this->assign('department',array($computerBrand));
        */

        //获取用户信息
        $userInfo = D('User')->getUserInfo($GLOBALS['e8']['mid']);
        $this->assign('className',$userInfo['classes_name']);
        $this->assign('telNumber',$userInfo['tel_num']);
        $this->assign('computers',$userInfo['computer']);
        //分配搜索的url
        $this->assign('searchTipUrl',U('searchModel'));
        //分配添加电脑的url
        $this->assign('addComputer',U('addComputer'));
        $this->display();
    }
    /**
     * 返回匹配的电脑型号
     */
    public function searchModel(){
        $pid = I('get.pid',0);
        if($pid == 0){
            $this->error('没有该电脑品牌');
        }
        $keyword = I('get.keyword','');
        $res = D('Computer')->searchModel($keyword,$pid,5);
        echo json_encode($res);
    }
    /**
     *  给用户添加电脑型号
     */
    public function addComputer(){
        //返回插入的电脑型号
        try{
            $computerName = D('ComputerLink')->addComputerToUser($GLOBALS['e8']['mid'],I('get.model',''),I('get.brandId',0));
            $this->ajaxReturn(array('computerName'=>$computerName));
        }catch (\Exception $e){
            $this->ajaxReturn(array(),0,$e->getMessage());
        }
    }
    /**
     * 处理登记信息
     */
    public function doRegister(){
        $problemDesc = safetyHtml($_POST['problemDesc']);

        if(empty($_POST['computerModelId'])){
            $this->error('请选择电脑型号');
        }
        $computerModelId = $_POST['computerModelId'];
        if(!empty($_SESSION['images'])){
            $imageSetId = D('FaultImageSet')->addImages($_SESSION['images']);
<<<<<<< HEAD
            $data = array('problem_desc' =>$problemDesc,'computer_id'=>$computerModelId,'image_set_id' =>$imageSetId);
        }else{
            $data = array('problem_desc' =>$problemDesc,'computer_id'=>$computerModelId);
        }
        unset($_SESSION['images']);
=======
            $data['problem_desc']   =   $problemDesc;
            $data['computer_id']    =   $computerModelId;
            $data['image_set_id']   =   $imageSetId;
            unset($_SESSION['images']);
        }else{
            $data['problem_desc']   =   $problemDesc;
            $data['computer_id']    =   $computerModelId;
        }
>>>>>>> origin/master
        D('RepairRecord')->addRepairRecord($data);
        //登记成功
    }
    /**
     * 处理图片上传
     * 生成大，中，小三张缩略图
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
        $absoluteImagePath =ROOT_PATH.'data/uploads/'.$info['pic']['savepath'].$info['pic']['savename'];//服务器绝对路径的形式
        //生成缩略图
        $imageConfig = C('thumbnail');//获取生成缩略图的配置
        $filePath = pathinfo($absoluteImagePath,PATHINFO_DIRNAME).'/';//获取原图的路径
        $pahtInfo = pathinfo($imagePath);//获得原图的路径信息
        $fileDir = $pahtInfo['dirname'].'/';//原图的路径
        $fileName = $pahtInfo['filename'];//原图的文件名
        $filePostfix = $pahtInfo['extension'];//原图的后缀名
        //分别生成大图 中图 小图 的缩略图

        $smImagePath='';
        $mdImagePath='';
        $lgImagePath='';
        //首先讲返回的图片URl设置成原图
        $retImagePath = $imagePath;
        if(!empty($imageConfig['lgThumbnail'])){
            $lgImagePath = $fileDir.$fileName.$imageConfig['lgThumbnail']['suffix'].'.'.$filePostfix;
            $image = new \Think\Image(\Think\Image::IMAGE_GD,$absoluteImagePath); // GD库 打开图片
            $image->thumb($imageConfig['lgThumbnail']['width'], $imageConfig['lgThumbnail']['width'])->save($filePath.$fileName.$imageConfig['lgThumbnail']['suffix'].'.'.$filePostfix);
            //将返回的图片设置成$lgImagePath
            $retImagePath = $lgImagePath;
        }
        if(!empty($imageConfig['mdThumbnail'])){
            $mdImagePath = $fileDir.$fileName.$imageConfig['mdThumbnail']['suffix'].'.'.$filePostfix;
            $image = new \Think\Image(\Think\Image::IMAGE_GD,$absoluteImagePath); // GD库 打开图片
            $image->thumb($imageConfig['mdThumbnail']['width'], $imageConfig['mdThumbnail']['width'])->save($filePath.$fileName.$imageConfig['mdThumbnail']['suffix'].'.'.$filePostfix);
            //将返回的图片设置成$mdImagePath
            $retImagePath = $mdImagePath;
        }
        if(!empty($imageConfig['smThumbnail'])){
            $smImagePath = $fileDir.$fileName.$imageConfig['smThumbnail']['suffix'].'.'.$filePostfix;
            $image = new \Think\Image(\Think\Image::IMAGE_GD,$absoluteImagePath); // GD库 打开图片
            $image->thumb($imageConfig['smThumbnail']['width'], $imageConfig['smThumbnail']['width'])->save($filePath.$fileName.$imageConfig['smThumbnail']['suffix'].'.'.$filePostfix);
            //将返回的图片设置成$smImagePath
            $retImagePath = $smImagePath;
        }

        //将图片路径放入session
        $_SESSION['images'][] = array('url_original'=>$imagePath,'url_sm'=>$smImagePath,'url_mid'=>$mdImagePath,'url_lg'=>$lgImagePath);

        die('<script>parent.callbackImageDisplay("'.$retImagePath.'")</script>');
    }
}