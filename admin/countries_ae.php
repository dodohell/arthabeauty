<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["countries"];
	
	$php_self = "countries.php";
	$php_edit = "countries_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	$countries = new Countries();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$countries->addEditRow($params);
		header("Location: countries.php");
		die();
	}
    if( $params->has("SaveAndStay") ){
		$id = $countries->addEditRow($params);
		header("Location: countries_ae.php?id=$id&act=edit");
		die();
	}
	if ( $id ){
		$row = $countries->getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$countries->deleteField($id, $field);
		header("Location: countries_ae.php?id=$id&act=edit");
		die();
	}
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
	
	$sm->display("admin/countries_ae.html");
?>