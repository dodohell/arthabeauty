<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["colors"];
	
	$php_self = "colors.php";
	$php_edit = "colors_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$colors = new Colors();
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$colors->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$colors->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $colors->getColors();
	$sm->assign("items", $items);
    
	$sm->display("admin/colors.html");
?>