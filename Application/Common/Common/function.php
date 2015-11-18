<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/9
 * Time: 22:10
 */

/**
 * 全局静态缓存
 * @param String $key   缓存的键名
 * @param string $value 缓存的值如果传入null表示删除$key缓存
 * @return boolean
 */
function static_cache($key,$value=false){
    static $cache = array();

    if($value === false)    //获取缓存
        return isset($cache[$key])?$cache[$key]:false;
    elseif($value == null){ //删除缓存
        unset($cache[$key]);
    }else                   //设置缓存
        $cache[$key] = $value;
}

/**
 * 根据时间参数，计算距离现在过了多长时间
 *
 * @param Integer $time 时间戳
 * @return String $timeStr 距离现在多长时间的字符串
 * @author webdd
 */
function friendlyShowTime($time) {
    $now = time ();
    $old = $time;
    $date = floor ( ($now - $old) / 86400 );
    $hour = floor ( ($now - $old) % 86400 / 3600 );
    $minute = floor ( ($now - $old) % 86400 % 3600 / 60 );
    $second = floor ( ($now - $old) % 86400 % 60 );
    // echo "天" . $date . "时" . $hour . "分" . $minute . '秒' . $second . '<br />';
    // $timeArr = array('day'=>$date,'hour'=>$hour,'minute'=>$minute,'second'=>$second);
    if ($date != 0) {
        return $date . '天以前';
    } else if ($hour != 0) {
        return $hour . '小时以前';
    } else if ($minute != 0) {
        return $minute . '分钟以前';
    } else {
        return $second . '秒以前';
    }
}
/**
 * 递归删除目录及所包含文件函数
 * @param string $dir Directory name
 * @param boolean $deleteRootToo Delete specified top-level directory as well
 */
function unlinkRecursive($dir, $deleteRootToo=false)
{
    if(!$dh = @opendir($dir))
    {
        return false;
    }
    while (false !== ($obj = readdir($dh)))
    {
        if($obj == '.' || $obj == '..')
        {
            continue;
        }

        if (!@unlink($dir . '/' . $obj))
        {
            unlinkRecursive($dir.'/'.$obj, true);
        }
    }

    closedir($dh);

    if ($deleteRootToo)
    {
        @rmdir($dir);
    }

    return true;
}

/**
 * 获取字符串的长度
 *
 * 计算时, 汉字或全角字符占1个长度, 英文字符占0.5个长度
 *
 * @param string  $str
 * @return int 字符串的长度
 */
function getStrLenth($str){
    return (strlen($str) + mb_strlen($str, 'UTF8')) / 4;
}

/**
 * 原格式输出
 */
function p(){
    $args = func_get_args();
    echo '<pre>';
    foreach($args as $v){
        if(empty($v)){
            var_dump($v);
        }else{
            print_r($v);
        }
        echo "\n\n";
    }
    echo '</pre>';
}

/**
 * html2Text函数用于过滤标签，输出没有html的干净的文本
 * @param string text 文本内容
 * @return string 处理后内容
 */
function html2Text($text){
    $text = nl2br($text);
    $text = htmlspecialchars($text);
    $text = trim($text);
    return $text;
}

/**
 * h函数用于过滤不安全的html标签，输出安全的html
 * @param string $text 待过滤的字符串
 * @param string $type 保留的标签格式
 * @return string 处理后内容
 */
function safetyHtml($text, $type = 'html'){
    // 无标签格式
    $text_tags  = '';
    //只保留链接
    $link_tags  = '<a>';
    //只保留图片
    $image_tags = '<img>';
    //只存在字体样式
    $font_tags  = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    //标题摘要基本格式
    $base_tags  = $font_tags.'<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
    //兼容Form格式
    $form_tags  = $base_tags.'<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    //内容等允许HTML的格式
    $html_tags  = $base_tags.'<meta><ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    //专题等全HTML格式
    $all_tags   = $form_tags.$html_tags.'<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    //过滤标签
    $text = real_strip_tags($text, ${$type.'_tags'});
    // 过滤攻击代码
    if($type != 'all') {
        // 过滤危险的属性，如：过滤on事件lang js
        while(preg_match('/(<[^><]+)(allowscriptaccess|ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background|codebase|dynsrc|lowsrc)([^><]*)/i',$text,$mat)){
            $text = str_ireplace($mat[0], $mat[1].$mat[3], $text);
        }
        while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
            $text = str_ireplace($mat[0], $mat[1].$mat[3], $text);
        }
    }
    return $text;
}
function real_strip_tags($str, $allowable_tags="") {
    $str = html_entity_decode($str,ENT_QUOTES,'UTF-8');
    return strip_tags($str, $allowable_tags);
}
/**
 * 获取站点唯一密钥，用于区分同域名下的多个站点
 * @return string
 */
function getSiteKey(){
    return md5(C('SECURE_CODE').C('COOKIE_PREFIX'));
}
//来自于Ucenter
function tsauthcode($string, $operation = 'DECODE', $key = '') {
    $ckey_length = 4;
    $key = md5($key ? $key : SITE_URL);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}

/**
 * 用于读取/设置语言配置
 * @param string name 配置名称
 * @param string value 值
 * @return mixed 配置值|设置状态
 */
function lang($key,$data = array()){
    $key = strtoupper($key);
    if(!isset($GLOBALS['_lang'][$key])){
        if(C('APP_DEBUG')){
            $notValveForKey = F('notValveForKey', '', DATA_PATH.'/develop');
            if($notValveForKey==false){
                $notValveForKey = array();
            }
            if(!isset($notValveForKey[$key])){
                $notValveForKey[$key] = '?MODULE='.MODULE_NAME.'&CONTROLLER='.CONTROLLER_NAME.'&act='.ACTION_NAME;
            }
            F('notValveForKey', $notValveForKey, DATA_PATH.'/develop');
        }
        return $key;
    }
    if(empty($data)){
        return $GLOBALS['_lang'][$key];
    }
    $replace = array_keys($data);
    foreach($replace as &$v){
        $v = '{'.$v.'}';
    }
    return str_replace($replace,$data,$GLOBALS['_lang'][$key]);
}