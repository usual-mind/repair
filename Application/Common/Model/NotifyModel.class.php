<?php
/**通知模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/11
 * Time: 8:31
 */

namespace Common\Model;


use Think\Model;

class NotifyModel extends Model
{
    protected $tableName = 'notify_node';
    //缓存对象
    private $cacheObj = null;
    private $_config = array();
    public function _initialize(){
        //初始化缓存对象
        $this->cacheObj = S(array('type'=>'file','prefix'=>'think','length'=>100));

        //$site = empty($GLOBALS['ts']['site']) ? model('Xdata')->get('admin_Config:site') : $GLOBALS['ts']['site'];
        $site = $GLOBALS['e8']['site'];
        $this->_config['site'] = $site['site_name'];
        $this->_config['site_url'] = $site['site_url'];
    }
    public function getMessageList($uid){
        $condition['uid'] = intval($uid);
        $condition['is_read'] = 0;
    }
    /**
     * 更改指定用户的消息从未读为已读
     * @param integer $uid 用户ID
     * @return mix 更改失败返回false，更改成功返回消息ID
     */
    public function setRead($uid, $node = null) {
        $map['uid'] = $uid;
        $map['is_read'] = 0;
        !empty($node) && $map['node'] = $node;
        $data['is_read'] = 1;
        return M('notify_message')->where($map)->save($data);
    }
    /**
     * 获取指定用户未读消息的总数
     * @param integer $uid 用户ID
     * @return integer 指定用户未读消息的总数
     */
    public function getUnreadCount($uid){
        $condition['uid'] = intval($uid);
        $condition['is_read'] = 0;
        return $this->where($condition)->count();
    }
    /**
     * 获取节点列表
     * @return array 节点列表数据
     */
    public function getNodeList() {
        // 缓存处理
        if($list = static_cache('notify_node')) {
            return $list;
        }
        if(($list = $this->cacheObj->get('notify_node')) == false) {
            $data = $this->select();
            //将node字段变成数组的索引
            $list = array();
            foreach($data as $v){
                $list[$v['node']] = $v;
                unset($list[$v['node']]['node']);
            }
            $this->cacheObj->set('notify_node', $list);
        }
        static_cache('notify_node', $list);
        return $list;
    }
    /**
     * 获取指定节点信息
     * @param string $node 节点Key值
     * @return array 指定节点信息
     */
    public function getNode($node){
        $list = $this->getNodeList();
        return $list[$node];
    }
    /**
     * 发送消息
     * @param array $toUid 接收消息的用户ID数组
     * @param string $node 节点Key值
     * @param array $config 配置数据
     * @param intval $from 消息来源用户的UID
     * @return void
     */
    public function sendNotify($toUid, $node, $config, $from) {
        empty($config) && $config = array();
        $config = array_merge($this->_config,$config);

        $nodeInfo = $this->getNode($node);
        if(!$nodeInfo) {
            return false;
        }
        !is_array($toUid) && $toUid = explode(',', $toUid);
        $userInfo = D('User')->getUserInfoByUids($toUid);
        $data['node'] = $node;
        $data['title'] = lang($nodeInfo['title_key'],$config);
        $data['body'] = lang($nodeInfo['content_key'],$config);
        $data['ctime'] = time();
        if($from) $data['from_uid'] = $from;
        foreach($userInfo as $v) {
            $data['uid'] = $v['id'];
            //发送站内消息
            1==$nodeInfo['send_message'] && $this->sendMessage($data);
            $data['email'] = $v['email'];
            //发送邮件
            1==$nodeInfo['send_email'] && $this->sendEmail($data);
            //发送手机推送
            1==$nodeInfo['send_push'] && $this->sendPush($data);
        }
    }
    /**
     * 清除消息节点缓存
     * @return void
     */
    public function cleanCache() {
        $this->cacheObj->rm('notify_node');
        //更新语言包
        //model('Lang')->initSiteLang();
    }

    /**
     * 发送系统消息，给指定用户
     * @param array $data 发送系统消息相关数据
     * @return mix 发送失败返回false，发送成功返回新的消息ID
     */
    public function sendMessage($data) {
        if(empty($data['uid'])) {
            return false;
        }
        $s['uid'] = intval($data['uid']);
        $s['node'] = $data['node'];
        $s['is_read'] = 0;//刚插入的消息没有被阅读
        $s['title'] = html2Text($data['title']);
        $s['body'] = safetyHtml($data['body']);
        $s['ctime'] = $data['ctime'];
        $s['from_uid'] = intval($data['from_uid']);
        return M('notify_message')->add($s);
    }

    /**
     * 删除通知
     * @param integer $id 通知ID
     * @return mix 删除失败返回false，删除成功返回删除的通知ID
     */
    public function deleteNotify($id) {
        $condition['uid'] = $GLOBALS['e8']['mid'];		// 仅仅只能删除登录用户自己的通知
        $condition['id'] = intval($id);
        return M('notify_message')->where($condition)->delete();
    }
    /**
     * 发送系统消息，给用户组或全站用户
     * @param array $user_group 用户组ID
     * @param string $content 发送信息内容
     * @return boolean 是否发送成功
     */
    public function sendSysMessage($user_group , $content , $node='sys_notify') {
        $ctime = time();
        $user_group = intval($user_group);
        if(!empty($user_group)) {
            // 判断组中是否存在用户
            $m['group_id'] = $user_group;
            $c = M('UserGroupLink')->where($m)->count();
            if($c > 0) {
                // 针对用户组  执行insert_select 语句  这里有一个问题 并没有判断用户是否被删除可
                $sql = "INSERT INTO {$this->tablePrefix}notify_message (`uid`,`node`,`title`,`body`,`ctime`,`is_read`,`from_uid`)
    				SELECT uid,'{$node}','','{$content}','{$ctime}','0','0'
    				FROM {$this->tablePrefix}user_group_link WHERE group_id = {$user_group} ";
            } else {
                return true;
            }
        } else {
            // 全站用户  执行insert_select 语句
            $sql = "INSERT INTO {$this->tablePrefix}notify_message (`uid`,`node`,`title`,`body`,`ctime`,`is_read`,`from_uid`)
    				SELECT uid,'{$node}','','{$content}','{$ctime}','0','0'
    				FROM {$this->tablePrefix}user WHERE is_del=0 ";
        }
        p($sql);die;
        M('')->query($sql);
        return true;
    }
}