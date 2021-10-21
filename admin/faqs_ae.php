<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = (int)$_REQUEST["id"];
	$customer_id = (int)$_REQUEST["customer_id"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$page_heading = $configVars["faqs"];
	
	$php_self = "faqs.php?customer_id=$customer_id";
	$php_edit = "faqs_ae.php?customer_id=$customer_id";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("customer_id", $customer_id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);

	$faqs = new Faqs();
	$settings = new Settings();
	
	if( isset($_REQUEST["Submit"]) ){
		$faqs->addEditRow($_REQUEST);
		header("Location: faqs.php?customer_id=$customer_id");
		die();
	}
	if ( $id ){
		$row = $faqs->getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $_REQUEST["field"];
		$faqs->deleteField($id, $field);
		header("Location: faqs_ae.php?id=$id&act=edit&customer_id=$customer_id");
		die();
	}
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$sm->display("admin/faqs_ae.html");
?>