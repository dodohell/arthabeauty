<?php
	include("globals.php");
	include("./classes/class.econt.php");
    
	$cityId = $params->getInt("city_id");
    
	$econtDelivery = new econt($econt_user, $econt_pass);
    
    $econtCityId = $db->getRow("SELECT * FROM $cities_econt_table WHERE native_id = '".$cityId."'")["id"];
	
	$offices_tmp = $econtDelivery->getOffices();

	foreach($offices_tmp as $k => $v){
		if ( trim($v["id_city"]) == $econtCityId ){
			if ( trim($v["name"]) ){
				$offices[] = $v;
			}
		}
	}
	//$city_id_test["region_name"] = $region_name;
    echo json_encode($offices);
	
?>