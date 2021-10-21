<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["attributes"];
	
	$php_self = "attributes.php";
	$php_edit = "attributes_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);

	$attributes = new Attributes();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$attributes->addEditRow($params);
		header("Location: attributes.php");
		die();
	}
    if( $params->has("SaveAndStay") ){
		$id = $attributes->addEditRow($params);
		header("Location: attributes_ae.php?id=$id&act=edit");
		die();
	}
	if ( $id ){
		$row = $attributes->getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$attributes->deleteField($id, $field);
		header("Location: attributes_ae.php?id=$id&act=edit");
		die();
	}
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
	
	$sm->display("admin/attributes_ae.html");
?>