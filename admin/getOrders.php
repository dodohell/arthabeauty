<?php
	include("globals.php");
	
	$delivery_since = $params->getInt("delivery_since");

	$orders = $db->getAll("SELECT * FROM ".$carts_table." WHERE postdate >= '".$delivery_since."' AND finalised = 1");
	$fast_orders = $db->getAll("SELECT * FROM ".$fast_orders_table." WHERE postdate >= '".$delivery_since."' ");

    if ( sizeof($orders) > 0 || sizeof($fast_orders) > 0 ){
		echo 1;
	}

?>