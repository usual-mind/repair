<?php
/**评价模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/16
 * Time: 22:35
 */

namespace Common\Model;


use Think\Model;

class EvaluationModel extends Model
{
    /**添加一条评价信息
     * $evaluation = array(
     *      'type_id'=>,
     *      'grade'=>,
     *      'record_id'=>,
     *      'repairmem_id'=>,//维修人id
     *      'content'=>//评价内容
     * );
     * @param $evaluation
     * @return 插入的评价id
     */
    public function addEvaluation($evaluation){
        !empty($evaluation) || E('评价信息参数有误');
        $evaluation['ctime'] = time();
        if(!$evaluationId = $this->add($evaluation)) E('添加评价信息失败');
        //通知维修者
        D('Notify')->sendNotify();
        //添加评价内容
        D('comment')->addComment();
        return $evaluationId;
    }

    /**获取指定的评价信息
     * @param $recordId
     * @return array
     */
    public function getEvaluation($recordId){

        if(($condition['record_id'] = intval($recordId)) <= 0) E('评价信息参数错误' );
        $evaluation = $this->field('e.grade,e.ctime,t.title')->alias('e')
            ->join('RIGHT JOIN __EVALUATION_TYPE__ t ON e.type_id = t.id')
            ->where($condition)->order('t.sort')->limit('1')->select();
        if(!$evaluation) E('获取评价信息失败');
        //获取评价内容

        return $evaluation;
    }

}