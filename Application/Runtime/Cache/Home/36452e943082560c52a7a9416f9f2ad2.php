<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if(($_title) != ""): echo ($_title); ?> - <?php echo ($site['site_name']); else: echo ($site["site_name"]); endif; ?></title>
    <meta content="<?php if(($_keywords) != ""): echo ($_keywords); else: echo ($site['keywords']); endif; ?>" name="keywords" />
    <meta content="<?php if(($_description) != ""): echo ($_description); else: echo ($site['description']); endif; ?>" name="description" />
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1,minimum-scale=1,maximum-scale=1, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="stylesheet" type="text/css" href="<?php echo ($APP_PUBLIC_URL); ?>css/comm.css">
</head>
<body>
    <!--header开始-->
    <?php if(!empty($headTitle)): ?><header class="header-top bs2">
            <a href="#" data-href="details.html"  class="hidden-text" title="后退">后退</a>
            <h1><?php echo ($headTitle); ?></h1>
        </header><?php endif; ?>
    <!--header开始-->
<!--提示框-->
<div class="messageBox" id="messageBox">
    <div class="mask">
    </div>
    <section class="bs1">
        <p id="messageBoxText">测试</p>
        <ul>
            <li id="messageBoxOkBtn">确定</li>
            <li id="messageBoxCancelBtn">取消</li>
        </ul>
    </section>
</div>
<!--消息框-->
<div id="tipBox" class="cue-box">
    <div class="cue-text bs1">显示一个消息</div>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo ($APP_PUBLIC_URL); ?>css/step2.css">
<!--header开始-->
<header class="header-top bs2">
    <a href="#" data-href="details.html"  class="hidden-text" title="后退">后退</a>
    <h1>完善个人信息</h1>
</header>
<div class="tip">检测到你第一次使用e8阳光维修服务，请完善你的个人信息。</div>
<form>
    <div class="personal-info">
        <input type="text" placeholder="输入你的姓名" />
        <input type="number" placeholder="输入你的联系方式" />
    </div>
    <label class="label">选择班级</label>
    <div class="sel-class">
        <p>设置班级</p>
        <a class="right-icon" href="#"></a>
    </div>
</form>
<div class="submit"><a href="#" disabled="">下一步</a></div>
</body>
    <script type="text/javascript" src="<?php echo ($APP_PUBLIC_URL); ?>js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="<?php echo ($APP_PUBLIC_URL); ?>js/function.js"></script>
</html>