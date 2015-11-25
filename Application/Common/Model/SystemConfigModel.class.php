<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/25
 * Time: 20:20
 */

namespace Common\Model;


use Think\Model;

class SystemConfigModel extends Model
{
    protected $tableName = 'system_config';
    public function get($key){
        //获取list和key
        list($list,$key) = explode(':',$key);
        if(empty($key)){
            //如果只传入了list 就查询出list下面的所有的值
        }
    }

    public function set($key,$value,$replace = true){
        //获取list和key
        list($list,$key) = explode(':',$key);
        if(empty($list) || empty($key)) E('请传入正确的list:key。');
        $data['value'] = addslashes(serialize($value));//将键值序列化处理
        if($replace){//覆盖配置
            $condition['list'] = $list;
            $condition['key'] = $key;
            $res = $this->where($condition)->setField('value',$data['value']);

        }else {
            $data['list'] = $list;
            $data['key'] = $key;
            $res = $this->add($data);
        }

        if(!$res) E('设置系统配置出错!');
        return $this;
    }
}