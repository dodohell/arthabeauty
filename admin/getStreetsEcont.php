<?php
	include("globals.php");
	include("./classes/class.econt.php");
	
	$cityId = $params->getInt("city_id");
	
	$econtDelivery = new econt($econt_user, $econt_pass);
	
    $econtCityId = $db->getRow("SELECT * FROM $cities_econt_table WHERE native_id = $cityId");
    
	$streets_tmp = $econtDelivery->getStreets();
	
	foreach($streets_tmp as $k => $v){
		if ( $v["id_city"] == $econtCityId ){
			$streets[] = $v["name"];
		}
	}
    
	//sort($streets);
	echo json_encode($streets_tmp);
?>