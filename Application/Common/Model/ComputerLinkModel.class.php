<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/13
 * Time: 21:38
 */

namespace Common\Model;


use Think\Model;

class ComputerLinkModel extends Model
{
    protected $tableName = 'computer_model_link';

    /**获取某个用户的所有电脑
     * @param $uids
     * @return array
     */
    public function getUserComputerList($uids){
        is_array($uids) || $uids = explode(',',$uids);
        $uids = array_unique(array_filter($uids));//删除数组中为空的值和重复的值
        //TODO 缓存处理
        $return = array();
        foreach($uids as $uid){
            //查询$uid用户的所有电脑
            if($res = $this->where('uid='.$uid)->select()){
                foreach($res as $v) {
                    //获取电脑名称
                    $return[$uid][] =
                        array('id'=>$v['computer_model_id'],
                        'computer_name' => D('Computer')->getComputerNameById($v['computer_model_id']));
                }
            }else{//该用户没有添加电脑
                $return[$uid] = null;
            }
        }
        return $return;
    }
}