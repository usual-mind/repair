<?php
/**
 * 维修记录模型
 * Created by PhpStorm.
 * User: TaoYu
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
     * @return array 维修记录列表数组
     */
    public function getUserRepairRecords($uid){
        //TODO 缓存处理
        $condition['uid']=intval($uid);
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
            //获取图片
            if(!empty($record['image_set_id'])){

                $images = D('FaultImageSet')->getImagesBySetId($record['image_set_id']);
                $record['images'] = array_shift($images);//获取第一张图片

            }
            //获取电脑全称
            $record['computer_name'] = D('Computer')->getComputerNameById($record['computer_id']);
            unset($record['is_del'],$record['image_set_id']);
        }
        //因为这里的$record 是 foreach中用了引用的变量 如果不释放会发生意想不到的情况
        unset($record);
        return $records;
    }

    /**获取某个维修记录详情（包括所有的图片和维修状态信息）
     * @param $recordId
     * @return array 维修记录详情数组
     */
    public function getRepairRecord($recordId){
        //TODO 缓存处理
        $condition['id'] = intval($recordId);
        $condition['is_del'] = 0;
        if(!$repairRecord = $this->where($condition)->find())
            E('获取ID为'.$recordId.'的维修记录详情失败'.$this->getError());
        if(!empty($repairRecord['image_set_id'])){
            //获取图片
            $repairRecord['images'] = D('FaultImageSet')->getImagesBySetId($repairRecord['image_set_id']);
        }
        //维修人员
        $repairRecord['repairmem'] = D('user')->getLinkNameByUid($repairRecord['repairmem_id']);
        //提交时间
        $repairRecord['start_time'] =friendlyShowTime($repairRecord['start_time']);
        //获取电脑全称
        $repairRecord['computer_name'] = D('Computer')->getComputerNameById($repairRecord['computer_id']);
        //获取维修状态
        $repairRecord['repairState'] = D('RepairState')->getRepairState($recordId);

        unset($repairRecord['is_del'],$repairRecord['image_set_id']);
        return $repairRecord;
    }
    /**
     * 添加一条维修信息
     * $data 中的一些键
     * array(
     *      problem_desc =>,
     *      computer_id=>,
     *      image_set_id =>//如果没有图片就不用有这个键
     * @param $data
     * @return 返回插入的id
     */
    public function addRepairRecord($data){

        
        if(empty($data)) E('参数为空');
        if(!isset($GLOBALS['e8']['mid']) || intval($GLOBALS['e8']['mid'])<=0) E('请先登录!');
        empty($data['problem_desc']) && E('问题描述不能为空');
        empty($data['computer_id']) && E('computer_id不能为空');
        $map['problem_desc']    =   html2Text($data['problem_desc']);
        $map['computer_id']     =   intval($data['computer_id']);
        $map['start_time']      =   time();
        /*if(!empty($data['images'])){
            //插入图片
            $map['repair_record_id'] = D('FaultImageSet')->addImages($data['images']);
        }*/
        //插入维修信息
        if(!$repairRecordId = $this->add($map)) E('添加维修记录失败! '.$this->getError());
        //添加维修状态
        $state['repair_record_id']  =   $repairRecordId;
        $state['state_title']       =   '维修信息已经提交';
        $state['state_info']        =   '请耐心等待';
        $state['state_node']        =   'commit';
        D('RepairState')->addStatue($state);

        //推送消息给当天值班人员
        $config['name'] = D('User')->getLinkNameByUid($GLOBALS['e8']['mid']);//获取修电脑的人的名字
        $config['computer'] = D('Computer')->getComputerNameById($map['computer_id']);//获取电脑全称
        //TODO 需要降低耦合
        $config['detailurl'] = U('Index/index',array('id'=>$repairRecordId));

        D('Notify')->sendNotify(
            D('DutyInfo')->getToDayDuty(),
            'new_repair_record',
            $config
            );
        return $repairRecordId;//返回插入的维修记录id
    }
}