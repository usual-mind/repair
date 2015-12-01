<?php
/**
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/25
 * Time: 20:19
 */

namespace Common\Model;

class PermissionModel
{
    static protected $permission = array();// 本次请求所有的用户所具有的权限列表 $uid 是键值
    private $option = array();

    public function load($load){
        list($app,$module) = explode('_',$load,2);
        $this->option['app'] = $app;
        $this->option['module'] = $module;
        return $this;
    }
    /**
     * 加载该用户的所有权限
     * @param $uid
     */
    public function loadRule($uid){

        if(empty(self::$permission[$uid])){
            //获取用户权限
            //获取用户组id
            $groupId = D('UserGroupLink')->getUserGroupByUids($uid);
            $groupId = $groupId[$uid];
            self::$permission[$uid] = $this->getGroupPermission($groupId);
        }

        return self::$permission[$uid];
    }
    public function check($action,$uid=NULL){
        //验证是否load了
        if(empty($this->option['app']) || empty($this->option['module'])) {
            return false;
        }
        if(is_null($uid)){
            //如果没有传入uid默认为当前登录的用户
            $uid = $GLOBALS['e8']['mid'];
        }
        $uid = intval($uid);
        $permission = $this->loadRule($uid);

        return isset($permission[$this->option['app']][$this->option['module']][$action]);
    }
    /**
     * 获取用户组权限
     * @param $gidrr
     */
    public function getGroupPermission($gid){
        //TODO 缓存处理
        return D('SystemConfig')->get('permission:'.$gid);
    }
    /**
     * 设置指定用户组的权限信息
     * @param string $key 用户组ID
     * @param array $data 相关权限信息
     * @return void
     */
    public function setGroupPermission($key, $data) {
        D('SystemConfig')->set('permission:'.$key, $data);
        //TODO 更新缓存
        /*model('Cache')->set('perm_'.$key, $data);
        $userIds = D('user_group_link')->where('user_group_id='.$key)->field('uid')->findAll();
        foreach($userIds as $v){
            model('Cache')->rm('perm_user_'.$v['uid']);
        }*/
    }
}