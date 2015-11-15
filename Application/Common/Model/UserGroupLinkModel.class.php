<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/11
 * Time: 8:08
 */

namespace Common\Model;


use Think\Model;

class UserGroupLinkModel extends Model
{
    //缓存对象
    private $cacheObj = null;
    public function _initialize(){
        //初始化缓存对象
        $this->cacheObj = S(array('type'=>'file','prefix'=>'think','length'=>100));
    }
    /**
     * 获取用户的用户组ID
     * @param array $uids 用户UID数组
     * @return array 用户的用户组ID数组 索引是用户的uid
     */
    public function getUserGroupByUids($uids) {

        $uids = !is_array($uids) ? explode(',', $uids) : $uids;
        $uids = array_unique(array_filter($uids));//删除数组中为空的值和重复的值
        if(!$uids) return false;
        $return = array();
        foreach ($uids as $uid){
            $return[$uid] = $this->cacheObj->get ( 'user_group_' . $uid);
            if($return[$uid]==false){
                $map['uid'] = $uid;
                //目前一个用户只能加入一个用户组 所以调用find()
                $list = $this->where($map)->find();
                $return[$uid] = $list['group_id'];
                $this->cacheObj->set ( 'user_group_' . $uid, $return[$uid]);
            }
        }

        return $return;
    }

    /**清除用户的用户组信息缓存
     * @param $uids
     * @return bool
     */
    public function clearCache($uids){
        $uids = !is_array($uids) ? explode(',', $uids) : $uids;
        $uids = array_unique(array_filter($uids));//删除数组中为空的值和重复的值
        if(!$uids) return false;
        foreach ($uids as $uid){
            $this->cacheObj->rm('user_group_' . $uid);
        }
        return true;
    }
    /**
     * 获取用户所在用户组详细信息
     * @param array $uids 用户UID数组
     * @return array 用户的用户组详细信息数组 索引是用户的id
     */
    public function getUserGroupData($uids){
        is_array($uids) || $uids = explode(',', $uids);
        $uids = array_unique(array_filter($uids));//删除数组中为空的值和重复的值
        if(!$uids) return false;
        $userGids = $this->getUserGroupByUids($uids);

        //把所有用户组信息查询出来
        $ugresult = D('UserGroup')->getUserGroupByGids($userGids);
        //根据用户的用户组将用户组信息于用户的id拼装起来
        $userGroupData = array();
        foreach($userGids as $k=>&$v){
            $userGroupData[$k] = $ugresult[$v];
        }

        return $userGroupData;
    }
    /**
     * 转移用户的用户组
     * @param array $uids 用户UID
     * @param string $user_group_id 用户组ID
     * @return boolean 是否转移成功
     */
    public function domoveUserGroup($uids, $user_group_id) {
        // 验证数据
        if(empty($uids) && empty($user_group_id)) {
            E('用户组或用户不能为空');
            return false;
        }
        is_array($uids) || $uids = explode(',', $uids);
        $uids = array_unique(array_filter($uids));

        if(!$uids || !$user_group_id) {
            return false;
        }
        $map['uid'] = array('IN', $uids);
        $this->where($map)->delete();
        foreach($uids as $v) {
            $save = array();
            $save['uid'] = $v;
            $save['group_id'] = $user_group_id;
            $this->add($save);
        }
        //清除缓存
        model('User')->cleanCache($uids);

        return true;
    }

}