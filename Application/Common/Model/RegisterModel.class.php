<?php

/**注册信息验证模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/11
 * Time: 8:31
 */

namespace Common\Model;
class RegisterModel {
    private $error;
    private $_user_model;																// 用户模型对象字段
    //private $_email_reg = '/[_a-zA-Z\d\-\.]+(@[_a-zA-Z\d\-\.]+\.[_a-zA-Z\d\-]+)+$/i';    // 邮箱正则规则
    //private $_name_reg = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_\.]+$/u';							// 昵称正则规则
    //微信号正则
    private $_weixin_reg = '/^[A-Za-z][\d_\-a-zA-Z]{5,19}$/i';
    //手机号码正则
    private $_tel_reg = '/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/';
    //学号正则
    private $_student_id_reg = '/\d{10}/';
    //姓名正则
    private $_name_reg = '/[\u4e00-\u9fa5]{2,}/';

    public function __construct(){
        $this->_user_model = D('User');
    }
    /**
     * 检测微信是否可用
     * @param String $weixin
     * @return boolean
     */
    public function checkWeiXin($weixin){
        //匹配昵称的正则表达式
        if(preg_match($this->_weixin_reg, $weixin)==0){
            $this->error = '请输入正确的微信号';
            return false;
        }
        if($this->_user_model->hasUser($weixin)){
            $this->error = '该微信称已被使用';
            return false;
        }
        return true;
    }
    /**
     * 检测姓名是否可用
     * @param String $name
     * @return boolean
     */
    public function checkName($name){
        //匹配昵称的正则表达式
        if(preg_match($this->_name_reg, $name)==0){
            $this->error = '请输入正确的姓名';
            return false;
        }
        return true;
    }
    /**
     * 检测学号是否可用
     * @param String $studentid
     * @return boolean
     */
    public function checkStudentId($studentid){
        //匹配昵称的正则表达式
        if(preg_match($this->_student_id_reg, $studentid)==0){
            $this->error = '请输入正确的学号';
            return false;
        }
        if($this->_user_model->hasUser($studentid)){
            $this->error = '该学号已注册';
            return false;
        }
        return true;
    }
    /**
     * 检测密码是否可用
     * @param unknown $pwd
     * @return boolean
     */
    public function checkPassword($pwd) {
        if(!preg_match('/^[a-zA-Z0-9]+$/',$pwd)){
            $this->error = '密码只能包含字母和数字';
            return false;
        }
        $length = strlen($pwd);
        if($length < 6) {
            $this->_error = '密码太短了，最少6位';
            return false;
        } else if ($length > 15) {
            $this->_error = '密码太长了，最多15位';
            return false;
        }
        return true;
    }
    public function getError(){
        return $this->error;
    }
}