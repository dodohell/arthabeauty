<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["brands"];
	
	$php_self = "brands.php";
	$php_edit = "brands_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
    
	$brands = new Brands();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$brands->addEditRow($params);
		header("Location: brands.php");
		die();
	}
    
	if ( $id ){
		$row = $brands->getRecord($id);
		$sm->assign("row", $row);
	}
    
	if( $params->has("SaveAndStay") ){
		$id = $brands->addEditRow($params);
        $row = $brands->getRecord($id);
		header("Location: brands_ae.php?id=$id&act=edit");
		die();
	}
    
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$brands->deleteField($id, $field);
		header("Location: brands_ae.php?id=$id&act=edit");
		die();
	}
    
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
    $manifacturers = Manifacturers::getManifacturersAll();
    $sm->assign("manifacturers", $manifacturers);
    
	$sm->display("admin/brands_ae.html");
?>