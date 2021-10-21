<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["user_groups"];
	
	$php_self = "user_groups.php";
	$php_edit = "user_groups_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	$user_groups = new UserGroups();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$user_groups->addEditRow($params);
		header("Location: user_groups.php");
		die();
	}
    if( $params->has("SaveAndStay") ){
		$id = $user_groups->addEditRow($params);
		header("Location: user_groups_ae.php?id=$id&act=edit");
		die();
	}
	if ( $id ){
		$row = UserGroups::getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$user_groups->deleteField($id, $field);
		header("Location: user_groups_ae.php?id=$id&act=edit");
		die();
	}
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$sm->display("admin/user_groups_ae.html");
?>