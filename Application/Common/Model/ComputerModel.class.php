<?php
/**
 * 电脑型号模型
 * TODO 树形结构的模型提供的方法差不过 可以抽象出一个树形结构模型的公用模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/13
 * Time: 21:38
 */

namespace Common\Model;


use Think\Model;

class ComputerModel extends Model
{
    protected $tableName = 'computer_model';
    protected $computerFormatStr = '%s %s';//电脑全称格式化字符串
    //缓存对象
    private $cacheObj = null;
    public function _initialize(){
        //初始化缓存对象
        $this->cacheObj = S(array('type'=>'file','prefix'=>'think','length'=>100));
    }
    /**
     * 设置班级全称格式化字符串
     * @param $fromateStr
     * @return $this
     */
    public function setComputerFormatStr($fromateStr){
        $this->computerFormatStr = $fromateStr;
        return $this;
    }

    /**
     * 获取班级全称格式化字符串
     * @return string
     */
    public function getComputerFormatStr(){
        return $this->computerFormatStr;
    }
    /**
     * 根据电脑型号id获取班级全称
     * 该函数做了缓存操作 随便调用
     * @param $classId 电脑型号id
     * @return String 电脑型号字符串
     */
    public function getComputerNameById($id){

        //从缓存中获取
        $cacheKey = 'computerNameById_'.$id;
        if($str = static_cache($cacheKey)){
            return $str;
        }
        if($str = $this->cacheObj->get($cacheKey)){
            return $str;
        }

        //2个缓存里面都没有 从数据库中获取
        //因为电脑型号就只有父子关系 因此就直接find()两次
        $map['id'] = $id;
        if(!($res = $this->where($map)->find())){
            E('id为'.$map['id'].'的电脑型号不存在');
            //$this->error = 'id为'.$map['id'].'的电脑型号不存在';
            return false;
        }
        $data['model'] = $res['title'];//电脑型号
        $map['id'] = $res['pid'];

        if(!($res = $this->where($map)->find())){
            E('id为'.$map['id'].'的电脑品牌不存在');
            //$this->error = 'id为'.$map['id'].'的电脑型号不存在';
            return false;
        }
        $data['brand'] = $res['title'];//电脑品牌
        $str = sprintf($this->computerFormatStr,$data['brand'],$data['model']);
        //设置缓存
        $this->cacheObj->set($cacheKey,$str);
        static_cache($cacheKey,$str);
        return $str;
    }
    /**
     * 清除由getComputerNameById()产生的电脑名称缓存
     * @param $classIds 可以是单个班级id或者是一组班级id
     * @return bool
     */
    public function clearCache($computerModelIds){
        if(empty($computerModelIds)) E('清除缓存失败');
        is_array($computerModelIds) || explode(',',$computerModelIds);
        $computerModelIds = array_unique(array_filter($computerModelIds));//删除数组中为空的值和重复的值
        foreach($computerModelIds as $v){
            //先清除全局静态缓存
            static_cache('computerNameById_'.$v,null);
            //清除文件缓存
            if(!$this->cacheObj->rm('computerNameById_'.$v))
                //清除由getChildByPid()产生的查询缓存
                S('computer_child_'.$v,null);
        }
        return true;
    }
    /**
     * 通过一个父级的id 获取下面所有的儿子节点
     * @param $pid
     * @return bool|array 如果没有儿子节点返回false 有则返回所有儿子节点数组
     */
    public function getChildByPid($pid){
        //查询缓存 缓存24小时
        return  $this->cache('computer_child_'.$pid,86400)->field('id,title')->where(array('pid'=>$pid))->order('sort')->select();
    }
    /**添加电脑品牌型号
     * //可以传入品牌+型号
     * $classInfo => array('东芝','L800');
     * //也可以传入型号 但是需要指定一下$pid
     * $classInfo => array('L800');
     * @param array $computerInfo 可以是数组或者是字符串用逗号隔开 比如'东芝,L800'
     * @param int $pid
     * @return bool|int 返回插入的电脑型号id
     */
    public function addComputer($computerInfo,$pid=0)
    {

        is_array($computerInfo) || $computerInfo = explode(',', $computerInfo);
        $computerInfo = array_unique(array_filter($computerInfo));//删除数组中为空的值和重复的值

        foreach($computerInfo as $title){

            if(!$pid = $this->addComputerItem($title,$pid))
                return false;
        }
        return $pid;//最后插入的肯定是班级号
    }

    /**添加某一项信息 //TODO 修改代码
     * @param $data
     * @return int 返回插入记录的id
     */
    private function addComputerItem($title,$pid)
    {
        //判断该项在数据库中是否已经存在
        $data['pid'] = $pid;//强行让pid在前面 因为pid字段是加了索引的 查询速度快
        $data['title'] = $title;
        if ($res = $this->field('id')->where($data)->find()) {
            //该项已经存在 直接返回已存在的id
            return $res['id'];
        }
        return $this->add($data);
    }

    /**
     * 根据关键词搜索电脑型号
     * @param $keywords
     * @param $pid
     * @param $limit
     * @return mixed|null
     */
    public function searchModel($keywords,$pid,$limit = 10){
        $condition['pid'] = intval($pid);
        if(!$condition['pid']) return null;
        $bind = array();
        if($keywords){
            $condition['title'] = array('like', ':keywords');
            //参数绑定
            $bind[':keywords'] = trim($keywords).'%';
        }
        return $this->where($condition)->bind($bind)->limit($limit)->select();
    }
}