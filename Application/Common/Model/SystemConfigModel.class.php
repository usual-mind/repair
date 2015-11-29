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

    public function getList($list){
        //TODO 缓存处理
        $condition['list'] = $list;
        $config = $this->where($condition)->order('id ASC')->select();
        if(!$config) return array();
        $data = array();
        foreach($config as $v){

            $data[$v['key']] = unserialize( stripslashes($v['value']) );
        }
        return $data;
    }

    public function get($key){

        //获取list和key
        list($list,$key) = explode(':',$key,2);
        $data = $this->getList($list);
        return empty($data)?array():$data[$key];
    }

    public function set($key,$value,$replace = true){
        //获取list和key
        list($list,$key) = explode(':',$key,2);
        if(empty($list) || empty($key)) E('请传入正确的list:key。');
        $data['value'] = addslashes( serialize($value) );//将键值序列化处理
        $data['mtime'] = time();
        if($replace){//覆盖配置
            $condition['list'] = $list;
            $condition['key'] = $key;

            $res = $this->where($condition)->save(array('value'=>$data['value'],'mtime'=>$data['mtime']));

        }else {
            $data['list'] = $list;
            $data['key'] = $key;

            $res = $this->add($data);
        }
        if(!$res) E('设置系统配置出错!');
        //if(!$res) echo $this->getLastSql();
        return $this;
    }
}