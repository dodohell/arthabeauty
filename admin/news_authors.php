<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
    $page = $params->getInt("page");
	
	$page_heading = $configVars["news_authors"];
	
    $limit = 20;
    
	$php_self = "news_authors.php";
	$php_edit = "news_authors_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$news_authors = new NewsAuthors();
	
	if ( $act == "delete" ){
		$news_authors->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $news_authors->getNewsAuthors($page, $limit, $params);
    $pagination = $news_authors->getNewsAuthorsPagination();
    
	$sm->assign("items", $items);
    $sm->assign("pagination", $pagination);
    
	$sm->display("admin/news_authors.html");
?>