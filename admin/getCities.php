<?php
	include("globals.php");
	
	$district_id = $params->getInt("district_id");
	
	$cities = $db->getAll("SELECT id, name_{$lng} AS name FROM ".$cities_table." WHERE edate = 0 AND district_id = '{$district_id}' ORDER BY name_{$lng}"); safeCheck($cities);
	
//	echo '<select name="city_id" class="inputField" id="city_id">';
//	foreach($cities as $k=>$v){
//		echo '<option value="'.$v["id"].'">'.$v["name"].'</option>';
//	}
//	echo '</select>';
    
    echo json_encode($cities);
?>