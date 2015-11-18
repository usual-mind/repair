<?php
/**
 * 值班信息模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/18
 * Time: 20:20
 */

namespace Common\Model;


use Think\Model;

class DutyInfoModel extends Model
{
    protected $tableName = 'duty_info';

    /**
     * 获取当天值班人员
     * @return 当天值班人员数组
     */
    public function getToDayDuty(){
        //TODO 缓存处理
        $condition['dutytime'] = date('w');
        $dutyInfo = $this->field('uid')->where($condition)->select();
        if(!$dutyInfo) return array();
        //将二维数组转成一维数组
        if(function_exists('array_column')){
            return array_column($dutyInfo,'uid');
        }else{
            $return = array();
            foreach($dutyInfo as $v){
                $return[] = $v['uid'];
            }
            return $return;
        }
    }

    /**该uid当天是否值班
     * @param $uid
     * @return bool
     */
    public function isDuty($uid){
        $uid = intval($uid);
        //TODO 缓存处理
        $dutytime = $this->field('dutytime')->where('uid='.$uid)->find();
        if(date('w') == $dutytime['dutytime'])
            return true;
        else
            return false;
    }
}