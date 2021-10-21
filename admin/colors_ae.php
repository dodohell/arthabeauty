<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["colors"];
	
	$php_self = "colors.php";
	$php_edit = "colors_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
    
	$colors = new Colors();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$colors->addEditRow($params);
		header("Location: colors.php");
		die();
	}
    
	if ( $id ){
		$row = $colors->getRecord($id);
		$sm->assign("row", $row);
	}
    
	if( $params->has("SaveAndStay") ){
		$id = $colors->addEditRow($params);
        header("Location: colors_ae.php?id={$id}&act=edit");
		die();
	}
    
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$colors->deleteField($id, $field);
		header("Location: colors_ae.php?id=$id&act=edit");
		die();
	}
    
//	$editor = $settings->generateEditor($languages,"description", $row);
//	$sm->assign("editor", $editor);
	
    $brands = Brands::getBrandsAll();
    $sm->assign("brands", $brands);
    
	$sm->display("admin/colors_ae.html");
?>