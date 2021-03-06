<?php
/**
 * 评论模型
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
    /**添加一条评论
     * @param $data = array(
     *      record_id=>, //如果是一条评价则有该值
     *      to_replay_id=>,//如果是回复评论 则有该值
     *      to_uid=>,//如果是回复评论或者是评价 则有该值
     *      content=>,//评论内容
     * );
     * @return 返回添加的评论id
     */
    public function addComment($data){
        if(!$GLOBALS['e8']['mid']) E('请先登录!');
        $data['uid'] = $GLOBALS['e8']['mid'];
        $data['ctime'] = time();
        if(empty($data['content'])) E('请输入评论内容!');
        $data['content'] = html2Text($data['content']);//安全处理

        if(!empty($data['record_id'])){
            //这是一条评价
            //通知维修者收到评价
            $config['name'] = D('User')->getLinkNameByUid($data['uid']);
            //TODO 维修记录链接，这里需要降低耦合
            $config['detailurl'] = U('Repair/index',array('record_id'=>$data['record_id']));
            D('Notify')->sendNotify($data['to_uid'],'received_evaluation',$config);
        }else{
            //这是对e8的留言
            if(!empty($data['to_replay_id'])){
                //这是回复评论
                //通知被回复者收到回复消息
                //D('Notify')->sendNotify($data['to_uid'],'',);
            }
        }

        return $this->add($data);
    }

    public function getCommentById(){
        //TODO
    }
}