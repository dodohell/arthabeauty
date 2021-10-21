<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["currencies"];
	
	$php_self = "currencies.php";
	$php_edit = "currencies_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$currencies = new Currencies();
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$currencies->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$currencies->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $currencies->getCurrencies();
	$sm->assign("items", $items);
    
	$sm->display("admin/currencies.html");
?>