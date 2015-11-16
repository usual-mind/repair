<?php
/**
 * TODO 树形结构的模型提供的方法差不过 可以抽象出一个树形结构模型的公用模型
 * Created by PhpStorm.
 * User: TAOYU
 * Date: 2015/11/9
 * Time: 19:24
 */

namespace Common\Model;


use Think\Model;

class ClassesModel extends Model
{
    protected $tableName = 'classes';
    //班级全称格式化字符串
    private $classesFormateStr = '%s/%d级/%s(%d)班';
    //缓存对象
    private $cacheObj = null;

    public function _initialize()
    {
        //初始化缓存对象
        $this->cacheObj = S(array('type' => 'file', 'prefix' => 'think', 'length' => 100));
    }

    /**
     * 设置班级全称格式化字符串
     * @param $fromateStr
     * @return $this
     */
    public function setClassesFormateStr($fromateStr)
    {
        $this->classesFormateStr = $fromateStr;
        return $this;
    }

    /**
     * 获取班级全称格式化字符串
     * @return string
     */
    public function getClassesFormateStr()
    {
        return $this->classesFormateStr;
    }

    /**
     * 根据班级id获取班级全称
     * 该函数做了缓存操作 随便调用
     * @param $classId 班级id
     * @param $returnStr 是否要返回字符传默认为false
     * @return String|array 班级全称字符串或数组
     */
    public function getClassById($classId, $returnStr = false)
    {

        //首先从缓存中获取 如果缓存中没有调用一下recursiveGetClass()获取并缓存
        $cacheKey = 'classArr_' . $classId;
        if (!($array = static_cache($cacheKey))) {//从全局静态缓存获取
            if (!($array = $this->cacheObj->$cacheKey)) {//从file缓存中获取
                //2个缓存中都没有
                $this->recursiveGetClass($classId, $array);
                //设置缓存
                $this->cacheObj->$cacheKey = $array;
            }
            static_cache($cacheKey, $array);//设置静态缓存
        }
        if ($returnStr) {//如果需要返回字符串
            return sprintf($this->classesFormateStr, $array[3], substr($array[2], -2), $array[1], $array[0]);
        }
        //递归调用后 结果数组是反序的需要转换一下
        $array = array_reverse($array);
        return $array;
    }

    /**
     * 清除由getClassById()产生的班级全称数组缓存
     * @param $classIds 可以是单个班级id或者是一组班级id
     * @return bool
     */
    public function clearCache($classIds)
    {
        if (empty($classIds)) E('缺少参数');
        is_array($classIds) || $classIds = explode(',', $classIds);
        $classIds = array_unique(array_filter($classIds));//删除数组中为空的值和重复的值

        foreach ($classIds as $v) {
            //先清除全局静态缓存
            static_cache('classArr_' . $v, null);
            //清除文件缓存
            if ($this->cacheObj->rm('classArr_' . $v))
                //清除由getChildByPid()产生的查询缓存
                S('child_class_' . $v, null);
        }
        return true;
    }

    /**
     * 递归获取id = $classId的班级全称
     * @param $classId 班级id
     * @param $array  班级全称数组
     * @return bool 获取成功返回true 失败返回false
     */
    private function recursiveGetClass($classId, &$array)
    {
        //$classId = 0说明已经获取到学院了 直接返回true
        if (0 == $classId) return true;
        if (!($res = $this->field('title,pid')->where(array('id' => $classId))->find())) {
            E('id为' . $classId . '的班级不存在');
            //$this->error='id为'.$classId.'的班级不存在';
            return false;
        }
        $array[] = $res['title'];
        //递归调用 把pid作为classId再次传给自己
        $this->recursiveGetClass($res['pid'], $array);
    }

    /**
     * 通过一个父级的id 获取下面所有的儿子节点
     * @param $pid
     * @return bool|array 如果没有儿子节点返回false 有则返回所有儿子节点数组
     */
    private function getChildByPid($pid)
    {
        //查询缓存 缓存24小时
        return $this->cache('child_class_' . $pid)->field('id,title')->where(array('pid' => $pid))->order('sort')->select();
    }

    /**获取所有的学院
     * @return array|String
     */
    public function getAllDepartment(){
        return $this->getChildByPid(0);
    }
    /**
     * 获取某个学院下的所有年级
     * @param $pid 学院的id
     * @return array|String
     */
    public function getGradeByPid($pid){
        return $this->getChildByPid($pid);
    }

    /** 获取某年级下的专业
     * @param $pid
     * @return array|bool
     */
    public function getMajorByPid($pid){
        return $this->getChildByPid($pid);
    }

    /**获取某专业下的所有班级
     * @param $pid
     * @return array|bool
     */
    public function getClassByPid($pid){
        return $this->getChildByPid($pid);
    }

    /**添加班级信息
     * //可以传入所有信息
     * $classInfo => array('计算机学院','2015'，'网络工程','3');
     * //也可以传入部分信息 但是要指定下$pid
     * $classInfo => array('2015'，'网络工程','3');
     * @param array $classInfo 可以是数组或者是字符串用逗号隔开 比如'计算机学院,2015,网络工程,3'
     * @param int $pid
     * @return bool|int 返回插入的班级id
     */
    public function addClass($classInfo,$pid=0)
    {
        is_array($classInfo) || $classInfo = explode(',', $classInfo);
        $classInfo = array_unique(array_filter($classInfo));//删除数组中为空的值和重复的值
        foreach($classInfo as $title){
            if(!$pid = $this->addClassItem($title,$pid))
                return false;
        }
        return $pid;//最后插入的肯定是班级号
    }

    /**添加某一项信息
     * @param $data
     * @return int 返回插入记录的id
     */
    private function addClassItem($title,$pid)
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
}