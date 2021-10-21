<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
    $page = $params->getInt("page");
	
	$page_heading = $configVars["cities"];
	
    $limit = 20;
    
	$php_self = "cities.php";
	$php_edit = "cities_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$cities = new cities();
	
	if ( $act == "delete" ){
		$cities->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $cities->getCities($page, $limit, $params);
    $pagination = $cities->getCitiesPagination();
    
	$sm->assign("items", $items);
    $sm->assign("pagination", $pagination);
    
	$sm->display("admin/cities.html");
?>