<?php
/**评论模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/16
 * Time: 22:37
 */

namespace Common\Model;


use Think\Model;

class CommentModel extends Model
{
    protected $tableName = 'comment';
    /**
    id
    record_id
    to_replay_id
    to_uid
    content
     */
    public function addComment($data){
        if(!$GLOBALS['e8']['mid']) E('请先登录!');
        $data['uid'] = $GLOBALS['e8']['mid'];
        $data['ctime'] = time();
        if(empty($data['content'])) E('请输入评论内容!');
        $data['content'] = html2Text($data['content']);
        if(!$data['record_id']){
            //这是一条评价
            //通知维修者收到评价
            $config['name'] = D('User')->getLinkName($data['uid']);
            //TODO 维修记录链接，这里需要降低耦合
            $config['record_link'] = U();
            D('Notify')->sendNotify($data['to_uid'],'received_evaluation',$config);
        }else{
            //这是对e8的留言
            if(!empty($data['to_replay_id'])){
                //这是回复评论
                D('Notify')->sendNotify();
            }
        }
        $this->add();
    }
}