<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["age_groups"];
	
	$php_self = "age_groups.php";
	$php_edit = "age_groups_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
    
	$age_groups = new AgeGroups();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$age_groups->addEditRow($params);
		header("Location: age_groups.php");
		die();
	}
    
	if ( $id ){
		$row = $age_groups->getRecord($id);
		$sm->assign("row", $row);
	}
    
	if( $params->has("SaveAndStay") ){
		$id = $age_groups->addEditRow($params);
        header("Location: age_groups_ae.php?id={$id}&act=edit");
		die();
	}
    
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$age_groups->deleteField($id, $field);
		header("Location: age_groups_ae.php?id=$id&act=edit");
		die();
	}
    
//	$editor = $settings->generateEditor($languages,"description", $row);
//	$sm->assign("editor", $editor);
    
	$sm->display("admin/age_groups_ae.html");
?>