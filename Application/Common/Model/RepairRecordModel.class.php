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

    /**
     * @param $uids
     */
    public function getUserRepairRecords($uid){
        //TODO 缓存处理
        $condition['uid']=$uid;
        $condition['is_del']=0;

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
        D('RepairState')->addStatue(array('','请耐心等待	'));

    }
}