<?php
	header("Content-Type: text/xml; charset=utf-8");
	$xml = '<?xml version="1.0" encoding="utf-8"?>
		<Response>';
	
	$station = array(
		array(250,"盛岡バスセンター"),
		array(77,"県庁・市役所前"),
		array(599,"盛岡城跡公園"),
		array(439,"菜園川徳前"),
		array(257,"柳新道"),
		array(45,"開運箸"),
		array(241,"盛岡駅"),
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
	//
	//var_dump($station);
	foreach ($station as $key => $value) {
		$xml .=	'<busstop><id>'. ($key+1) .'</id>
			<order_id>'. $value[0] .'</order_id>
			<name>'.$value[1].'</name>
		</busstop>';
	}

	$xml .= "</Response>";
	echo $xml;
	$data = simplexml_load_string($xml);
	$json = json_encode($data);
	//echo $json;
	/*
	 * 

	 * 
	 * 
	 * 
	 */
?>
