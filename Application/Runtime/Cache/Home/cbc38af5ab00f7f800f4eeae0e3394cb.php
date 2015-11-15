<?php if (!defined('THINK_PATH')) exit(); if(is_array($classData)): $i = 0; $__LIST__ = $classData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="option"  data-href="<?php echo ($vo["url"]); ?>">
        <p><?php echo ($vo["title"]); ?></p>
        <a class="right-icon" href="#"></a>
    </li><?php endforeach; endif; else: echo "" ;endif; ?>