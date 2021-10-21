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
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$age_groups->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$age_groups->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $age_groups->getAgeGroups();
	$sm->assign("items", $items);
    
	$sm->display("admin/age_groups.html");
?>