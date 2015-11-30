<?php
/**
 * 评论赞模型
 * Created by PhpStorm.
 * User: Taoyu
 * Date: 2015/11/20
 * Time: 8:26
 */

namespace Common\Model;


use Think\Model;

class CommentDiggModel extends Model
{
    protected $tableName = 'comment_digg';

    /**
     * 添加赞
     * @param $commentId
     * @param $mid
     * @return bool|mixed
     */
    public function addDigg($commentId , $mid){
        $data['comment_id'] = intval($commentId);
        if(empty($mid)) $mid = $GLOBALS['e8']['mid'];
        if(!$mid) E('未登录不能赞!');
        //该评论是否已经被$mid赞过了
        $isDigged = $this->where ( $data )->getField ( 'id' );
        if($isDigged){
            $this->error = '你已经赞过了!';
            return false;
        }
        $data['ctime'] = time();
        $diggId = $this->add($data);
        if($diggId){
            $commendModel = M('Comment');
            //获取评论，(需要uid)
            $comment = $commendModel->getCommentById($data['comment_id']);
            //评论表的赞总数自增1
            $commendModel->where('id='.$data['comment_id'])->setInc('digg_count');
            //TODO 清除评论缓存

            //通知被赞的用户
            $config['name']         =   D('User')->getLinkNameByUid($mid);
            $config['content']      =   $comment['content'];
            $config['sourceurl']    =   U($comment['record_id']);
            D('Notify')->sendNotify($comment['uid'],'digg',$config,$mid);
            $this->setDiggCache();
        }
        return $diggId;
    }

    /**取消赞
     * @param $commentId
     * @param $mids
     */
    public function delDigg($commentId , $mid){
        $data['comment_id'] = intval($commentId);
        if(empty($mid)) $mid = $GLOBALS['e8']['mid'];
        if(!$mid) E('未登录不能赞!');
        //该评论是否已经被$mid赞过了
        $isDigged = $this->where ( $data )->getField ( 'id' );
        if(!$isDigged){
            $this->error = '取消赞失败，你可能已经取消过赞了!';
            return false;
        }
        $res = $this->where($data)->delete();
        if($res){
            //评论表的赞总数自减1
            D('Comment')->where('id='.$data['comment_id'])->setDec('digg_count');
            $this->setDiggCache();
        }
        return $res;
    }
    public function setDiggCache(){
        //TODO
    }
}