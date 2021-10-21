<?php
	include("globals.php");
	
	$term = htmlspecialchars(trim($_REQUEST["term"]),ENT_QUOTES);
	
	$results = $db->getAll("SELECT id, name_{$lng} AS name FROM ".$products_table." WHERE MATCH(name_{$lng}) AGAINST('".strtolower($term)."' IN BOOLEAN MODE) AND edate = 0"); safeCheck($results);
	foreach( $results as $k => $v ){
		$display_results[$k]["label"] = $v["name"];
		$display_results[$k]["value"] = $v["id"];
	}
	echo json_encode($display_results);
?>