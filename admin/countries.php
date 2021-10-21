<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
    $page = $params->getInt("page");
	
	$page_heading = $configVars["countries"];
	
    $limit = 20;
    
	$php_self = "countries.php";
	$php_edit = "countries_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$countries = new Countries();
	
	if ( $act == "delete" ){
		$countries->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $countries->getCountries($page, $limit, $params);
    $pagination = $countries->getCountriesPagination();
    
	$sm->assign("items", $items);
    $sm->assign("pagination", $pagination);
    
	$sm->display("admin/countries.html");
?>