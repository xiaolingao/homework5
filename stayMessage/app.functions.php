<?php
//公共方法文件
function __autoload($class) {
    $class_file = './include/class.' . $class . '.php';
    if(is_file($class_file)){
    	include($class_file);
    }
}
?>