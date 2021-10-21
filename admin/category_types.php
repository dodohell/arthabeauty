<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = (int)$_REQUEST["id"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
	$page_heading = $configVars["category_types"];
	
	$php_self = "category_types.php";
	$php_edit = "category_types_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$category_types = new CategoryTypes();
	
	if ( $act == "delete" ){
		$category_types->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $category_types->getCategoryTypes($menu_pos);
	$sm->assign("items", $items);
	
	
	
	
	$sm->display("admin/category_types.html");
?>