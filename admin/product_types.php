<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = (int)$_REQUEST["id"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
	$page_heading = $configVars["product_types"];
	
	$php_self = "product_types.php";
	$php_edit = "product_types_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$product_types = new ProductTypes();
	
	if ( $act == "delete" ){
		$product_types->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $product_types->getProductTypes($menu_pos);
	$sm->assign("items", $items);
	
	
	
	
	$sm->display("admin/product_types.html");
?>