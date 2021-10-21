<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["attributes"];
	
	$php_self = "attributes.php";
	$php_edit = "attributes_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$attributes = new Attributes();
	
	if ( $act == "delete" ){
		$attributes->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $attributes->getAttributes($menu_pos);
	$sm->assign("items", $items);
	
	
	
	
	$sm->display("admin/attributes.html");
?>