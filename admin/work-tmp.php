<?php
	include("globals.php");
	include("./classes/class.econt.php");
	
	//$region = $_REQUEST["region"];
	
//	$econtDelivery = new econt($econt_user, $econt_pass);
//	$regions = $econtDelivery->getRegionsMoreInfo();
//	$cities_tmp = $econtDelivery->getCities();
	
//	foreach($cities_tmp as $k => $v){
//		if ( trim($v["region"]) == trim($region) ){
//			$cities[] = $v;
//		}
//	}
//    $counter = 0;
//    foreach ($cities_tmp as $k => $v){
//        $district = $db->getRow("SELECT id FROM ".$districts_table." WHERE name_bg = '".$v['region']."' AND edate = 0"); safeCheck($district);
//        $fields = [
//            //"id"            => $v["id"],
//            "district_id"   => $district["id"],
//            "city_type"     => $v["type"],
//            "postcode"      => $v["post_code"],
//            "name_bg"       => $v["name"],
//            "name_en"       => $v["name_en"],
//            "active"        => 1,
//            "pos"           => $counter,
//        ];
//        $res = $db->autoExecute($cities_table, $fields, DB_AUTOQUERY_INSERT ); safeCheck($res);
//        $counter ++;
//    }
//	var_dump($res);
//    
//    
//    $counter = 0;
//    foreach ($cities_tmp as $k => $v){
//        $district = $db->getRow("SELECT id FROM ".$districts_table." WHERE name_bg = '".$v['region']."' AND edate = 0"); safeCheck($district);
//        $districtSql = $district ? "AND district_id = ".$district["id"] : "";
//        $nativeCity = $db->getRow("SELECT id FROM ".$cities_table." WHERE name_bg = '".$v['name']."' {$districtSql} AND edate = 0"); safeCheck($district);
//        
//        $fields = [
//            "id"            => $v["id"],
//            "native_id"     => $nativeCity["id"],
//            "region_bg"     => $v["region"],
//            "region_en"     => $v["region_en"],
//            "city_type"     => $v["type"],
//            "postcode"      => $v["post_code"],
//            "name_bg"       => $v["name"],
//            "name_en"       => $v["name_en"],
//            "active"        => 1,
//            "pos"           => $counter,
//        ];
//        $res = $db->autoExecute($cities_econt_table, $fields, DB_AUTOQUERY_INSERT ); safeCheck($res);
//        $counter ++;
//    }
//
//	var_dump($res);
    
    
	//dbg($cities_tmp);
	//echo json_encode($cities);
?>