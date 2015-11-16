<?php
/**
 * Created by PhpStorm.
 * User: TAOYU
 * Date: 2015/11/9
 * Time: 21:25
 */

namespace Common\Model;


use Think\Model;

class RepairRecordModel extends Model
{
    protected $tableName = 'repair_record';

    /**获取某个用户的维修记录列表
     * //TODO 考虑分页
     * @param $uids
     */
    public function getUserRepairRecords($uid){
        //TODO 缓存处理
        $condition['uid']=$uid;
        $condition['is_del']=0;
        if(!$records = $this->where($condition)->select()){
            //这个用户还没有维修记录
            return null;
        }
        foreach($records as &$record){
            //维修人员
            $record['repairmem'] = D('user')->getLinkNameByUid($record['repairmem_id']);
            //提交时间
            $record['start_time'] = friendlyShowTime($record['start_time']);
            /*
            [image_set_id] =>
            [repairmem_id] => 3
            [computer_id] => 3
            [start_time] => 21小时以前
            [end_time] => 0
            [is_del] => 0
             */
            if(!empty($record['image_set_id'])){
                //TODO 获取图片集中的第一张图片
                $record['image'] = D('FaultImageSet')->getImagesBySetId($record['image_set_id']);
            }
            //获取电脑全称
            $record['computer_name'] = D('Computer')->getComputerNameById($record['computer_id']);
            unset($record['is_del'],$record['image_set_id']);
        }
        unset($record);
        return $records;
    }

    /**获取某个维修记录详情（包括所有的图片和维修状态信息）
     * @param $recordId
     */
    public function getRepairRecord($recordId){

    }
    /**
     * 添加一条维修信息
     * $data 中的一些键
     * array(
     *      problem_desc =>,
     *      computer_id=>,
     *      images => array(), //如果没有图片就不用有这个键
     * @param $data
     * @return 返回插入的id
     */
    public function addRepairRecord($data){
        if(empty($data)) E('参数为空');
        empty($data['problem_desc']) && E('问题描述不能为空');
        empty($data['computer_id']) && E('computer_id不能为空');
        $map['problem_desc']    =   $data['problem_desc'];
        $map['computer_id']     =   $data['computer_id'];
        $map['start_time']      =   time();
        if(!empty($data['images'])){
            //插入图片
            $map['repair_record_id'] = D('FaultImageSet')->addImages($data['images']);
        }
        //插入维修信息
        if(!$repairRecordId = $this->add($map)) E('添加维修记录失败! '.$this->getError());
        //添加维修状态
        $state['repair_record_id']  =   $repairRecordId;
        $state['state_title']       =   '维修信息已经提交';
        $state['state_info']        =   '请耐心等待';
        $state['state_node']        =   'commit';
        D('RepairState')->addStatue($state);
        return $repairRecordId;//返回插入的维修记录id
    }
}