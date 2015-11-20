<?php
/**
 * 评价模型
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
     *      'type_id'=>1, default = 1   //评价类型
     *      'grade'=>,                  //评价分数
     *      'record_id'=>,              //评价对应的记录id
     * );
     * @param $evaluation
     * @param $repairmemId //维修人的id
     * @param $content //评价类容
     * @return 插入的评价id
     */
    public function addEvaluation($evaluation ,$repairmemId,$content){
        // 判断用户是否登录
        if(!$GLOBALS['e8']['mid']) E('请先登陆');
        !empty($evaluation) || E('评价信息参数有误');
        empty($evaluation['type_id']) && $evaluation['type_id'] = 1;
        $evaluation['ctime'] = time();
        if(!$evaluationId = $this->add($evaluation)) E('添加评价信息失败');
        //添加评价内容，在添加评价的时候会通知维修者
        $data['record_id'] = $evaluation['record_id'];
        $data['to_uid'] = $repairmemId;
        $data['content'] = $content;
        D('Comment')->addComment($data);
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