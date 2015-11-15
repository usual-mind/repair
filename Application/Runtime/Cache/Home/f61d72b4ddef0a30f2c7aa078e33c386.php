<?php if (!defined('THINK_PATH')) exit();?>    <!DOCTYPE HTML>
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
    <?php if(($headTitle) != ""): ?><header class="header-top bs2">
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
    <div class="cue-text">显示一个消息</div>
</div>
    <link rel="stylesheet" type="text/css" href="<?php echo ($APP_PUBLIC_URL); ?>/css/regisiter.css">
        <label class="label">问题描述</label>
        <section class="repair-body">
            <aside class="text">
            	<textarea placeholder="说点什么吧..."></textarea>
                <div class="upload">
					<iframe src="<?php echo ($APP_PUBLIC_URL); ?>img/icon.png" scrolling="no"></iframe>
                    <a href=""><input type="file" accept="image/*"/></a>
                </div>
            </aside>
            <aside class="form">
        		<label class="label">添加/选择电脑型号</label>
                <ul class="model">
                	<li>联想/E30C</li>
                    <li class="selected">东芝/E30C</li>
                    
                    <div><a href="#">添加</a></div> 
                </ul>
            </aside>
        </section>
       	<div class="submit"><a href="#" disabled >提交</a></div>
        <footer class="foot-nav">
        	<ul>
            	<li>
                	<a href="#" data-href="record.html"><i></i><span>维修</span></a>
                </li>
            	<li>
                	<a href="#"><i></i><span>首页</span></a>
                </li>
            	<li>
                	<a href="#"><i></i><span>我</span></a>
                </li>
            </ul>
        </footer>
    </body>
    <script type="text/javascript" src="<?php echo ($APP_PUBLIC_URL); ?>js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="<?php echo ($APP_PUBLIC_URL); ?>js/function.js"></script>
</html>
    <script type="text/javascript">
    	$(function (){
			$(".foot-nav li>a").click(function (){
				var parent = $(this).parent();
				if(parent.hasClass("selected")){
					parent.removeClass("selected");
				}else{
					parent.addClass("selected");
				}
			});
		});
    </script>