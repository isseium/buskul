<?php

    /*
    ** htmlSQL - Example 1
    **
    ** Shows a simple query
    */
    
    include_once("htmlSQL/snoopy.class.php");
    include_once("htmlSQL/htmlsql.class.php");
    
    $wsql = new htmlsql();
	$wsql2 = new htmlsql();

	$station = array(
		array(250,"盛岡バスセンター"),
		array(77,"県庁・市役所前"),
		array(599,"盛岡城跡公園"),
		array(439,"菜園川徳前"),
		array(257,"柳新道"),
		array(45,"開運箸"),
		array(241,"盛岡駅前"),
		array(9,"旭橋"),
		array(263,"夕顔瀬橋"),
		array(48,"片原"),
		array(143,"館坂橋"),
		array(336,"安倍館"),
		array(337,"上堂"),
		array(338,"上堂二丁目"),
		array(34,"氏子橋"),
		array(53,"上堂四丁目"),
		array(37,"運動公園口"),
		array(68,"厨川一丁目"),
		array(177,"農業研究センター"),
		array(58,"北厨川小学校前"),
		array(28,"岩手牧場前"),
		array(47,"果樹研究所前"),
		array(111,"森林総合研究所前"),
		array(112,"巣子"),
		array(609,"富士見団地入口"),
		array(610,"富士見団地"),
		array(611,"中巣子"),
		array(612,"勤労青少年ホーム前"),
		array(613,"榛沢"),
		array(1016,"第三富士見団地口"),
		array(615,"巣子駅入口"),
		array(616,"巣子車庫"),
		array(617,"滝沢東小学校前"),
		array(618,"岩手県立大学入口"),
		array(619,"岩手県立大学")
		);

	//$startpoint = 177;// = 盛岡駅
	//$endpoint = 619;// = 岩手県立大学	
	$startpoint = $_GET['startpoint']; //241 = 盛岡駅
	$endpoint = $_GET['endpoint']; //619 = 岩手県立大学
	$url = "http://gps.iwatebus.or.jp/bls/pc/busiti_jk.jsp?jjg=1&"
		."jtr=".$startpoint //乗車
		."&kjg=2&"
		."ktr=".$endpoint //降車
		."&don=1";
	/*
	$startpoint = 177;// = 盛岡駅
	$endpoint = 619;// = 岩手県立大学
	$url = "http://172.16.134.92/~ryousuke/APPfog/busapp/2/%E3%83%8F%E3%82%99%E3%82%B9%E4%BD%8D%E7%BD%AE.html?jjg=1&"
		."jtr=".$startpoint //乗車
		."&kjg=2&"
		."ktr=".$endpoint //降車
		."&don=1";
	*/
	if(empty($startpoint) || empty($endpoint)){ //ゲットで値がない場合
		$respons = "<remaining>NULL</remaining>";//バスデータなし
	}
	
    // connect to a URL
    if (!$wsql->connect('url', $url)){
        print 'Error while connecting: ' . $wsql->error;
        exit;
    }
    if (!$wsql->query('SELECT * FROM tr WHERE $class == "busstop"' )){
        print "Query error: " . $wsql->error; 
        exit;
    }
	//var_dump($wsql);
	if (!$wsql2->connect('url', $url)){
        print 'Error while connecting: ' . $wsql2->error;
        exit;
    }
	if (!$wsql2->query('SELECT * FROM td WHERE $class == "ridebusstop"' )){
        print "Query error: " . $wsql2->error; 
        exit;
    }
	
	//到着した時の判定
	foreach($wsql2->fetch_array() as $row){
    	//echo $row["text"];
		//echo "<br />";
		
		$bus[0]['flag'] = 0;
		$bus[0]['id'] = $startpoint;
		//乗車地点をさがす。
		foreach ($station as $key => $value) {
			if($value[0] == $startpoint){//idが一致する
				$bus[0]['name'] = $value[1];
				for ($i=$key; $i >= 0; $i--) { 
					$tmp[$i]['id'] = $station[$i][0]; //id
					$tmp[$i]['name'] = $station[$i][1]; //name
				}
			}
		}
		if(strstr($row["text"], '<img src=')){
			$bus[0]['flag'] = 1;
			break;
		}
    }
	//krsort($tmp);
	/*
	foreach ($tmp as $key => $value) {
		echo $key;
		echo "/";
		echo $value['flag'];
		echo ":";
		echo $value['id'];
				echo ":";
		echo $value['name'];
		echo "<br />";		
	}*/
	$n = count($tmp) -2;
	$l = $n - 6;
	/*echo $n;
	echo ":";
	echo $l;
	echo "<hr>";
	*/
	//一つ前のバス停までの判定
	$limit = count($wsql->fetch_array());
	foreach($wsql->fetch_array() as $row){
    	//echo $row["text"];
		//echo "<br />";
		if(strstr($row["text"], '<img src=')){
			$result = preg_split("/&nbsp;/",$row["text"]); //分割	
			preg_match("/[0-9]/",$result[1],$match); //"４つ前" とかから数字だけ取り出す
			$bus[$limit]['flag'] = 1;
		}else{
			$bus[$limit]['flag'] = 0;
		}

		//バス停の名前をidを取得
		foreach ($station as $key => $value) {
			if($value[0] == $startpoint){
				$bus[0]['name'] = $value[1];
			}
		}
		$bus[$limit]['id'] = $tmp[$l]['id'];
		$bus[$limit]['name'] = $tmp[$l]['name'];
		$l++;
		$limit--;
    }
	
	krsort($bus);

	
	
	//var_dump($bus);
	/*
	foreach ($bus as $key => $value){
		echo $key;
		echo "/";
		echo $value['flag'];
		echo ":";
		echo $value['id'];
				echo ":";
		echo $value['name'];
		echo "<br />";		
	}*/
	// 例) array(8) { [7]=> int(0) [6]=> int(1) [5]=> int(0) [4]=> int(0) [3]=> int(0) [2]=> int(1) [1]=> int(0) [0]=> int(0) } 
	//一番近い場所の情報を持ってくる
	$nowpoint = 100; //データなしフラグ
	foreach ($bus as $key => $value){
		if($value['flag'] == 1){ //バスがいるバス停を探す
			$nowpoint = $key; //なんこ待ちか
			$nowid = $value['id']; //今バスがいるバス停id
			$nowname = $value['name']; //今バスがいるバス停名
		}else{ //バスがいないときはスキップ
			continue;
		}
	}
	if($nowpoint == 100){ //全てのバス停にいない
		$respons = "<remaining>NULL</remaining>";//バスデータなし
		$responsid = null;
		$responsname = null;
	}else{
		$respons = "<remaining>".$nowpoint."</remaining>";
		$responsid = "<id>".$nowid."</id>";
		$responsname = "<name>".$nowname."</name>";
	}
	
	//header("Content-Type: text/json");
	$xml = '<?xml version="1.0" encoding="utf-8"?><Response><busstop>'.
	$respons.
	$responsid.
	$responsname.
	'</busstop></Response>';
	//echo $xml;
	$data = simplexml_load_string($xml);
	$json = json_encode($data);
	echo $json;
?>