<?php

namespace Home\Widget;
use Think\Controller;

class PersonalCenterColumnBtnWidget extends Controller{
    public function columnBtn(){
        if(2 == $GLOBALS['e8']['user']['user_group']['id']){
            //e8成员
            $this->assign("notRepairListUrl",U('PersonalCenter/getNotRepairList'));//分配ajax的获取当前维修任务的URL
            $this->assign("myReapirListUrl",U('PersonalCenter/getRepairRecord'));//分配ajax的我的维修记录的URl
            return $this->fetch('Widget/personal_center_column_btn');
        }
    }
}