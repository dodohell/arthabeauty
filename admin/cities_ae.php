<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["cities"];
	
	$php_self = "cities.php";
	$php_edit = "cities_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	$cities = new cities();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$cities->addEditRow($params);
		header("Location: cities.php");
		die();
	}
    if( $params->has("SaveAndStay") ){
		$cities->addEditRow($params);
		header("Location: cities_ae.php?id=$id&act=edit");
		die();
	}
	if ( $id ){
		$row = $cities->getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$cities->deleteField($id, $field);
		header("Location: cities_ae.php?id=$id&act=edit");
		die();
	}
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
	
	$sm->display("admin/cities_ae.html");
?>