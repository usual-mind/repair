<?php
/**
 * 用户与电脑关联模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/13
 * Time: 21:38
 */

namespace Common\Model;


use Think\Model;

class ComputerLinkModel extends Model
{
    protected $tableName = 'computer_model_link';

    /**
     * 添加电脑给某个用户
     *
     * @param $uid
     * @param $computerInfo 可以是数组或者是字符串用逗号隔开 比如'东芝,L800'
     *  //可以传入品牌+型号
     * $classInfo => array('东芝','L800');
     * //也可以传入型号 但是需要指定一下$pid
     * @param $pid 父级id
     * @return bool|int
     */
    public function addComputerToUser($uid,$computerInfo,$pid=0){
        $uid = intval($uid);
        if(!$uid) E('添加电脑失败，UID错误!');
        //先插入电脑品牌和型号 如果该电脑已经存在会返回该电脑的id
        if(!$computerId = D('Computer')->addComputer($computerInfo,$pid)) E('添加电脑型号失败!');
        $data['computer_model_id'] = $computerId;
        $data['uid'] = $uid;
        $id = $this->add($data);//插入数据库
        //清除用户模型缓存
        D('User')->cleanCache($uid);
        return $id;
    }

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