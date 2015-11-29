<?php
namespace Home\Controller;

use Common\Controller\BaseController;

use Think\Controller;

class IndexController extends  BaseController{
    public function index()
    {
        //p(D('User')->getUserInfo(1));
        //unset($_SESSION['images']);
        //p(D('SystemConfig')->get('Permission:1'));
        //$config = array('core'=>array('normal'=>array('feed_view'=>1)));
        //D('SystemConfig')->set('Permission:1',$config,false);
            /**
             *
             *
             * [core] => Array
            (
            [normal] => Array
            (
            [feed_view] => 1
            [read_data] => 1
            [invite_user] => 1
            [send_message] => 1
            [search_info] => 1
            [comment_del] => 1
            [feed_del] => 1
            [feed_post] => 1
            [feed_comment] => 1
            [feed_report] => 1
            [feed_share] => 1
            )

            [admin] => Array
            (
            [feed_del] => 1
            [comment_del] => 1
            [message_del] => 1
            [admin_login] => 1
            [feed_recommend] => 1
            )

            )
             */
        D('Permission')->loadRule(300);
    }
}