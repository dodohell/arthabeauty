<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["order_statuses"];
	
	$php_self = "order_statuses.php";
	$php_edit = "order_statuses_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);

	$order_statuses = new OrderStatuses();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$order_statuses->addEditRow($params);
		header("Location: order_statuses.php");
		die();
	}
    if( $params->has("SaveAndStay") ){
		$order_statuses->addEditRow($params);
		header("Location: order_statuses_ae.php?id=$id&act=edit");
		die();
	}
	if ( $id ){
		$row = $order_statuses->getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$order_statuses->deleteField($id, $field);
		header("Location: order_statuses_ae.php?id=$id&act=edit");
		die();
	}
	
	$editor = $settings->generateEditor($languages,"email_text", $row);
	$sm->assign("email_text", $editor);
	
	$sm->display("admin/order_statuses_ae.html");
?>