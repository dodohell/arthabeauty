<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["manifacturers"];
	
	$php_self = "manifacturers.php";
	$php_edit = "manifacturers_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
    
	$manifacturers = new Manifacturers();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$manifacturers->addEditRow($params);
		header("Location: manifacturers.php");
		die();
	}
    
	if ( $id ){
		$row = $manifacturers->getRecord($id);
		$sm->assign("row", $row);
	}
    
	if( $params->has("SaveAndStay") ){
		$id = $manifacturers->addEditRow($params);
        $row = $manifacturers->getRecord($id);
		header("Location: manifacturers_ae.php?id=$id&act=edit");
		die();
	}
    
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$manifacturers->deleteField($id, $field);
		header("Location: manifacturers_ae.php?id=$id&act=edit");
		die();
	}
    
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
    $countries = Countries::getCountriesAll();
    $sm->assign("countries", $countries);
    
	$sm->display("admin/manifacturers_ae.html");
?>