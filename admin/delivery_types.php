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
	$sm->assign("page_heading", $page_heading);

	if ( $act == "delete" ){
		DeliveryTypes::deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}

	$items = DeliveryTypes::getDeliveryTypes();
	$sm->assign("items", $items);

	$sm->display("admin/delivery_types.html");
?>