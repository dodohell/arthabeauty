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
    
  if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$collections->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$collections->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $collections->getCollections($params);
	$sm->assign("items", $items);
	
	$brands = Brands::getBrandsAll();
    $sm->assign("brands", $brands);
    $sm->assign("brand_id", $params->getInt('brand_id'));
    
	$sm->display("admin/collections.html");
?>