<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = (int)$_REQUEST["id"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
	$page_heading = $configVars["categories"];
	
	$php_self = "categories.php";
	$php_edit = "categories_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$categories = new Categories();
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$categories->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$categories->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
    
	$items = $categories->getCategoriesList();
	$sm->assign("items", $items);
	
	$sm->display("admin/categories.html");
?>