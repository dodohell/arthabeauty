<?php
	include("globals.php");
	
//	$settingsObj->checkLogin(312);
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["brands"];
	
	$php_self = "brands.php";
	$php_edit = "brands_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$brands = new Brands();
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$brands->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$brands->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $brands->getBrands();
	$sm->assign("items", $items);
    
	$sm->display("admin/brands.html");
?>