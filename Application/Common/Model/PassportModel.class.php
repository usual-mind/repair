<?php
/**通行证模型
 * Created by PhpStorm.
 * User: TaoYu
 * Date: 2015/11/13
 * Time: 13:07
 */

namespace Common\Model;


class PassportModel
{
    protected $error = null;		// 错误信息
    protected $success = null;		// 成功信息
    protected $isFirstLogin = array();//是否是第一次登录
    /**
     * 返回最后的错误信息
     * @return string 最后的错误信息
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 返回最后的错误信息
     * @return string 最后的错误信息
     */
    public function getSuccess() {
        return $this->success;
    }

    /**
     * 初始化
     */
    public function _initialize(){
        //session_start
        if(!session_id())
            session_start();
        //登录的用户id 默认为游客 mid=0
        isset($_SESSION['mid']) || $_SESSION['mid']=0;

        //为了防止同目录下多个网站session冲突
        isset($_SESSION['SITE_KEY']) || $_SESSION['SITE_KEY'] = getSiteKey();
    }

    /**
     * 构造方法
     */
    public function __construct(){
        $this->_initialize();
    }
    /**
     * 验证是否需要登录
     */
    public function needLogin(){

        if($this->isLogged()){//判断是否已经登录
            //已经登录直接返回false

            return false;
        }else{
            //游客访问
            $acl = C('access');//获取游客可以访问的操作
            return !((array_key_exists(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,$acl)
                    && $acl[MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME] === true)
                || (array_key_exists(MODULE_NAME.'/'.CONTROLLER_NAME.'/*',$acl)
                    && $acl[MODULE_NAME.'/'.CONTROLLER_NAME.'/*'] === true)
                || (array_key_exists(MODULE_NAME.'/*/*',$acl) && $acl[MODULE_NAME.'/*/*'] === true));

        }

    }
    /**
     * 验证用户是否已登录
     * 按照session -> cookie的顺序检查是否登陆
     * @return boolean 登陆成功是返回true, 否则返回false
     */
    public function isLogged() {
        // 验证本地系统登录
        if(intval($_SESSION['mid']) > 0 && $_SESSION['SITE_KEY']==getSiteKey()) {
            return true;
        } else if($uid = $this->getCookieUid()) {
            //TODO 判断用户是否禁用
            /*$isDisable = model('DisableUser')->isDisableUser($uid);
            if ($isDisable) {
                $this->error = '此用户已被禁用';
                $this->logoutLocal();
                return false;
            }*/
            return $this->_recordLogin($uid);
        } else {
            //没有登录
            //unset($_SESSION['mid']);
            //unset($_SESSION['SITE_KEY']);
            return false;
        }
    }
    /**
     * 获取cookie中记录的用户ID
     * @return integer cookie中记录的用户ID
     */
    public function getCookieUid() {
        static $cookie_uid = null;
        if(isset($cookie_uid) && $cookie_uid !== null) {
            return $cookie_uid;
        }

        $cookie = cookie('E8_LOGGED_USER');

        $cookie = explode('.', $this->jiemi($cookie));

        $cookie_uid = ($cookie[0] != C('SECURE_CODE')) ? false : $cookie[1];

        return $cookie_uid;
    }
    /**
     * 使用本地帐号登陆，无密码
     * @param string $login 登录名称，weixin|student_id
     * @param boolean $is_remember_me 是否记录登录状态，默认为false
     * @return boolean 是否登录成功
     */
    public function loginLocalWithoutPassword($login, $is_remember_me = false) {
        $login = addslashes($login);
        if(empty($login)) {
            $this->error = '帐号不能为空';			// 帐号不能为空
            return false;
        }

        $uid = D('User')->hasUser($login);

        if(!$uid) {
            $this->error = '帐号不存在';
            return false;
        }

        return $uid>0 ? $this->_recordLogin($uid, $is_remember_me) : false;
    }
    /**
     * 设置登录状态、记录登录知识
     * @param integer $uid 用户ID
     * @param boolean $is_remember_me 是否记录登录状态，默认为false
     * @return boolean 操作是否成功
     */
    private function _recordLogin($uid, $is_remember_me = false) {

        // 注册cookie
        if(!$this->getCookieUid() && $is_remember_me ) {
            $expire = 3600 * 24 * 30;
            cookie('E8_LOGGED_USER', $this->jiami(C('SECURE_CODE').'.'.$uid), $expire);
    }

    // 记住活跃时间
    cookie('E8_ACTIVE_TIME', time() + 60 * 30);
    cookie('login_error_time', null);

    // 更新登陆时间
    M('User')->where('id='.$uid)->setField('last_login_time', $_SERVER['REQUEST_TIME']);

    // 记录登陆知识，首次登陆判断
        empty($this->rel) && $this->rel	= M('login_record')->where('uid = '.$uid)->limit('1')->getField('id');

        // 注册session
        $_SESSION['mid'] = intval($uid);
        $_SESSION['SITE_KEY'] = getSiteKey();

        $map['ip'] = ip2long(get_client_ip());
        $map['ctime'] = time();

        $this->success = '登录成功，努力加载中。。';

        if($this->rel) {
            M('login_record')->where('uid = '.$uid)->save($map);
        } else {
            $map['uid'] = $uid;
            M('login_record')->add($map);
        }

        return true;
    }

    /**
     * 注销登录
     * @return void
     */
    public function logout() {
        unset($_SESSION['mid'],$_SESSION['SITE_KEY']); // 注销session
        cookie('E8_LOGGED_USER', NULL);	// 注销cookie
    }
    /**
     * 加密函数
     * @param string $txt 需加密的字符串
     * @param string $key 加密密钥，默认读取SECURE_CODE配置
     * @return string 加密后的字符串
     */
    private function jiami($txt, $key = null)
    {
        empty($key) && $key = C('SECURE_CODE');
        return tsauthcode($txt, 'ENCODE', $key);
    }

    /**
     * 解密函数
     * @param string $txt 待解密的字符串
     * @param string $key 解密密钥，默认读取SECURE_CODE配置
     * @return string 解密后的字符串
     */
    private function jiemi($txt, $key = null) {
        empty($key) && $key = C('SECURE_CODE');

        return tsauthcode($txt, 'DECODE', $key);
    }
}