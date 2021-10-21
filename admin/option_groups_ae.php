<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["option_groups"];
	
	$php_self = "option_groups.php";
	$php_edit = "option_groups_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);

	$option_groups = new OptionGroups();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$option_groups->addEditRow($params);
		header("Location: option_groups.php");
		die();
	}
    if( $params->has("SaveAndStay") ){
		$option_groups->addEditRow($params);
		header("Location: option_groups_ae.php?id=$id&act=edit");
		die();
	}
	if ( $id ){
		$row = $option_groups->getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$option_groups->deleteField($id, $field);
		header("Location: option_groups_ae.php?id=$id&act=edit");
		die();
	}
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
	
	$sm->display("admin/option_groups_ae.html");
?>