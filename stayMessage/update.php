<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>修改留言板信息</title>
</head>

<body>
	<?php 
		$content= $_GET['content'];
		$contentArray = explode(' ', $content);
	?>
	<form action="action.php?method=stayMessage&do=update" method='post'>
		昵称:<input type='text' name='username' value="<? echo $contentArray['0']; ?>"/>
		<br/>
		内容:<textarea rows='10' cols='80' name='text'><? echo $contentArray['2']; ?></textarea>
		<br/>
		<input type="hidden" name='lastContent' value="<?php echo $content; ?>" />
		<input type='submit' name='submit' id='submit' value='修改' />
	</form>
</body>
</html>