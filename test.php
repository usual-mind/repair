<?php
$str = 'my name is {name} and my age is {age}';
echo str_replace(array('{name}','{age}'),array('age'=>'18','name'=>'taoyu'),$str);