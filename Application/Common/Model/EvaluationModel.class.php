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
     *      'record_id'=>
     * );
     * @param $evaluation
     * @return 插入的评价id
     */
    public function addEvaluation($evaluation){
        !empty($evaluation) || E('评价信息参数有误');
        if(!$evaluationId = $this->add($evaluation)) E('添加评价信息失败');
        return $evaluationId;
    }

    /**获取指定的评价信息
     * @param $recordId
     * @return array
     */
    public function getEvaluation($recordId){
        
    }
}