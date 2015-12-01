<?php
namespace Home\Widget;

use Think\Controller;
class UserRepairRecordListWidget extends Controller
{
    public function recordList(){
        if($GLOBALS['e8']['user']['user_group']['id'] == 2){
            //e8成员

        }else if($GLOBALS['e8']['user']['user_group']['id'] == 1){
            //普通用户
            $records = D('RepairRecord')->getUserRepairRecords($GLOBALS['e8']['mid']);
            $this->assign('records',$records);
        }

        $this->assign('userGroupId',$GLOBALS['e8']['user']['user_group']['id']);

        $content = $this->fetch ('Widget/user_repair_record_list');

        return $content;
    }
}