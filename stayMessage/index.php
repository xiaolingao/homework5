<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>留言板信息</title>
</head>

<body>
	<a href='./action.php?method=stayMessage&do=select'>查看留言</a>
	<br/>
	<br/>
	<form action='action.php?method=stayMessage&do=add' method='post'>
		<div id='content'>
			昵称:<input type='text' name='username'/>
			<br/>
			内容:<textarea rows='10' cols='80' name='text'></textarea>
			<br/>
			<input type='submit' name='submit' id='submit' value='发布' />
		</div>
	</form>
</body>
</html>
