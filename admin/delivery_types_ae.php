<?php
	include("globals.php");
	
	//$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["delivery_types"];
	
	$php_self = "delivery_types.php";
	$php_edit = "delivery_types_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		DeliveryTypes::addEditRow($params);
		header("Location: ".$php_self);
		die();
	}
    
    if( $params->has("SaveAndStay") ){
		DeliveryTypes::addEditRow($params);
		header("Location: ".$php_edit."?id=$id&act=edit");
		die();
	}
    
	if ( $id ){
		$row = DeliveryTypes::getRecord($id);
		$sm->assign("row", $row);
	}
	
	$sm->display("admin/delivery_types_ae.html");
?>