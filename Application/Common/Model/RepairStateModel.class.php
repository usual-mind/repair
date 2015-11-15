<?php
/**
 * Created by PhpStorm.
 * User: TAOYU
 * Date: 2015/11/14
 * Time: 18:07
 *
 * 维修状态信息跟踪 表模型
 */

namespace Common\Model;


use Think\Model;

class RepairStateModel extends Model
{
    protected $tableName = 'repair_state';
    /**
     * 添加维修记录状态信息
     * @param $stateInfo
     * $stateInfo=>array(
     *      'repair_record_id'=>,
     *      'state_title'=>,
     *      'state_info'=>
     * );
     * @return mixed
     */
    public function addStatue($stateInfo){
        if(empty($stateInfo) || !is_array($stateInfo)) E('参数错误');
        $stateInfo['ctime'] = time();
        if(!$id = $this->add($stateInfo)) E('添加维修记录状态信息失败');
        return $id;
    }

    /**获取维修状态信息
     * @param $recordIds 维修记录ID
     * @return array
     */
    public function getRepairState($recordIds){
        //TODO 缓存处理
        is_array($recordIds) || $recordIds = explode(',',$recordIds);
        $recordIds = array_unique(array_filter($recordIds));//删除数组中为空的值和重复的值
        $map['repair_record_id'] = array('IN',$recordIds);
        $res = $this->field('id',true)->where($map)->select();

        //组装要返回的数据
        $return =array();
        foreach($recordIds as $recordId){
            $condition['repair_record_id'] = $recordId;
            $return[$recordId] = $this->field('state_title,state_info,ctime')->where($condition)->select();
        }
        return $return;
    }

}