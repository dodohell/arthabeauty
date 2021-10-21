<?php
	include("globals.php");
	
	$id = $params->getInt("id");
	$page = $params->getInt("page");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["fast_orders"];
	
	$php_self = "fast_orders.php";
	$php_edit = "fast_orders_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
	
	$limit = 20;
	
	$fast_orders = new FastOrders();
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$fast_orders->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$fast_orders->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$search_string = $params->getString("search_string");
	$items = $fast_orders->getFastOrders($page, $limit, $search_string);
	$pagination = $fast_orders->getFastOrdersPagination();
	
	$sm->assign("items", $items);
	$sm->assign("pagination", $pagination);
	$sm->assign("search_string", $search_string);
    
    $sm->assign("time_now", time());

	$sm->display("admin/fast_orders.html");
?>