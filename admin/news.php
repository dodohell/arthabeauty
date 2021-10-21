<?php
	include("globals.php");
	
	$id = (int)$_REQUEST["id"];
	$page = (int)$_REQUEST["page"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
	$page_heading = $configVars["news"];
	
	$php_self = "news.php";
	$php_edit = "news_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
	
	$limit = 20;
	
	$news = new News();
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$news->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$news->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$search_string = $_REQUEST["search_string"];
	$items = $news->getNews($page, $limit, $search_string);
	$pagination = $news->getNewsPagination();
	
	$sm->assign("items", $items);
	$sm->assign("pagination", $pagination);
	$sm->assign("search_string", $search_string);
	
	
	
	
	$sm->display("admin/news.html");
?>