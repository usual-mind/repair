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
     * 获取错误信息
     * @return string
     */
    public function getError(){
        return $this->error;
    }
    /**
     * 维修电脑
     * @param $repairRecordId
     * @param $uid 维修者id；默认为当前登录的用户
     */
    public function repairComputer($repairRecordId,$uid=NULL){
        $repairRecordId = intval($repairRecordId);
        //检查用户是否有维修电脑权限
        $repairPermission = checkPermission('core_normal','repair',$uid);
        if(!$repairPermission){

            $this->error = 'Sorry! 您并没有权限维修电脑!';
            return false;
        }
        $repairRecord = D('RepairRecord')->getRepairRecord($repairRecordId);
        if(!$repairRecord){
            $this->error = D('RepairRecord')->getError();
            return false;
        }
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
     * @param null $uid 删除记录的用户id 默认为当前的登录的用户
     */
    public function delRepairRecord($rid , $uid = NULL){
        $rid = intval($rid);
        //验证用户是否有删除该条维修记录的权限
        $checkPermission = checkPermission('core_admin','repair_record_del',$uid);
        if(!$checkPermission){
            $this->error = '您并没有权限删除该条维修记录!';
            return false;
        }
        return D('RepairRecord')->delRepairRecord($rid);
    }

    /**
     * 用户撤销维修 TODO 撤销原因
     * @param $rid 维修记录的id
     * @param null $uid 撤销维修的用户id 默认为当前登录的id
     */
    public function revokeRepair($rid , $uid = NULL){;
        $repairRecordId = intval($rid);
        $repairRecord = D('RepairRecord')->getRepairRecord($repairRecordId);
        if(!$repairRecord){
            $this->error = D('RepairRecord')->getError();
            return false;
        }
        //判断用户是否有撤销维修的权限
        $revokePermission = checkPermission('core_admin','revoke_repair',$uid);
        if(!$revokePermission){//先判断admin模块是否有cancel_repair权限
            $revokePermission = checkPermission('core_normal','revoke_repair',$uid);
            if($revokePermission){
                //判断提交维修记录的uid是否为$uid 如果不是uid则没有权限取消维修
                if($repairRecord['uid'] != $uid){
                    $revokePermission = false;
                }
            }
            if(!$revokePermission){
                $this->error = 'Sorry! 您并没有取消维修的权限！';
                return false;
            }

        }
        //判断维修状态是不是未维修
        if($repairRecord['status'] != 0){
            $this->error = 'Sorry! 撤销维修失败。您的电脑已经不是未维修状态了!';
            return false;
        }
        //撤销维修
        if(!D('RepairRecord')->revokeRepair($repairRecordId)){
            $this->error = '撤销维修失败!';
            return false;
        }
        //添加维修动态
        $stateInfo = array(
            'repair_record_id'=>$repairRecordId,
            'state_node'=>'cancel_repair',
            'state_title'=>'维修被撤销',
        );
        if($repairRecord['uid'] == $uid){
            $stateInfo['state_info'] = '用户自行撤销';
        }else{
            $stateInfo['state_info'] = '维修被'.D('User')->getLinkNameByUid($uid).' 撤销';
        }
        D('RepairState')->addStatue($stateInfo);
        return true;
    }

    /**
     * E8成员取消维修 TODO 取消原因
     * @param $rid 维修记录id
     * @param int $uid 触发取消维修的用户id  默认为当前登录的用户id
     */
    public function cancelRepair($rid , $uid = NULL){
        is_null($uid) && $uid = $GLOBALS['e8']['mid'];
        $uid = intval($uid);
        //判断用户是否有有维修的权限
        if(!checkPermission('core_normal','repair',$uid)){
            $this->error = '你并没有权限!';
            return false;
        }

        //添加维修动态
        //发送notify给每个当前正在值班的用户
    }
}