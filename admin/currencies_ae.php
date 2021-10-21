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
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$currencies->addEditRow($params);
		header("Location: currencies.php");
		die();
	}
    
	if ( $id ){
		$row = $currencies->getRecord($id);
		$sm->assign("row", $row);
	}
    
	if( $params->has("SaveAndStay") ){
		$id = $currencies->addEditRow($params);
        header("Location: currencies_ae.php?id={$id}&act=edit");
		die();
	}
    
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$currencies->deleteField($id, $field);
		header("Location: currencies_ae.php?id=$id&act=edit");
		die();
	}
    
	$sm->display("admin/currencies_ae.html");
?>