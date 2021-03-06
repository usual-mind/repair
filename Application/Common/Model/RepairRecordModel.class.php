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
    /**
     * 获取维修记录列表
     * //TODO 考虑分页
     * @param array $map
     * @param string $field
     * @return mixed|null
     */
    public function _getRepairRecordList(array $map , $field = '*'){

        if(!$records = $this->field($field)->where($map)->order('start_time DESC')->select()){
            //这个用户还没有维修记录
            return null;
        }
        foreach($records as &$record){
            if(!empty($record['repairmem_id'])){
                //维修人员
                $record['repairmem'] = D('user')->getLinkNameByUid($record['repairmem_id']);
            }
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
            //获取最近的维修状态
            $record['state'] = D('RepairState')->getLastNode($record['id']);
            //获取电脑全称
            $record['computer_name'] = D('Computer')->getComputerNameById($record['computer_id']);
            unset($record['is_del'],$record['image_set_id']);
        }
        //因为这里的$record 是 foreach中用了引用的变量 如果不释放会发生意想不到的情况
        unset($record);
        return $records;
    }

    /**
     * 获取所有没有维修任务列表
     */
    public function getNotRepairList(){
        //检查用户是否有维修权限
        if(!checkPermission('core_normal','repair')){
            $this->error = '你并没有权限!';
            return false;
        }
        //TODO 缓存处理
        $condition['status'] = 0;
        $condition['is_del'] = 0;
        $records = $this->_getRepairRecordList($condition);
        return $records;
    }
    /**
     * 获取某个E8成员所有的维修记录
     * //TODO 考虑分页
     * @param $repairmemId维修者id 默认为当前登录的id
     */
    public function getRepairRecordByRepairmemId($repairmemId = NULL){
        //检查用户是否有维修权限
        if(!checkPermission('core_normal','repair')){
            $this->error = '你并没有权限!';
            return false;
        }
        is_null($repairmemId) && $repairmemId = $GLOBALS['e8']['mid'];
        $repairmemId = intval($repairmemId);
        //TODO 缓存处理
        $condition['repairmem_id'] = $repairmemId;
        $condition['is_del']       = 0;
        $records = $this->_getRepairRecordList($condition);
        return $records;

    }
    /**获取某个用户的维修记录列表
     * //TODO 考虑分页
     * @param $uids
     * @return array 维修记录列表数组
     */
    public function getUserRepairRecords($uid){

        //TODO 缓存处理
        $condition['uid']=intval($uid);
        $condition['is_del']=0;
        $records = $this->_getRepairRecordList($condition);
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
        if(!$repairRecord = $this->where($condition)->find()){

            //E('获取ID为'.$recordId.'的维修记录详情失败'.$this->getError());
            $this->error = '没有找到该维修记录!';
            return false;
        }

        if(!empty($repairRecord['image_set_id'])){
            $repairRecord['has_image'] = 1;
            //获取图片
            $repairRecord['images'] = D('FaultImageSet')->getImagesBySetId($repairRecord['image_set_id']);
        }else{
            //没有上传图片使用默认图片
            $repairRecord['has_image'] = 0;
            $repairRecord['images'] = D('FaultImageSet')->getDefaultImage();
        }
        //维修人员
        empty($repairRecord['repairmem']) || $repairRecord['repairmem'] = D('user')->getLinkNameByUid($repairRecord['repairmem_id']);
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
     *      'problem_desc' =>,
     *      'computer_id'=>,
     *      'image_set_id' =>//如果没有图片就不用有这个键
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
        $map['uid']             =   $GLOBALS['e8']['mid'];

        empty($data['image_set_id']) || $map['image_set_id'] = $data['image_set_id'];

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
    /**
     * 假删除维修记录
     * @param $rid 维修记录id
     * @return bool
     */
    public function delRepairRecord($rid){
        $rid = intval($rid);
        if($rid<=0) return false;

        return $this->where('id='.$rid)->setField('is_del','1');
    }

    /**
     * 设置维修状态
     * @param $rid
     */
    public function setRepairRecordStatus($rid,$status){
        $rid = intval($rid);
        $status = intval($status);
        $res = $this->where('id='.$rid)->setField('status',$status);
        if(!$res) E('设置维修状态失败');
        return $res;
    }
}