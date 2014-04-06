<?php
/*
*@Description:链接数据库配置信息
*
*
*/
	define("HOST","localhost");
	define("USER","root");
	define("PASS","");
	define("DBNAME","test");
	$link = mysql_connect(HOST,USER,PASS) or die('can not connect to the mysql'.mysql_error());
	$db = mysql_select_db(DBNAME) or die('can not connect to the database');
?>