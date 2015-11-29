<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/29
 * Time: 19:25
 */

namespace Home\Widget;


use Think\Controller;

class RepairRecordMangeWidget extends Controller
{

    public function showButton($data){

        if (empty ( $data ['repair_record_id'] )
            || empty ( $data ['repair_record_uid'] )) {
            return '';
        }
        // 获取维修记录ID
        $repairRecordId = intval ( $data ['repair_record_id'] );
        // 获取提交该维修记录的用户的UID
        $repairRecordUid = intval ( $data ['repair_record_uid'] );

        //是否维修
        // 管理员删除维修记录权限
        $adminRepairDel = checkPermission ( 'core_admin', 'repair_record_del' );
        //删除自己维修记录的权限
        if(checkPermission ( 'core_normal', 'repair_record_del' ) && $repairRecordUid==$GLOBALS['e8']['mid']){
            $selfRepairDel = true;
        }else{
            $selfRepairDel = false;
        }
        //维修权限
        $repair = checkPermission ( 'core_normal', 'repair' );
        if($repair){
            //获取维修记录详情
            $repairRecord = D('RepairRecord')->getRepairRecord($repairRecordId);
            //当前的维修状态 0-未维修 1-维修中 2-维修结束
            $repairStatus = intval($repairRecord['status']);
            //如果该维修记录的状态为维修中 且该条维修记录的维修人是本人
            if(0 == $repairStatus){
                //显示维修按钮
                $showRepair = true;
            }else if(1 == $repairStatus && $repairRecord['repairmem_id'] == $GLOBALS['e8']['mid']){
                //显示取消维修按钮
                $showRepair = false;
            }else {
                //不显示维修相关按钮
                $repair = false;
            }
        }
        //判断需不需显示管理按钮
        $checkShowBtn = array ();
        $checkShowBtn [] = $adminRepairDel;
        $checkShowBtn [] = $selfRepairDel;
        $checkShowBtn [] = $repair;
        if (! in_array ( true, $checkShowBtn )) {
            return '';
        }

        /*// 管理参数组合
        $args = array ();
        $args ['repair_record_id'] = $repairRecordId;
        $args ['repair_record_uid'] = $repairRecordUid;
        $args ['repair_record_del'] = ($adminRepairDel || $selfRepairDel);
        $args ['repair'] = $repair;
        isset($showRepair) && $args ['show_repair_btn'] = $showRepair;
        $this->assign('args',$args);*/
        $this->assign('repair_record_id',$repairRecordId);
        $this->assign('repair_record_uid',$repairRecordUid);
        $this->assign('repair_record_del',($adminRepairDel || $selfRepairDel));
        $this->assign('repair',$repair);
        isset($showRepair) && $this->assign('show_repair_btn',$showRepair);
        //$manageClass = isset ( $data ['manage_class'] ) ? $data ['manage_class'] : 'right hover dp-cs';
        //$var ['manageClass'] = $manageClass;

        $content = $this->fetch ('Widget/repair_record_mange');
        return $content;
    }
}