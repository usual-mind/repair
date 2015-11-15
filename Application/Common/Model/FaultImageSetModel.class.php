<?php
/**
 * Created by PhpStorm.
 * User: TAOYU
 * Date: 2015/11/14
 * Time: 12:16
 */

namespace Common\Model;


use Think\Model;

class FaultImageSetModel extends Model
{
    protected $tableName = 'fault_image_set';

    /**通过图片集id获取所有的图片
     * @param $setIds
     * @return array|bool
     */
    public function getImagesBySetId($setIds){
        is_array($setIds) || $setIds = explode(',' , $setIds);
        $setIds = array_unique(array_filter($setIds));//删除数组中为空的值和重复的值

        //TODO 缓存处理
        $map['id'] = array('IN',$setIds);
        if(!($set = $this->where($map)->limit(count($setIds))->select())){
            E('没有找到id为'.$setIds.'的图片集');
            return false;
        }

        $return = array();
        foreach($set as $v){

            $images = M('fault_images')->field('url')
                ->where('image_set_id='.$v['id'])->limit($v['count'])->select();

            $return[$v['id']] = array('count'=>$v['count'],
                        'ctime'=>$v['ctime'],
                        'path'=>$images);
        }
        return $return;
    }

    /**
     * 插入一组图片
     * 返回图片集id
     *
     * @parme array $images   array('图片的url','图片的url',....);
     */
    public function addImages($images){

        //创建图片集
        $data['count'] = count($images);
        $data['ctime'] = time();
        if(!($setId = $this->add($data))) E('创建图片集失败！'.$this->getError());
        //将所有的图片插入到fault_images表中
        $image['image_set_id'] = $setId;
        foreach($images as $url){
            $image['url'] = $url;
            M('fault_images')->add($image);
        }
        return $setId;
    }
}