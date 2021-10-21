<?php
	include("globals.php");
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
	$page_heading = $configVars["news_categories"];
	
	$php_self = "news_categories.php";
	$php_edit = "news_categories_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
	
	
	$news_categories = new NewsCategories();
	
	if ( $act == "delete" ){
		$news_categories->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $news_categories->getNewsCategories();
	$sm->assign("items", $items);
	
	
	
	
	$sm->display("admin/news_categories.html");
?>