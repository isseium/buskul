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

	$startpoint = 241;
	$endpoint = 619;
	$url = "http://gps.iwatebus.or.jp/bls/pc/busiti_jk.jsp?jjg=1&"
		."jtr=".$startpoint //乗車
		."&kjg=2&"
		."ktr=".$endpoint //降車
		."&don=1";

	$url = "http://172.16.134.92/~ryousuke/APPfog/busapp/2/%E3%83%8F%E3%82%99%E3%82%B9%E4%BD%8D%E7%BD%AE.html?jjg=1&"
		."jtr=".$startpoint //乗車
		."&kjg=2&"
		."ktr=".$endpoint //降車
		."&don=1";

    //$url = "http://gps.iwatebus.or.jp/bls/pc/busiti_jk.jsp?jjg=1&jtr=241&kjg=2&ktr=619&don=1";
	//$url = "http://172.16.134.92/~ryousuke/APPfog/busapp/2/%E3%83%8F%E3%82%99%E3%82%B9%E4%BD%8D%E7%BD%AE.html";
	//$url = "http://gps.iwatebus.or.jp/bls/pc/busiti_jk.jsp?jjg=1&jtr=143&kjg=3&ktr=241&don=1";
    // connect to a URL
    if (!$wsql->connect('url', $url)){
        print 'Error while connecting: ' . $wsql->error;
        exit;
    }
    if (!$wsql->query('SELECT * FROM tr WHERE $class == "busstop"' )){
        print "Query error: " . $wsql->error; 
        exit;
    }
	
	if (!$wsql2->connect('url', $url)){
        print 'Error while connecting: ' . $wsql2->error;
        exit;
    }
	if (!$wsql2->query('SELECT * FROM td WHERE $class == "ridebusstop"' )){
        print "Query error: " . $wsql2->error; 
        exit;
    }
	
	foreach($wsql->fetch_array() as $row){
    	//echo $row["text"];
		//echo "<br />";
		if(strstr($row["text"], '<img src=')){
			$result = preg_split("/&nbsp;/",$row["text"]); //分割	
			preg_match("/[0-9]/",$result[1],$match); //"４つ前" とかから数字だけ取り出す
			break;
		}
    }
	
	if(!empty($match[0])){//バスまちがあれば (1〜8)
		$respons = "<remaining>".$match[0]."</remaining>"; //バスまちの数を返却
	}else{//バスまちがない = データなし(9以上のまち) or 到着(0まち)
		foreach($wsql2->fetch_array() as $row){
    		//echo $row["text"];
			//echo "<br />";
			$respons = "<remaining>NULL</remaining>";//バスデータなし
			if(strstr($row["text"], '<img src=')){
				$respons = "<remaining>". 0 ."</remaining>"; //0のバスまち =到着
				break;
			}
    	}
	}
	
	header("Content-Type: text/json");
	$xml = '<?xml version="1.0" encoding="utf-8"?><Response>'.
	$respons.
	'</Response>';
	//echo $xml;
	$data = simplexml_load_string($xml);
	$json = json_encode($data);
	echo $json;
?>