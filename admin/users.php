<?php
	include("globals.php");
	
	//$settingsObj->checkLogin();
	
	$id = (int)$_REQUEST["id"];
	$page = (int)$_REQUEST["page"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
	$page_heading = $configVars["users"];
	
	$php_self = "users.php";
	$php_edit = "users_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
	
	$limit = 20;
	
	
	$users = new Users();
	
	if(isset($_REQUEST["statistics"]) && $_REQUEST["statistics"] == 1){
		$options['id'] = $id;
		$options['dateFrom'] = isset($_REQUEST["dateFrom"]) ? $_REQUEST["dateFrom"] : date('m/d/Y', strtotime("-6 months"));
		$options['dateTo'] = isset($_REQUEST["dateTo"]) ? $_REQUEST["dateTo"] : date('m/d/Y');
		
		$user = $users->getRecord($id);
		$requests = $users->getUserStatistics($options);
		
		$sm->assign('from', $options['dateFrom']);
		$sm->assign('to', $options['dateTo']);
		$sm->assign("user", $user);
		$sm->assign("requests", $requests);
		$sm->display("admin/user_statistics.html");
		die();
	}
	
	if ( $act == "login_like_user" ){
		$_SESSION["user"] = $users->getRecord($id);
		header("Location: ".$host."myprofile");
		die();
	}
	if ( $act == "delete" ){
		$users->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$search_string = $_REQUEST["search_string"];
	$sortby = (int)$_REQUEST["sortby"];
	$items = $users->getUsers($page, $limit, $search_string, $sortby);
	$pagination = $users->getUsersPagination();
	
	$sm->assign("sortby", $sortby);
	$sm->assign("items", $items);
	$sm->assign("pagination", $pagination);
	$sm->assign("search_string", $search_string);
	
	
	
	$sm->assign("tables", (int)$_REQUEST["tables"]);
	$sm->assign("guests", (int)$_REQUEST["guests"]);
	$sm->assign("budget", (int)$_REQUEST["budget"]);
	$sm->assign("planner", (int)$_REQUEST["planner"]);
	
	
	$sm->display("admin/users.html");
?>