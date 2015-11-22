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
    /**
     * 初始化模型，加载相应的文件
     * @param integer $uid 用户UID
     * @return object 头像模型对象
     */
    public function init($uid) {
        $this->_uid = intval($uid);
        $this->savePath = DATA_PATH.'/upload'.$this->convertUidToPath($this->_uid);
        return $this;
    }
    /**
     * 将用户的UID转换为三级路径
     * @param integer $uid 用户UID
     * @return string 用户路径
     */
    public function convertUidToPath($uid) {
        // 静态缓存
        $sc = static_cache('avatar_uidpath_' . $uid);
        if (!empty($sc)) {
            return $sc;
        }
        $md5 = md5($uid);
        var_dump($md5);
        $sc = '/' . substr($md5, 0, 2) . '/' . substr($md5, 2, 2) . '/' . substr($md5, 4, 2);
        static_cache('avatar_uidpath_' . $uid, $sc);
        return $sc;
    }
    /**
     * 判断用户是否上传头像
     */
    public function hasFace(){
        $originalFileName = $this->savePath.'/original.jpg';
        return file_exists($originalFileName);
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
        $empty_url = __ROOT__.'/Public/Home/noface';
        $avatar_url = array(
            'face_original' => $empty_url . '/big.jpg',
            'face_big' => $empty_url . '/big.jpg',
            'face_middle' => $empty_url . '/middle.jpg',
            'face_small' => $empty_url . '/small.jpg',
            'face_tiny' => $empty_url . '/tiny.jpg'
        );

        $original_file_name = $this->savePath.'/original.jpg';
        if (file_exists($original_file_name)) {
            $filemtime = @filemtime(UPLOAD_PATH . $original_file_name);
            $avatar_url['avatar_original'] = getImageUrl($original_file_name);
            $avatar_url['avatar_big'] = getImageUrl($original_file_name, 200, 200) . '?v' . $filemtime;
            $avatar_url['avatar_middle'] = getImageUrl($original_file_name, 100, 100) . '?v' . $filemtime;
            $avatar_url['avatar_small'] = getImageUrl($original_file_name, 50, 50) . '?v' . $filemtime;
            $avatar_url['avatar_tiny'] = getImageUrl($original_file_name, 30, 30) . '?v' . $filemtime;
        }

        return $avatar_url;
    }
    /**
     * 上传头像
     */
    public function upload(){

    }
}