<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
    $page = $params->getInt("page");
	
	$page_heading = $configVars["user_groups"];
	
    $limit = 20;
    
	$php_self = "user_groups.php";
	$php_edit = "user_groups_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$user_groups = new UserGroups();
	
	if ( $act == "delete" ){
		$user_groups->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $user_groups->getUserGroups($page, $limit, $params);
    $pagination = $user_groups->getUserGroupsPagination();
    
	$sm->assign("items", $items);
    $sm->assign("pagination", $pagination);
    
	$sm->display("admin/user_groups.html");
?>