<?php
	include("globals.php");
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["news"];
	
	$php_self = "news.php";
	$php_edit = "news_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
    
	$news = new News();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$news->addEditRow($params);
		header("Location: news.php");
		die();
	}
    
    if( $params->has("SaveAndStay") ){
		$id = $news->addEditRow($params);
		header("Location: news_ae.php?id=$id&act=edit");
		die();
	}
    
	$row["publishdate_value"] = date("m/d/Y");
	if ( $id ){
		$row = $news->getRecord($id);
		$row["publishdate_value"] = date("m/d/Y", $row["publishdate"]);
	}
	$sm->assign("row", $row);

    $newsCategories = new NewsCategories();
    $newsAuthors = new NewsAuthors();
    
	$news_categories = $newsCategories->getNewsCategories();
	$news_categories_selected = $news->getSelectedCategories($id);
	foreach($news_categories as $k => $v){
		foreach($news_categories_selected as $kk => $vv){
			if ( $v["id"] == $vv["news_category_id"] ){
				$v["checked"] = "checked";
			}
		}
		$news_categories[$k] = $v;
	}
	$sm->assign("news_categories", $news_categories);
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
  
	
	$sm->assign("news_authors", $newsAuthors->getNewsAuthorsAll());
  
	$sm->display("admin/news_ae.html");
?>