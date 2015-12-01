<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/1
 * Time: 13:58
 */

namespace Common\Model;


class RepairServiceModel
{
    private $error = '';
    /**
     * 维修电脑
     * @param $repairRecordId
     * @param $uid 维修者id；默认为当前登录的用户
     */
    public function repairComputer($repairRecordId,$uid=NULL){
        $repairRecordId = intval($repairRecordId);
        if(is_null($uid)) $uid = $GLOBALS['e8']['mid'];
        $uid = intval($uid);

        //检查用户是否有维修电脑权限
        $repairPermission = checkPermission('core_normal','repair',$uid);
        if(!$repairPermission){

            $this->error = 'Sorry! 您并没有权限维修电脑!';
            return false;
        }
        $repairRecord = D('RepairRecord')->getRepairRecord($repairRecordId);
        //如果该维修记录的状态不为0-未维修 就返回false;
        if($repairRecord['status'] != 0){
            $this->error = '这台电脑已经在维修中啦!';
            return false;
        }
        $data['repairmem_id'] = $uid;
        $data['status'] = 1;//维修状态设置为维修中

        $res = M('RepairRecord')->where('id = '.$repairRecordId)->save($data);
        if(!$res) E('维修电脑失败!');
        //添加维修动态
        $stateInfo = array(
                'repair_record_id'=>$repairRecordId,
                'state_node'=>'repairing',
                'state_title'=>'您的电脑已经开始维修啦！',
                'state_info'=>'维修人员: '.D('User')->getLinkNameByUid($uid)
            );
        D('RepairState')->addStatue($stateInfo);
        //向需要维修的用户 发送notify信息
        $config = array(
            'content'=>'您的电脑已经开始维修啦！',
            'url'=>'#'
        );
        D('Notify')->sendNotify($repairRecord['uid'], 'new_state', $config);
        return true;
    }

    /**
     * 假删除维修记录
     * @param $rid 维修记录的id
     * @param $repair_record_uid 提交维修记录的用户id
     * @param null $uid 删除记录的用户id 默认为当前的登录的用户
     */
    public function delRepairRecord($rid , $repair_record_uid,$uid = NULL){
        $rid = intval($rid);
        $repair_record_uid = intval($repair_record_uid);
        if(is_null($uid)) $uid = $GLOBALS['e8']['mid'];
        $uid = intval($uid);

        //验证用户是否有删除该条维修记录的权限
        $checkPermission = checkPermission('core_admin','repair_record_del',$uid);
        if(!$checkPermission){
            $this->error = '您并没有权限删除该条维修记录!';
            return false;
        }
        return D('RepairRecord')->delRepairRecord($rid);
    }
    public function getError(){
        return $this->error;
    }
}