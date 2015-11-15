<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="<?php echo ($APP_PUBLIC_URL); ?>css/comm.css">
<link rel="stylesheet" type="text/css" href="<?php echo ($APP_PUBLIC_URL); ?>css/selectclass.css">
<!--header开始-->
<header class="header-top bs2">
    <a href="#" class="hidden-text" title="后退">后退</a>
    <h1>登陆维修记录</h1>
</header>
<section class="secelt-body">
    <label class="label">计算机学院/15网络工程2班</label>
    <ul id="classInfoBody"><?php echo W('SetClass/classesInfo',$classInfoWidgetparam);?></ul>
</section>
    </body>
    <script type="text/javascript" src="<?php echo ($APP_PUBLIC_URL); ?>js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="<?php echo ($APP_PUBLIC_URL); ?>js/function.js"></script>
</html>

<script type="text/javascript">
    $('#classInfoBody>li').click(function(){
        ajaxRequest($(this).attr('data-href'),function(data){
            alert(data);
        });
    });
</script>