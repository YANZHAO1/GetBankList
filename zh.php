<?php
	header("Content-Type: text/html; charset=utf-8");
	//隐藏警告信息
	ini_set("display_errors", 0);
	error_reporting(E_ALL ^ E_NOTICE);
	error_reporting(E_ALL ^ E_WARNING);

	link_db();
	for($i = 1;$i<6175;$i++){
		echo 'this '.$i." page \n";
		$res[$i] = get_content($i);
	}
	var_dump($res);
	
	//获得数据
	function get_content($page){
		$ch = curl_init(); 
		//echo $page;exit;
		$url = "http://furhr.com/?page=".$page;
		// set url 
		curl_setopt($ch, CURLOPT_URL, $url); 

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$output = curl_exec($ch); 
		curl_close($ch);
		//$s = file_get_contents($output);
		preg_match_all('/<table[^>]*>(.*)<\/table>/is', $output, $arMatch);	
		//var_export($arMatch);
		$table_data = $arMatch[0][0];
		//var_dump($table_data);
		preg_match_all('/<tr[^>]*>(.*)<\/tr>/',$table_data,$match);  	
		//var_dump($match);
		//$tr_data = $match[0];
		$tr_data = $match[1];
		for($i=1;$i<count($tr_data);$i++){  
			$data[$i] = explode('</td>',$tr_data[$i]);
			//var_dump($data);exit;
			for($j = 0;$j<count($data[$i]);$j++){ 
				//var_dump($data[$i][$j]);exit;
				if(!empty($data[$i][$j])){
					$data[$i][$j] = preg_replace('/\s(?=\s)/','',trim(strip_tags($data[$i][$j]))); 
				}else{
					unset($data[$i][$j]);
					//var_dump($data[$i]);exit;
				}
			}  
			$res = save_db($data[$i]);
			//print_r($res);exit;
		}
		$res = $data;
		unset($data);
		return $res;
	}
	
	
	
	//数据入库	
	function save_db($data){
          
        //构造一个SQL
        $query = "INSERT INTO lhh(id,h_id,h_name,h_phone,h_adder) VALUE('$data[0]', '$data[1]', '$data[2]','$data[3]','$data[4]')";  
          
        //执行该查询  
        $result = mysql_query($query) or die("Error in query: $query. ".mysql_error()); 
		 //关闭当前数据库连接  
        //mysql_close($connection);  
		return $result;
	}
	
	//链接数据库
	function link_db(){
		//数据库连接参数  
		$host = "localhost";  
		$user = "root";  
		$pass = "root";  
		$db = "test";
		//打开数据库连接  何问起
		$connection = mysql_connect($host, $user, $pass) or die("Unable to connect!");  
		mysql_query('SET NAMES UTF8');
		  
		//选择数据库  
		mysql_select_db($db) or die("Unable to select database!");  
	}
?>