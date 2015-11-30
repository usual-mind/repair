<?php
namespace Home\Controller;

use Common\Controller\BaseController;

class MessageController extends BaseController{
    public function index(){
        $this->setTitle("我的消息");
        $this->setHeader(" 我的消息");

        $uid = $GLOBALS['e8']['mid'];
        $this->assign('sysMessCount',D('Notify')->getUnreadCount($uid,1));//获取未读的系统消息的数量
        $this->assign('aboutMeMessCount',D('Notify')->getUnreadCount($uid,2));//获取未读的与我相关消息的数量
        $this->assign('setReadUrl',U('setRead'));
        $this->assign("mess",D('Notify')->getMessageList($uid));
        $this->display();
    }
    public function setRead(){
        $type = $_GET['type'];
        D('Notify')->setRead($type);
    }
}