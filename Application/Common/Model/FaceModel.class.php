<?php
/**
 * 用户头像模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/21
 * Time: 23:56
 */

namespace Common\Model;


class FaceModel
{
    private $_uid;
    private $savePath = '';//保存头像的根目录
    private $emptyUrl = '';//默认的头像url
    private $error='';
    /**
     * 初始化模型，加载相应的文件
     * @param integer $uid 用户UID
     * @return object 头像模型对象
     */
    public function init($uid) {
        $this->_uid = intval($uid);
        $this->savePath = '/face'.$this->convertUidToPath($this->_uid);
        return $this;
    }
    /**
     * 将用户的UID转换为三级路径
     * @param integer $uid 用户UID
     * @return string 用户路径
     */
    public function convertUidToPath($uid) {
        // 静态缓存
        $sc = static_cache('face_uidpath_' . $uid);
        if (!empty($sc)) {
            return $sc;
        }
        $md5 = md5($uid);
        $sc = '/' . substr($md5, 0, 2) . '/' . substr($md5, 2, 2) . '/' . substr($md5, 4, 2).'/';
        static_cache('face_uidpath_' . $uid, $sc);
        return $sc;
    }
    /**
     * 判断用户是否上传头像
     */
    public function hasFace(){
        $originalFileName = USER_UPLOAD_PATH.$this->savePath.'original.jpg';
        if(file_exists($originalFileName)){
            return $originalFileName;
        }else{
            return false;
        }
        /*
        if(file_exists($originalFileName)){
            //$filemtime = filemtime($originalFileName);
            //$avatar = getImageUrl($originalFileName, 50, 50) . '?v' . $filemtime;
        }*/
    }
    /**
     * 获取头像
     * @param int $size 头像大小
     */
    public function getFace(){
        $empty_url = __ROOT__.'/Public/'.MODULE_NAME.'/img/noface';
        $face_url = array(
            'face_original' => $empty_url . '/big.jpg',
            'face_big' => $empty_url . '/big.jpg',
            'face_middle' => $empty_url . '/middle.jpg',
            'face_small' => $empty_url . '/small.jpg',
            'face_tiny' => $empty_url . '/tiny.jpg'
        );

        $original_file_name = $this->hasFace();

        if ($original_file_name) {
            $filemtime = filemtime( $original_file_name );
            $face_url['face_original'] = $this->getFaceUrl($original_file_name);
            $face_url['face_big'] = $this->getFaceUrl($original_file_name, 200, 200) . '?v' . $filemtime;
            $face_url['face_middle'] = $this->getFaceUrl($original_file_name, 100, 100) . '?v' . $filemtime;
            $face_url['face_small'] = $this->getFaceUrl($original_file_name, 50, 50) . '?v' . $filemtime;
            $face_url['face_tiny'] = $this->getFaceUrl($original_file_name, 30, 30) . '?v' . $filemtime;
        }
        return $face_url;
    }
    /**
     * 上传头像到临时文件夹
     * @param $file file控件的name
     * @return 上传成功返回上传的图片url 失败返回false
     */
    public function uploadToTemp($file = 'file'){
        return D('UploadPic')->init()->uploadToTemp($file);
    }
    public function saveFace(){
        //$configs,$rootPath,$subPath='',$ext='',$fileName='uniqid'
        $configs['200_200']=array('width'=>200,'height'=>200);
        $configs['100_100']=array('width'=>100,'height'=>100);
        $configs['50_50']=array('width'=>50,'height'=>50);
        $configs['30_30']=array('width'=>30,'height'=>30);
        $pic = D('UploadPic')->saveAllTempPic($configs,USER_UPLOAD_PATH,$this->savePath,'.jpg','original');
        return $pic;
    }
    /**
     * 根据width和height获取头像图片名
     * @param $original_file_name
     * @param $width
     * @param $height
     * @return string
     */
    public static function getFacePic($original_file_name , $width, $height){
        //这里不用rtrim去除扩展名的原因是 rtrim这个函数有个bug 如 echo rtrim('aajj.jpg','.jpg');
        $pathInfo = pathinfo($original_file_name);

        return "{$pathInfo['dirname']}/{$pathInfo['filename']}_{$width}_{$height}.{$pathInfo['extension']}";
    }
    /**
     * 获取图片的url
     * @param $original_file_name
     * @param string $width
     * @param string $height
     * @return string
     */
    public function getFaceUrl($original_file_name , $width='', $height=''){

        $original_file_url = path2url($original_file_name);

        if(!$width){
            return $original_file_url;
        }
        return $this->getFacePic($original_file_url,$width,$height);
    }
    public function getError(){
        return $this->error;
    }
}