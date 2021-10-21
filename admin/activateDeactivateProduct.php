<?php
	include "globals.php";
	
	$action = $params->getString("action");
	$product_id = $params->getInt("product_id");
	
    if ( $action == "activate" ){
        $res = $db->autoExecute($products_table, array("active" => 1), DB_AUTOQUERY_UPDATE, " id = '".$product_id."' "); safeCheck($res);
    }
    if ( $action == "deactivate" ){
        $res = $db->autoExecute($products_table, array("active" => 0), DB_AUTOQUERY_UPDATE, " id = '".$product_id."' "); safeCheck($res);
    }
	
    echo $res ? 1 : 0;
?>