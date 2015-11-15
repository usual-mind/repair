<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="<?php echo ($APP_PUBLIC_URL); ?>css/comm.css">
<link rel="stylesheet" type="text/css" href="<?php echo ($APP_PUBLIC_URL); ?>css/selectclass.css">
<!--header开始-->
<header class="header-top bs2">
    <a href="#" class="hidden-text" title="后退">后退</a>
    <h1>选择班级</h1>
</header>
<section class="secelt-body">
    <label class="label" id="currClassInfo"><!----计算机学院/15网络工程2班---></label>
    <ul id="classInfoBody">
        <?php echo W('SetClass/classesInfo',$classInfoWidgetparam);?>
    </ul>
</section>
    </body>
    <script type="text/javascript" src="<?php echo ($APP_PUBLIC_URL); ?>js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="<?php echo ($APP_PUBLIC_URL); ?>js/function.js"></script>
</html>

<script type="text/javascript">
    //班级信息的UL
    var JclassInfoBody = $('#classInfoBody');
    //ajax返回班级信息
    var classInfo;
    //当前班级
    var JcurrClassInfo = $('#currClassInfo');
    //当前Li
    var JcurrLi;
    $(function(){
        clickItem();
    });
    function clickItem(){
        $('#classInfoBody>li').click(function(){
            JcurrLi = $(this);
            ajaxRequest(JcurrLi.attr('data-href'),function(data){
                classInfo = data;
                //替换内容
                JclassInfoBody.fadeOut('fast',function(){
                    JcurrClassInfo.html(JcurrClassInfo.html() + ' ' + JcurrLi.children('p').html());
                    JclassInfoBody.children().remove();
                    JclassInfoBody.fadeIn('fast');
                    JclassInfoBody.append(classInfo);
                    //再次绑定点击时间
                    clickItem();
                });
            });
        });
    }
</script>