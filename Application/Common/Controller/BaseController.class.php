<?php
namespace Common\Controller;
use Think\Controller;

class BaseController extends Controller {
    protected $site = array();
    protected $mid;//当前登录的用户的id
    protected $user;//当前登录的用户的信息

    /**
     * 控制器初始化
     * @return void
     */
    protected function _initialize(){

        $this->initSite();
        $this->initModule();
        $this->initUser();
        //语言
        $GLOBALS['_lang'] = array();
        $GLOBALS['_lang'] = array_merge($GLOBALS['_lang'],C('NOTIFY'));

    }
    /**
     * 站点信息初始化
     * @access private
     * @return void
     */
    private function initSite() {
        $this->setTitle();
        $this->setKeywords();
        $this->setDescription();
        $this->site['site_name'] = 'e8阳光维修服务';
        $this->site['keywords'] = 'e8阳光维修服务';
        $this->site['description'] = 'e8阳光维修服务';
        $this->site['site_url'] = 'http://www.baidu.com';
        $GLOBALS['e8']['site'] = $this->site;
        $this->assign('site',$this->site);
    }
    private function initUser(){
        // 验证登陆
        if ( D('Passport')->needLogin() ) {
            // 跳转到登录页面
            $this->redirect('Login/index');
        }
        //当前登录者uid
        if(intval($_SESSION['mid']) !== 0){
            //非游客登录
            $GLOBALS['e8']['mid'] = $this->mid = intval($_SESSION['mid']);
            $GLOBALS['e8']['user'] = $this->user = D('User')->getUserInfo($this->mid);
            $this->assign('mid', $this->mid);   //登录者
            $this->assign('user', $this->user); //当前登陆的人
        }

        return true;
    }
    /**
     * 初始化模块(Home)
     */
    private function initModule() {
        $this->assign('APP_PUBLIC_URL',__ROOT__."/Public/Home/");
    }

    /**
     * 设置顶部
     */
    public function setHeader($headTitle){
        $this->assign('headTitle',$headTitle);
    }
    /**
     * 模板Title
     */
    public function setTitle($title = '') {
        $this->assign('_title',$title);
    }
    /**
     * 模板keywords
     */
    public function setKeywords($keywords = '') {
        $this->assign('_keywords',$keywords);
    }
    /**
     * 模板description
     */
    public function setDescription($description = '') {
        $this->assign('_description',$description);
    }
}