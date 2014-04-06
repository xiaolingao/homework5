<?php
	if(!defined("ISCHECKED")) die('eeee');
	date_default_timezone_set('PRC');
	class stayMessage{
		private $key = 'keyTwo';

		/*
		*@Description:添加留言的方法
		*
		*
		*/
		public function add(){
			include_once('redis.php');
			if(isset($_POST['submit'])){
				$userName = trim($_POST['username']);
				$text = trim($_POST['text']);
				if(empty($userName) || empty($text)){
					die('不能为空');
				}
				$time = time();
			}
			$add_dataArray = array('username'=>"$userName", 'time'=>"$time", 'content'=>"$text");
			$add_dataJson = json_encode($add_dataArray);
			$result = $this->delKey(3,'keyOne');
			echo $result;
			$redis->sAdd('keyOne', $add_dataJson);
			return $this->tiaoZhuan('插入数据成功！');
		}
		/*
		*@Description:查看所有留言的方法
		*
		*
		*/
		public function select(){
			include_once('databaseConfig.php');
			include_once('redis.php');
			$table = '<a href="./index.php">留言板首页</a><table id="content" border="1"><tr><td>用户名</td><td>日期</td><td>内容</td><td>操作</td></tr>';
			$select_keyOnenumber = $redis->sCard('keyOne');
			$select_keyOneDataArray = $redis->sMembers('keyOne');
			for($i=0;$i<$select_keyOnenumber;$i++){
				$select_dataOne = json_decode($select_keyOneDataArray[$i],true);
				$needToDeleteOne = $select_dataOne['username'].' '.$select_dataOne['time'].' '.$select_dataOne['content'];
				$table .= '<tr><td>'.$select_dataOne['username'].'</td><td>'.date('Y-m-d H:i:s', $select_dataOne['time']).'</td><td>'.$select_dataOne['content'].'</td>';
				$table .="<td><a href='./action.php?method=stayMessage&do=delete&content=".$needToDeleteOne."'>删除</a>&nbsp;&nbsp;<a href='./update.php?content=".$needToDeleteOne."'>修改</a></td></tr>";
			}
	//来源于memcached的使用方法
			$select_keyTwoNumber = $redis->sCard($this->key);
			if(!$select_keyTwoNumber){
				//从数据库中读取信息
				$sql = 'select username,time,content from liuyanban order by time';
				$result = mysql_query($sql);
				if(!$result){
					return 'Could not successfully run query ($sql) from DB:' . mysql_error();
				}
				if(mysql_num_rows($result) == 0){
					return 'No rows found, nothing to print so am exiting';
				}
				while($row = mysql_fetch_assoc($result)){
					$needToDeleteTwo = $row['username'].' '.$row['time'].' '.$row['content'];
					$table .= '<tr><td>'.$row['username'].'</td><td>'.date('Y-m-d H:i:s',$row['time']).'</td><td>'.$row['content'].'</td>';
					$table .="<td><a href='./action.php?method=stayMessage&do=delete&content=".$needToDeleteTwo."'>删除</a>&nbsp;&nbsp;<a href='./update.php?content=".$needToDeleteTwo."'>修改</a></td></tr>";
					$select_saddDataJson = json_encode($row);
					$redis->sAdd($this->key,$select_saddDataJson);
				}
				$table .= '</table>';
				return $table;
			}else{
				$select_keyTwoDataArray = $redis->sMembers($this->key);
				for($i=0;$i<$select_keyTwoNumber;$i++){
					$select_dataThree = json_decode($select_keyTwoDataArray[$i], true);
					$needToDeleteThree = $select_dataThree['username'].' '.$select_dataThree['time'].' '.$select_dataThree['content'];
					$table .= '<tr><td>'.$select_dataThree['username'].'</td><td>'.date('Y-m-d H:i:s',$select_dataThree['time']).'</td><td>'.$select_dataThree['content'].'</td>';
					$table .="<td><a href='./action.php?method=stayMessage&do=delete&content=".$needToDeleteThree."'>删除</a>&nbsp;&nbsp;<a href='./update.php?content=".$needToDeleteThree."'>修改</a></td></tr>";
				}
				$table .= '</table>';
				return $table;
			}
		}

		/*
		*@Description:修改一条留言的方法
		*
		*
		*/
		public function update(){
			include_once('redis.php');
			if(isset($_POST['submit'])){
				$username = trim($_POST['username']);
				$text = trim($_POST['text']);
				$content = trim($_POST['lastContent']);
				if(empty($username) || empty($text)){
					die('不能为空');
				}
				$time = time();
			}
		//删除留在redis中的内容
			$contentArray = explode(' ', $content);
			//json 编码 目的是为了和插入redis时的格式一致，以便查找
			$dataArray = array('username'=>$contentArray['0'], 'time'=>$contentArray['1'], 'content'=>$contentArray['2']);
			$dataJson = json_encode($dataArray);
			if($redis->sIsMember('keyOne',$dataJson)){
				echo '在keyOne中';
				$redis->sRemove('keyOne', $dataJson);
			}elseif($redis->sIsMember($this->key,$dataJson)){
				echo '在'.$this->key.'中';
				$redis->sRemove($this->key, $dataJson);
			}
		//将删除的内容存进keyThree中,目的是为了删除数据库中的内容
			$redis->sAdd('keyThree', $dataJson);
			$result = $this->delKey(3, 'keyThree');
			echo $result;
		//增加新内容到redis中
			$updateDataArray = array('username'=>"$username", 'time'=>"$time", 'content'=>"$text");
			$updatedData = json_encode($updateDataArray);
			
			$redis->sAdd('keyOne', $updatedData);
			return $this->tiaoZhuan('修改数据成功！');	
		}

		/*
		*@Description:删除一条留言的方法
		*
		*
		*/
		public function delete(){
			include_once('redis.php');
			//接受ip上传过来的需要被删除的数据
			$content = $_GET['content'];
			$contentArray = explode(' ', $content);
			//json 编码 目的是为了和插入redis时的格式一致，以便查找
			$data = array('username'=>$contentArray['0'], 'time'=>$contentArray['1'], 'content'=>$contentArray['2']);
			$dataJson = json_encode($data);

			//删除redis中的信息
			if($redis->sIsMember('keyOne',$dataJson)){
				$redis->sRemove('keyOne', $dataJson);
				return $this->tiaoZhuan('删除信息成功！');
			}elseif($redis->sIsMember($this->key,$dataJson)){
				$redis->sRemove($this->key, $dataJson);
			//将删除的内容存进keyThree中
				$redis->sAdd('keyThree', $dataJson);
				$result = $this->delKey(3, 'keyThree');
				echo $result;
				return $this->tiaoZhuan('删除信息成功');
			}else{
				exit;
			}
		}

		/*
		*@Description:如果value值数量超过了限制，则清空
		*
		*
		*/
		public function delKey($needToDeleteNumber, $needToDeleteKey){
			$redis = new Redis();
			$redis->connect('127.0.0.1',6379);
			$redis->select('0');
			$number = $redis->sCard($needToDeleteKey);
			if($number >= $needToDeleteNumber){
				if($needToDeleteKey == 'keyOne'){
					$rr = $this->inserting($number, $needToDeleteKey);
					$redis->del($needToDeleteKey);
				}elseif($needToDeleteKey == 'keyThree'){
					$rr = $this->deleteing($number, $needToDeleteKey);
					$redis->del($needToDeleteKey);
					$redis->del($this->key);
				}else{
					exit;
				}
				return $rr;
			}
			
		}

		/*
		*@Description:将redis中的信息存进数据库
		*
		*
		*/
		public function inserting($number, $needToDeleteKey){
			include_once('databaseConfig.php');
			$redis = new Redis();
			$redis->connect('127.0.0.1',6379);
			$redis->select('0');
			$insert_dataJsonArray = $redis->sMembers($needToDeleteKey);
			for($q=0; $q <$number; $q++){
				$insert_dataArray = json_decode($insert_dataJsonArray[$q], true);
				$sql = "insert into liuyanban(`username`,`time`,`content`) values('$insert_dataArray[username]', '$insert_dataArray[time]', '$insert_dataArray[content]');";
				$result = mysql_query($sql);
			}
			if(mysql_insert_id()>0){
				return '插入数据库成功';
			}
		}
		
		/*
		*@Description:删除数据库中的信息
		*
		*/
		public function deleteing($number, $needToDeleteKey){
			include_once('databaseConfig.php');
			$redis = new Redis();
			$redis->connect('127.0.0.1',6379);
			$redis->select('0');
			$delete_dataJsonArray = $redis->sMembers($needToDeleteKey);
			for($q=0; $q <$number; $q++){
				$delete_dataArray = json_decode($delete_dataJsonArray[$q], true);
				$sql = "delete from liuyanban where username='$delete_dataArray[username]' and time='$delete_dataArray[time]' and content='$delete_dataArray[content]';";
				$result = mysql_query($sql);
			}
			if($result){
				return '删除数据库信息成功';
			}
		}

		/*
		*@Description:自动跳转的方法
		*
		*
		*/
		public function tiaoZhuan($message){
			return "<p id='tiao' disabled='true'>".$message."!&nbsp;&nbsp;&nbsp;<span id='span'>5</span>秒后自动跳转</p>
 				<script type='text/javascript'>
 					var seconds = 0; 
 					setInterval(function(){   
 						seconds += 1;
  						document.getElementById('span').innerHTML = 5-seconds; 
  						if(seconds == 5){
  							document.getElementById('tiao').disabled = false;
  							window.location='./index.php';
  						} 
  					},1000); 
				</script>";
		}
	}
?>