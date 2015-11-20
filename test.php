<?php
$_reg = '/(\d{3})(\d{4})(\d{4})/';
$m = array();
print_r(preg_match($_reg,'13956460801',$m));
echo "<br/>";
print_r($m);