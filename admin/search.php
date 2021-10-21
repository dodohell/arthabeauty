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
															name_{$lng} LIKE '%".strtolower($term)."%' OR 
															name_en LIKE '%".strtolower($term)."%' OR 
															barcode LIKE '%".strtolower($term)."'
														) AND edate = 0"); safeCheck($results);
	foreach( $results as $k => $v ){
		$display_results[$k]["label"] = $v["barcode"].", ".$v["name_en"].", ".$v["name"].", ".$v["brand_name"].", ".$v["col_name"];
		$display_results[$k]["value"] = $v["id"];
	}
	echo json_encode($display_results);
?>