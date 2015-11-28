<?php
/**
 * 电脑故障图片模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/14
 * Time: 12:16
 */

namespace Common\Model;


use Think\Model;

class FaultImageSetModel extends Model
{
    protected $tableName = 'fault_image_set';
    protected $defaultImage = '';

    public function _initialize(){
        $this->defaultImage = __ROOT__.'/Public/'.MODULE_NAME.'/img/default.jpg';
    }
    /**通过图片集id获取所有的图片
     * @param $setId
     * @return array|bool
     */
    public function getImagesBySetId($setId){
        $setId = intval($setId);
        if(!$setId) E('获取图片失败');
        //TODO 缓存处理
        $map['id'] = $setId;
        if(!($set = $this->where($map)->find())) {
            E('没有找到id为' . $setId . '的图片集');
            return false;
        }
        $images = M('fault_images')->field('url_original,url_sm,url_mid,url_lg')
            ->where('image_set_id='.$set['id'])->limit($set['count'])->select();
        $return = array();
        foreach($images as $image){
            $return[] = $image;
        }
        return $return;
    }
    /**
     * 插入一组图片
     * 返回图片集id
     *
     * @parme array $images   array(0=>array(
     *                            'url_original'=>,
     *                            'url_sm'=>,
     *                            'url_sm'=>,
     *                            'url_lg'=>
     *                          ),...);
     */
    public function addImages(array $images){
        $images = array_unique(array_filter($images));
        //创建图片集
        $data['count'] = count($images);
        if($data['count']<=0) return null;//如果传入的数组为空直接返回null
        $data['ctime'] = time();
        if(!($setId = $this->add($data))) E('创建图片集失败！'.$this->getError());
        //将所有的图片插入到fault_images表中
        $image['image_set_id'] = $setId;
        foreach($images as $allUrls){
            $image['url_original']  =   empty($allUrls['url_original'])?'':$allUrls['url_original'];
            $image['url_sm']        =   empty($allUrls['url_sm'])?'':$allUrls['url_sm'];
            $image['url_mid']       =   empty($allUrls['url_mid'])?'':$allUrls['url_mid'];
            $image['url_lg']        =   empty($allUrls['url_lg'])?'':$allUrls['url_lg'];
            M('fault_images')->add($image);
        }
        return $setId;
    }
    public function getDefaultImage(){
        return $this->defaultImage;
    }
}