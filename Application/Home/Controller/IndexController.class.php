<?php
namespace Home\Controller;

use Common\Controller\BaseController;

use Think\Controller;

class IndexController extends  BaseController{
    public function index()
    {
        // TODO $this->theme('default'); 模版切换

        //p(D('User')->getUserInfo(1));
        //unset($_SESSION['images']);
        //p(D('SystemConfig')->get('Permission:1'));
        //$config = array('core'=>array('normal'=>array('feed_view'=>1)));
        //D('SystemConfig')->set('Permission:1',$config,false);
            $data = array(
                'core'=>array(
                    'normal'=>array(
                        'repair_record_view'=>1,
                        'send_message'=>1,
                        'comment_del'=>1,
                        'repair_record_del'=>1,
                        'add_repair_record'=>1,
                        'repair_record_comment'=>1,
                        'repair'=>1,
                        'revoke_repair'=>1
                    ),
                'admin'=>array(
                    'repair_record_del'=>1,
                    'comment_del'=>1,
                    'message_del'=>1,
                    'admin_login'=>1,
                    'cancel_repair'=>1,
                    'revoke_repair'=>1
                )
                ),
            );

            //D('SystemConfig')->set('permission:2',$data,false);
            //p(D('Permission')->load('core:normal')->check('repair_record_view'));
            //p( unserialize( 'a:4:{s:4:"core";a:2:{s:6:"normal";a:11:{s:9:"feed_view";s:1:"1";s:9:"read_data";s:1:"1";s:11:"invite_user";s:1:"1";s:12:"send_message";s:1:"1";s:11:"search_info";s:1:"1";s:11:"comment_del";s:1:"1";s:8:"feed_del";s:1:"1";s:9:"feed_post";s:1:"1";s:12:"feed_comment";s:1:"1";s:11:"feed_report";s:1:"1";s:10:"feed_share";s:1:"1";}s:5:"admin";a:5:{s:8:"feed_del";s:1:"1";s:11:"comment_del";s:1:"1";s:11:"message_del";s:1:"1";s:11:"admin_login";s:1:"1";s:14:"feed_recommend";s:1:"1";}}s:5:"weiba";a:2:{s:6:"normal";a:6:{s:10:"weiba_post";s:1:"1";s:11:"weiba_reply";s:1:"1";s:9:"weiba_del";s:1:"1";s:15:"weiba_del_reply";s:1:"1";s:10:"weiba_edit";s:1:"1";s:18:"weiba_apply_manage";s:1:"1";}s:5:"admin";a:6:{s:9:"weiba_del";s:1:"1";s:10:"weiba_edit";s:1:"1";s:16:"weiba_global_top";s:1:"1";s:12:"weiba_marrow";s:1:"1";s:9:"weiba_top";s:1:"1";s:15:"weiba_recommend";s:1:"1";}}s:7:"channel";a:1:{s:5:"admin";a:1:{s:17:"channel_recommend";s:1:"1";}}s:5:"vtask";a:1:{s:5:"admin";a:1:{s:15:"vtask_recommend";s:1:"1";}}}' ) );
            //p( unserialize( 'a:2:{s:4:"core";a:2:{s:6:"normal";a:11:{s:9:"feed_view";s:1:"1";s:9:"read_data";s:1:"1";s:11:"invite_user";s:1:"1";s:12:"send_message";s:1:"1";s:11:"search_info";s:1:"1";s:11:"comment_del";s:1:"1";s:8:"feed_del";s:1:"1";s:9:"feed_post";s:1:"1";s:12:"feed_comment";s:1:"1";s:11:"feed_report";s:1:"1";s:10:"feed_share";s:1:"1";}s:5:"admin";a:1:{s:8:"feed_del";s:1:"1";}}s:5:"weiba";a:1:{s:6:"normal";a:6:{s:10:"weiba_post";s:1:"1";s:11:"weiba_reply";s:1:"1";s:9:"weiba_del";s:1:"1";s:15:"weiba_del_reply";s:1:"1";s:10:"weiba_edit";s:1:"1";s:18:"weiba_apply_manage";s:1:"1";}}}' ) );

        //$this->assign('data' , array('repair_record_id'=>29,'repair_record_uid'=>2));
        //$this->display();
        //p(D('SystemConfig')->get('permission:2'));

        if(!D('RepairService')->revokeRepair(29)){
            p(D('RepairService')->getError());
        }else{
            echo 'OK';
        }

    }
}