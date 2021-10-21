<?php
	include("globals.php");
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["news_categories"];
	
	$php_self = "news_categories.php";
	$php_edit = "news_categories_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);

	$news_categories = new NewsCategories();
	$settings = new Settings();
	
	if( isset($_REQUEST["Submit"]) ){
		$news_categories->addEditRow($_REQUEST);
		header("Location: news_categories.php");
		die();
	}
	$row["publishdate_value"] = date("m/d/Y");
	if ( $id ){
		$row = $news_categories->getRecord($id);
		$row["publishdate_value"] = date("m/d/Y", $row["publishdate"]);
	}
	$sm->assign("row", $row);
	
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
	
	$sm->display("admin/news_categories_ae.html");
?>