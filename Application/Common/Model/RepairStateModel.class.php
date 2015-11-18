<?php
/**维修状态模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/14
 * Time: 18:07
 *
 * 维修状态信息跟踪 表模型
 */

namespace Common\Model;


use Think\Model;

class RepairStateModel extends Model
{
    protected $tableName = 'repair_state_node';
    //缓存对象
    private $cacheObj = null;
    private $_repair_state = null;//repair_state表model
    public function _initialize(){
        //初始化缓存对象
        $this->cacheObj = S(array('type'=>'file','prefix'=>'think','length'=>100));
        $this->_repair_state = M('repair_state');
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
        //TODO 需要登录
        if(empty($stateInfo) || !is_array($stateInfo)) E('参数错误');
        $stateInfo['ctime'] = time();
        if(!$id = $this->_repair_state->add($stateInfo)) E('添加维修记录状态信息失败');
        return $id;
    }

    /**获取维修状态信息
     * @param $recordIds 维修记录ID
     * @return array 返回的数组的键就是维修记录id
     */
    public function getRepairState($recordId){
        //TODO 缓存处理
        $map['repair_record_id'] = $recordId;
        $res = $this->_repair_state->field('id',true)->where($map)->select();


        foreach($res as &$v){
            /* ty注释于2015年11月16日 09:29:23  因为RepairState不需要node信息
            //获取每个维修状态的node节点信息
            $v['node_info'] = $this->getNode($v['state_node']);
            //获取节点信息中的node_info文本
            $v['node_info'] = $v['node_info']['node_info'];
             */
            $v['ctime']     = friendlyShowTime($v['ctime']);
        }
        unset($v);//因为$v是地址引用所有需要unset 否则会出现意想不到的情况
        return $res;
    }


    public function getLastNode($repairId){
        //TODO 缓存处理
        $repairId = intval($repairId);
        if(!$repairId) E('参数错误');
        if(!$node = $this->_repair_state->field('state_node')->where('repair_record_id='.$repairId)->order('ctime desc')->find()) E('获取维修状态节点失败');
        return $this->getNode($node['state_node']);

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
                    $data = $this->select();
                    //将node字段变成数组的索引
                    $list = array();
                    foreach($data as $v){
                        //改变node_info的字体颜色
                        $v['node_info'] = '<span style="color:'.$v['font_color'].';">'.$v['node_info'].'</span>';
                        unset($v['font_color']);

                        $list[$v['node']] = $v;
                        unset($list[$v['node']]['node']);
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