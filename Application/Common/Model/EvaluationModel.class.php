<?php
/**����ģ��
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/16
 * Time: 22:35
 */

namespace Common\Model;


use Think\Model;

class EvaluationModel extends Model
{
    /**���һ��������Ϣ
     * $evaluation = array(
     *      'type_id'=>,
     *      'grade'=>,
     *      'record_id'=>
     * );
     * @param $evaluation
     * @return ���������id
     */
    public function addEvaluation($evaluation){
        !empty($evaluation) || E('������Ϣ��������');
        if(!$evaluationId = $this->add($evaluation)) E('���������Ϣʧ��');
        return $evaluationId;
    }

    /**��ȡָ����������Ϣ
     * @param $recordId
     * @return array
     */
    public function getEvaluation($recordId){
        
    }
}