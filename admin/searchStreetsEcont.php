<?php
	include("globals.php");
	include("./classes/class.econt.php");
	error_reporting(0);
	$cityId = $params->getInt("city_id");
	
	$econtDelivery = new econt($econt_user, $econt_pass);
	
    $econtCityId = $db->getRow("SELECT * FROM $cities_econt_table WHERE native_id = $cityId");
    
	$streets_tmp = $econtDelivery->getStreets();
	
	foreach($streets_tmp as $k => $v){
		if ( $v["id_city"] == $econtCityId["id"] ){
			$streets[] = $v["name"];
		}
	}
    
	sort($streets);
	
	mb_internal_encoding('UTF-8');
	
	$term = mb_strtolower(htmlspecialchars(trim($_REQUEST["term"]), ENT_QUOTES));
	
	foreach($streets as $k => $v){
		if ( strpos(mb_strtolower($v), $term) === 0 ){
			$streets_use[] = $v;
//			$streets_use[$k]["label"] = $v;
//			$streets_use[$k]["value"] = $v;
		}
	}
	if ( sizeof($streets_use) == 0 ){
		$streets_use[0] = "Няма намерени резултати";
//		$streets_use[0]["label"] = "Няма намерени резултати";
//		$streets_use[0]["value"] = "Няма намерени резултати";
	}
	echo json_encode($streets_use);
?>