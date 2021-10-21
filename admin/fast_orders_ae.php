<?php
	include("globals.php");
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["fast_orders"];
	
	$php_self = "fast_orders.php";
	$php_edit = "fast_orders_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
    
	$fast_orders = new FastOrders();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$fast_orders->addEditRow($params);
		header("Location: ".$php_self);
		die();
	}
    
    if( $params->has("SaveAndStay") ){
		$id = $fast_orders->addEditRow($params);
		header("Location: ".$php_edit."?id=$id&act=edit");
		die();
	}
    
	if ( $id ){
		$row = FastOrders::getRecord($id);
		//$row["publishdate_value"] = date("m/d/Y", $row["publishdate"]);
		$product = FastOrders::getCartProduct($row["product_id"]);
		$sm->assign("product", $product);
	}
	$sm->assign("row", $row);
    
	$sm->display("admin/fast_orders_ae.html");
?>