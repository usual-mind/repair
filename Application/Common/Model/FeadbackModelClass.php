<?php
/**
 * 意见反馈模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/20
 * Time: 13:59
 */

namespace Common\Model;


use Think\Model;

class FeadbackModel extends Model
{
    protected $tableName = 'feadback';

    /**
     * 添加意见反馈
     * @param $type 反馈类型
     * @param $content 反馈内容
     * @param $mid 反馈者的id
     * @return $feedbackId 插入的意见反馈id
     */
    public function addFeedBack($type , $content ,$mid = ''){
        if(!intval($type)) E('请选择反馈类型');
        if(empty($content)) E('请输入反馈内容');
        intval($mid) || $mid = $GLOBALS['e8']['mid'];
        if(!intval($mid)) E('请先登录后在添加意见反馈!');

        $data['type']       =   intval($type);
        $data['content']    =   html2Text($content);
        $data['ctime']      =   time();
        $data['uid']        =   $mid;
        if(!$feedbackId = $this->add($data)) E('意见反馈插入失败!');
        //通知e8用户组成员
        $config['name'] = D('User')->getLinkNameByUid($mid);
        $config['feedback_url'] = U($feedbackId);
        //TODO 这里的e8用户组id写死了，需要改进
        D('Notify')->sendSysMessage(2 , 'feedback_audit' , $config);

        return $feedbackId;
    }
}