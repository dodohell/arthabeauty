<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["collections"];
	
	$php_self = "collections.php";
	$php_edit = "collections_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
    
	$collections = new Collections();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$collections->addEditRow($params);
		header("Location: collections.php");
		die();
	}
    
	if ( $id ){
		$row = $collections->getRecord($id);
		$sm->assign("row", $row);
	}
    
	if( $params->has("SaveAndStay") ){
		$id = $collections->addEditRow($params);
        //$row = $collections->getRecord($id);
        header("Location: collections_ae.php?id={$id}&act=edit");
		die();
	}
    
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$collections->deleteField($id, $field);
		header("Location: collections_ae.php?id=$id&act=edit");
		die();
	}
    
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
    $brands = Brands::getBrandsAll();
    $sm->assign("brands", $brands);
    
	$sm->display("admin/collections_ae.html");
?>