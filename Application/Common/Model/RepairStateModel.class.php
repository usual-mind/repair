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
    //缓存对象
    private $cacheObj = null;
    public function _initialize(){
        //初始化缓存对象
        $this->cacheObj = S(array('type'=>'file','prefix'=>'think','length'=>100));
    }
    /**
     * 添加维修记录状态信息
     * @param $stateInfo
     * $stateInfo=>array(
     *      'repair_record_id'=>,
     *      'state_node'=>,
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

    /**
     * 获取节点列表
     * @return array 节点列表数据
     */
    public function getNodeList() {
        // 缓存处理
        if($list = static_cache('state_node')) {
            return $list;
        }
        if(($list = $this->cacheObj->get('state_node')) == false) {
            $list = $this->select();
            //将node字段变成数组的索引
            $return = array();
            foreach($list as $v){
                $return[$v['node']] = $v;
                unset($return[$v['node']]['node']);
            }
            $this->cacheObj->set('state_node', $list);
        }
        static_cache('state_node', $list);
        return $list;
    }
    /**
     * 获取指定节点信息
     * @param string $node 节点Key值
     * @return array 指定节点信息
     */
    public function getNode($node){
        $list = $this->getNodeList();
        return $list[$node];
    }
}