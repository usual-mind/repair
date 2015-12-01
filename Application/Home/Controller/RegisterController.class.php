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

        $header['title'] = '登记维修记录';
        $header['backUrl'] = $_SERVER['HTTP_REFERER'];
        $this->assign('header',$header);
        /*

        $this->assign('department',array($computerBrand));
        */

        //获取用户信息
        $userInfo = D('User')->getUserInfo($GLOBALS['e8']['mid']);
        $this->assign('className',$userInfo['classes_name']);
        $this->assign('telNumber',$userInfo['formate_tel_num']);
        $this->assign('computers',$userInfo['computer']);
        //分配搜索的url
        $this->assign('searchTipUrl',U('searchModel'));
        //分配添加电脑的url
        $this->assign('addComputer',U('addComputer'));
        //分配个人中心URL
        $this->assign('personalUrl',U('PersonalCenter/index'));
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
            $computerId = D('ComputerLink')->addComputerToUser($GLOBALS['e8']['mid'],I('get.model',''),I('get.brandId',0));
            $this->ajaxReturn(array('computerName'=>D('Computer')->getComputerNameById($computerId)));
        }catch (\Exception $e){
            $this->ajaxReturn(array(),0,$e->getMessage());
        }
    }
    /**
     * 处理登记信息
     */
    public function doRegister(){

        $problemDesc = html2Text($_POST['problemDesc']);
        if(empty($_POST['computerModelId'])){
            $this->error('请选择电脑型号');
        }
        $computerModelId = $_POST['computerModelId'];
        $imageConfig = C('THUMB');//获取生成缩略图的配置

        $configs['lg'] = array('width'=>$imageConfig['LG_THUMB']['WIDTH'],'height'=>$imageConfig['LG_THUMB']['HEIGHT']);
        $configs['mid'] = array('width'=>$imageConfig['MD_THUMB']['WIDTH'],'height'=>$imageConfig['MD_THUMB']['HEIGHT']);
        $configs['sm'] = array('width'=>$imageConfig['SM_THUMB']['WIDTH'],'height'=>$imageConfig['SM_THUMB']['HEIGHT']);
        $res = D('UploadPic')->saveAllTempPic($configs,USER_UPLOAD_PATH.'/pic/');
        $images = array();
        $i=0;
        foreach($res as $v) {
            $images[$i]['url_original'] = $v['original']['url'];
            $images[$i]['url_sm'] = $v['sm']['url'];
            $images[$i]['url_mid'] = $v['mid']['url'];
            $images[$i]['url_lg'] = $v['lg']['url'];
            $i++;
        }
        if(!empty($images)){
            $imageSetId = D('FaultImageSet')->addImages($images);
            $data['problem_desc']   =   $problemDesc;
            $data['computer_id']    =   $computerModelId;
            $data['image_set_id']   =   $imageSetId;
            unset($images);
        }else{
            $data['problem_desc']   =   $problemDesc;
            $data['computer_id']    =   $computerModelId;
        }

        D('RepairRecord')->addRepairRecord($data);
        //登记成功
    }
    /**
     * 处理图片上传
     * 生成大，中，小三张缩略图
     */
    public function upLoadPic(){
        /*$upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     5242880 ;// 设置附件上传大小 5*1024*1024
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        //$rootPath = ROOT_PATH.'data/uploads/'.date('Y/m/d/');
        //if(!is_dir($rootPath))mkdir($rootPath,0777,true);
        $upload->rootPath  =     USER_UPLOAD_PATH.'/'; // 设置附件上传根目录
        $upload->subName   =    '';//设置附件的存储路径
        $upload->savePath  =     date('Y/m/d/');
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            die('<script>parent.callbackImageDisplay("","'.$upload->getError().'")</script>');
        }
        $ImagePath =USER_UPLOAD_PATH.'/'.$info['pic']['savepath'].$info['pic']['savename'];//服务器绝对路径的形式
        $imageUrl = path2url($ImagePath);//原图的Url
        //生成缩略图
        $imageConfig = C('thumbnail');//获取生成缩略图的配置
        $filePath = pathinfo($ImagePath,PATHINFO_DIRNAME).'/';//获取原图的路径
        $pahtInfo = pathinfo($ImagePath);//获得原图的路径信息
        $fileName = $pahtInfo['filename'];//原图的文件名
        $filePostfix = $pahtInfo['extension'];//原图的后缀名
        //分别生成大图 中图 小图 的缩略图

        $lgImageUrl = '';
        $mdImageUrl = '';
        $smImageUrl = '';
        //首先讲返回的图片URl设置成原图
        $retImageUrl = $imageUrl;
        if(!empty($imageConfig['lgThumbnail'])){
            $lgImagePath = $filePath.$fileName.$imageConfig['lgThumbnail']['suffix'].'.'.$filePostfix;
            $lgImageUrl = path2url($lgImagePath);
            $image = new \Think\Image(\Think\Image::IMAGE_GD,$ImagePath); // GD库 打开图片
            $image->thumb($imageConfig['lgThumbnail']['width'], $imageConfig['lgThumbnail']['width'])->save($lgImagePath);
            //将返回的图片设置成$lgImagePath
            $retImageUrl = $lgImageUrl;
        }
        if(!empty($imageConfig['mdThumbnail'])){
            $mdImagePath = $filePath.$fileName.$imageConfig['mdThumbnail']['suffix'].'.'.$filePostfix;
            $mdImageUrl = path2url($mdImagePath);
            $image = new \Think\Image(\Think\Image::IMAGE_GD,$ImagePath); // GD库 打开图片
            $image->thumb($imageConfig['mdThumbnail']['width'], $imageConfig['mdThumbnail']['width'])->save($mdImagePath);
            //将返回的图片设置成$mdImagePath
            $retImageUrl = $mdImageUrl;
        }
        if(!empty($imageConfig['smThumbnail'])){
            $smImagePath = $filePath.$fileName.$imageConfig['smThumbnail']['suffix'].'.'.$filePostfix;
            $smImageUrl = path2url($smImagePath);
            $image = new \Think\Image(\Think\Image::IMAGE_GD,$ImagePath); // GD库 打开图片
            $image->thumb($imageConfig['smThumbnail']['width'], $imageConfig['smThumbnail']['width'])->save($smImagePath);
            //将返回的图片设置成$smImagePath
            $retImageUrl = $smImageUrl;
        }

        //将图片路径放入session
        $_SESSION['images'][] = array('url_original'=>$imageUrl,'url_sm'=>$smImageUrl,'url_mid'=>$mdImageUrl,'url_lg'=>$lgImageUrl);

        die('<script>parent.callbackImageDisplay("'.$retImageUrl.'")</script>');*/
        if(!$tempThumbPic = D('UploadPic')->uploadToTemp('pic')){
            die('<script>parent.callbackImageDisplay("","图片上传失败")</script>');
        }
        //返回缩略图
        die('<script>parent.callbackImageDisplay("'.$tempThumbPic.'")</script>');

    }

}