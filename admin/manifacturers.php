<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["manifacturers"];
	
	$php_self = "manifacturers.php";
	$php_edit = "manifacturers_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$manifacturers = new Manifacturers();
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$manifacturers->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$manifacturers->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $manifacturers->getManifacturers($menu_pos);
	$sm->assign("items", $items);
    
	$sm->display("admin/manifacturers.html");
?>