<?php
	include("globals.php");
	include("./classes/class.econt.php");
	
	$region_id = (int)$_REQUEST["region_id"];
	
	if ( $region_id ){
		$row = $db->getRow("SELECT id, name_{$lng} AS name FROM ".$districts_table." WHERE id = '".$region_id."'"); safeCheck($row);
	}
	
	
	$econtDelivery = new econt($econt_user, $econt_pass);
	
	$regions_tmp = $econtDelivery->getRegions();
	
	
	foreach($regions_tmp as $k => $v){
		if ( trim($v) == trim($row["name"]) ){
			$region_name = $v;
		}
	}
	
	echo $region_name;
?>