<?php
namespace Home\Widget;

use Think\Controller;
class UserRepairRecordListWidget extends Controller
{
    public function recordList(){
        if(2 == $GLOBALS['e8']['user']['user_group']['id']){

        }else if(1 == $GLOBALS['e8']['user']['user_group']['id']){
            //普通用户
            $records = D('RepairRecord')->getUserRepairRecords($GLOBALS['e8']['mid']);
            $this->assign('records',$records);
        }

        $this->assign('userGroupId',$GLOBALS['e8']['user']['user_group']['id']);
        $content = $this->fetch ('Widget/user_repair_record_list');
        return $content;
    }
    /**
     * 获取e8成员的维修记录
     */
    public function getRepairRecord($uid){
        if(2 == $uid){
            $records = D('RepairRecord')->getRepairRecordByRepairmemId($GLOBALS['e8']['mid']);
            $this->assign('records',$records);
            $data['content'] = $this->fetch ('Widget/user_repair_record_list');
            return array($data,1,'');
        }else{
            return array(null,0,'没有权限');
        }
    }
    /**
     * 获取当前维修任务
     */
    public function getNotRepairList($uid){
        if(2 == $uid){
            $records = D('RepairRecord')->getNotRepairList($GLOBALS['e8']['mid']);
            $this->assign('records',$records);
            $data['content'] = $this->fetch ('Widget/user_repair_record_list');
            return array($data,1,'');
        }else{
            return array(null,0,'没有权限');
        }
    }
}