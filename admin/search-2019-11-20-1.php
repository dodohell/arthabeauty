<?php
	include("globals.php");
	
	$term = htmlspecialchars(trim($_REQUEST["term"]),ENT_QUOTES);
	
	$results = $db->getAll("SELECT 
														id, name_{$lng} AS name, name_en, barcode,  
														(SELECT name_{$lng} FROM {$brands_table} WHERE id = brand_id) AS brand_name,
														(SELECT name_{$lng} FROM {$collections_table} WHERE id = collection_id) AS col_name
														
														FROM ".$products_table." 
														WHERE 
														(
															MATCH(name_{$lng}) AGAINST('".strtolower($term)."' IN BOOLEAN MODE) OR 
															MATCH(name_en) AGAINST('".strtolower($term)."' IN BOOLEAN MODE) OR 
															barcode LIKE '%".strtolower($term)."%'
														) AND edate = 0"); safeCheck($results);
	foreach( $results as $k => $v ){
		$display_results[$k]["label"] = $v["barcode"].", ".$v["name_en"].", ".$v["name"].", ".$v["brand_name"].", ".$v["col_name"];
		$display_results[$k]["value"] = $v["id"];
	}
	echo json_encode($display_results);
?>