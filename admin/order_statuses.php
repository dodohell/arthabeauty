<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["order_statuses"];
	
	$php_self = "order_statuses.php";
	$php_edit = "order_statuses_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$order_statuses = new OrderStatuses();
	
	if ( $act == "delete" ){
		$order_statuses->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $order_statuses->getOrderStatuses();
    
	$sm->assign("items", $items);
	
	
	
	
	$sm->display("admin/order_statuses.html");
?>