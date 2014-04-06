<?php
	header("Content-Type:text/html;charset=utf8");
	include("app.functions.php");
	define("ISCHECKED",true);
	$method = trim($_GET['method']);
	$action = trim($_GET['do']);
	$to = new $method();
	$result = $to->$action();
	echo $result;
?>