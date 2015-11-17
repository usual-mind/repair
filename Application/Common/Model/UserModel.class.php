<?php
/**用户模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/8
 * Time: 18:58
 */

namespace Common\Model;


use Think\Model;

class UserModel extends Model
{

    protected $tableName = 'user';
    //微信号正则
    private $_weixin_reg = '/^[A-Za-z][\d_\-a-zA-Z]{5,19}$/i';
    //手机号码正则
    private $_tel_reg = '/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/';
    //学号正则
    private $_student_id_reg = '/\d{10}/';
    //缓存对象
    private $cacheObj = null;
    public function _initialize(){
        //初始化缓存对象
        $this->cacheObj = S(array('type'=>'file','prefix'=>'think','length'=>100));
        //TODO 读取站点配置 如默认用户组等等
    }
    /**
     * 检查用户是否存在
     * @param $user 用户唯一标识 uid|weixin|student_id
     * @param bool|false $isUid 用户表示是否在uid
     * @return bool
     */
    public function hasUser($user,$isUid = false){
        //首先该用户没有被逻辑删除
        $map['is_del'] = 0;
        if($isUid){
            $map['id'] = $user;
        }elseif(preg_match($this->_student_id_reg,$user)){
            //唯一标识符是学号
            $map['student_id'] = $user;
        }elseif(preg_match($this->_weixin_reg,$user)){
            //唯一标识符是微信号
            $map['weixin'] = $user;
        }else{
            //不存在的唯一标识符格式直接返回false 表示用户不存在
            return false;
        }
        if($uid = $this->where($map)->limit('1')->getField('id')){
            return $uid['id'];
        }else{
            return false;
        }
    }

    /**获取指定用户的相关信息 如果有缓存会直接从缓存中获取信息
     * @param $uid
     * @return array|bool
     */
    public function getUserInfo($uid){
        $uid = intval($uid);
        // # 判断uid是否存在
        if (0 >= $uid or !$uid) {
            E('UID参数值不合法');
            // # 判断是否有用户缓存
        } elseif (!($user = static_cache('user_info_' . $uid)) and !($user = $this->cacheObj->get('ui_' . $uid))) {
            /* # 获取用户信息缓存失败 */
            $this->error = '获取用户缓存失败';
            $user        = $this->_getUserInfo(array('id' => $uid,'is_del'=>0));
        }
        return $user;
    }
    /**
     * 直接从数据苦中获取指定用户的相关信息并缓存
     *
     * @param array $map
     *        	查询条件
     * @return array 指定用户的相关信息
     */
    private function _getUserInfo(array $map, $field = '*')
    {
        $user = $this->where($map)->field($field)->find();
        if (! $user) {
            E('用户查询失败！');
            return false;
        } else {
            unset ( $user ['password'] , $user ['is_del']);
            $uid = $user ['id'];
            //获取用户的用户组信息
            $userGroup = D ( 'UserGroupLink' )->getUserGroupData ( $uid );
            $user ['user_group'] = $userGroup [$uid];
            //TODO 获取该用户的所有电脑信息
            $computerList = D('ComputerLink')->getUserComputerList($uid);
            $user['computer'] = $computerList[$uid];

            $this->cacheObj->set ( 'ui_' . $uid, $user, 600 );
            static_cache ( 'user_info_' . $uid, $user );
            return $user;
        }
    }
    public function cleanCache($uids){
        //TODO 清除用户缓存
    }
    /**
     * 根据UID批量获取多个用户的相关信息
     *
     * @param array $uids
     *        	用户UID数组
     * @return array 指定用户的相关信息
     */
    public function getUserInfoByUids($uids)
    {
        is_array($uids) || $uids = explode(',', $uids);
        $uids = array_unique(array_filter($uids));//清除数组中重复的值和空值
        $list = array();
        foreach ($uids as $uid) {
            $list[$uid] = $this->getUserInfo($uid);
        }
        return $list;
    }

    /**
     * 添加用户
     *
     * @param array $user
     *        	新用户的相关信息|新用户对象
     * $user = array(
     *      'student_id' =>,
     *      'weixin'     =>, //可有可无
     *      'name'       =>,
     *      'classes_id' =>,
     *      'tel_num'    =>,
     * );
     * @param $group_id 用户组id
     * @return true or throw new Exception
     */
    public function addUser(array $user,$group_id=1)
    {
        if (empty($user['student_id'])) E('请输入学号');
        //判断学号是否被注册了
        if ($this->hasUser($user['student_id'])) E('该学号已存在');
        //判断微信是否被注册了
        if ($user['weixin'] && $this->hasUser($user['weixin'])) E('该微信号已存在');

        //获取班级字符串
        if (empty($user['classes_id'])) E('请填写班级信息');
        $user['classes_name'] = D('Classes')->getClassById($user['classes_id']);

        $user['ctime'] = time();             // # 注册时间
        $user['reg_ip'] = ip2long(get_client_ip());    // # 用户客户端注册IP
        if (($uid = $this->add($user))) {
            //  把用户添加到用户组
            D('UserGroupLink')->domoveUserGroup($uid, $group_id);
            return true;
        }
        E('添加用户失败!');
    }
    /**
     * 通过uid获取带连接的用户姓名
     * @param $uid
     * @return string name
     */
    public function getLinkNameByUid($uid){
        $user = $this->getUserInfo($uid);
        return '<a href="#'.$uid.'" class="user-name">'.$user['name'].'</a>';
    }

}

