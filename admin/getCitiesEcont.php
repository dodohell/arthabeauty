<?php
	include("globals.php");
	include("./classes/class.econt.php");
	
	$region = $_REQUEST["region"];
	
	$econtDelivery = new econt($econt_user, $econt_pass);
	
	$cities_tmp = $econtDelivery->getCities();
	// dbg($cities_tmp);
	foreach($cities_tmp as $k => $v){
		if ( trim($v["region"]) == trim($region) ){
			$cities[] = $v;
		}
	}
	//dbg($cities);
	// dbg($cities_tmp);
	echo json_encode($cities);
?>