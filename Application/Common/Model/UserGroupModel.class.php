<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/11
 * Time: 8:08
 */

namespace Common\Model;


use Think\Model;

class UserGroupModel extends Model
{
    //缓存对象
    private $cacheObj = null;
    public function _initialize(){
        //初始化缓存对象
        $this->cacheObj = S(array('type'=>'file','prefix'=>'think','length'=>100));
    }
    /**
     * 返回用户组信息
     * @param integer $gid 用户组ID，默认为空字符串 - 显示全部用户组信息
     * @return array 用户组信息
     */
    public function getUserGroup($gid = '') {
        //先判断缓存中是否有所有的用户组信息
        if(($data = $this->cacheObj->get('AllUserGroup')) == false) {
            $list = $this->select();
            foreach($list as $v) {
                $data[$v['id']] = $v;
            }
            $this->cacheObj->set('AllUserGroup', $data);
        }
        if(empty($gid)){
            // 返回全部用户组
            return $data;
        } else {
            // 返回指定的用户组
            if(is_array($gid)){
                $r = array();
                foreach($gid as $v){
                    $r[$v] = $data[$v];
                }
                return $r;
            } else {
                return $data[$gid];
            }
        }
    }

    /**
     * 清除用户组信息缓存
     * @param $gids
     */
    public function clearCache($gids){
        //如果没有传入空 清除AllUserGroup缓存
        if(empty($gids)) {
            $cacheKey = 'AllUserGroup';
        }elseif(is_array($gids)){
            //缓存键
            $cacheKey = 'UserGroupByGid_'.implode(',',$gids);
        }else {
            $cacheKey = 'UserGroupByGid_' . $gids;
            $gids = explode(',', $gids);
        }
        $this->cacheObj->rm($cacheKey);
    }
    /**返回用户组信息 并不是查出所有的用户组
     * @param $gids
     */
    public function getUserGroupByGids($gids){
        //如果没有传入空 直接调用$this->getUserGroup()获取所有用户组信息
        if(empty($gids)) return $this->getUserGroup();
        if(is_array($gids)){
            //缓存键
            $cacheKey = 'UserGroupByGid_'.implode(',',$gids);
        }else {
            $cacheKey = 'UserGroupByGid_' . $gids;
            $gids = explode(',', $gids);
        }
        $gids = array_unique(array_filter($gids));
        //从缓存中获取用户组信息
       if(!$data = $this->cacheObj->get($cacheKey)){
           //没有缓存 从数据库中获取
           $map['id'] = array('IN',$gids);
           $data = $this->where($map)->select();
           if(!$data){$this->error='没有该用户组信息';return false;}
           $return = array();
           foreach($data as $v){
               $return[$v['id']] = $v;
           }
           $this->cacheObj->set($cacheKey,$return);
       }else{
           return $data;
       }
        return $return;
    }
}