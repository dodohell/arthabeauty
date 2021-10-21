<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["categories"];
	
	$php_self = "categories.php";
	$php_edit = "categories_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	$categories = new Categories();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$categories->addEditRow($params);
		header("Location: categories.php");
		die();
	}
	if ( $id ){
		$row = $categories->getRecord($id);
		$sm->assign("row", $row);
	}
	if( $params->has("SaveAndStay") ){
		$id = $categories->addEditRow($params);
    $row = $categories->getRecord($id);
    $category_id = isset($row["category_id"]) ? $row["category_id"] : "";
		header("Location: categories_ae.php?id=$id&act=edit&category_id=".$category_id);
		die();
	}
    
	if ( $act == "delete" ){
		$field = $_REQUEST["field"];
		$categories->deleteField($id, $field);
		$row = $categories->getRecord($id);
    $category_id = isset($row["category_id"]) ? $row["category_id"] : "";
		header("Location: categories_ae.php?id={$id}&act=edit&category_id=".$category_id);
		die();
	}
    
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
	$sm->assign("editorExcerpt", $editorExcerpt);
	
	$editorForOffer = $settings->generateEditor($languages,"for_offer", $row);
	$sm->assign("editorForOffer", $editorForOffer);
	
	$sm->display("admin/categories_ae.html");
?>