<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["news_authors"];
	
	$php_self = "news_authors.php";
	$php_edit = "news_authors_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	$news_authors = new NewsAuthors();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$news_authors->addEditRow($params);
		header("Location: news_authors.php");
		die();
	}
    if( $params->has("SaveAndStay") ){
		$id = $news_authors->addEditRow($params);
		header("Location: news_authors_ae.php?id=$id&act=edit");
		die();
	}
	if ( $id ){
		$row = $news_authors->getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$news_authors->deleteField($id, $field);
		header("Location: news_authors_ae.php?id=$id&act=edit");
		die();
	}
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
	
	$sm->display("admin/news_authors_ae.html");
?>