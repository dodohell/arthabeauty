<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["option_groups"];
	
	$php_self = "option_groups.php";
	$php_edit = "option_groups_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$option_groups = new OptionGroups();
	
	if ( $act == "delete" ){
		$option_groups->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $option_groups->getOptionGroups($menu_pos);
	$sm->assign("items", $items);
	
	
	
	
	$sm->display("admin/option_groups.html");
?>