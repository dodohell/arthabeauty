<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["quiz_categories"];
	
	$php_self = "quiz_categories.php";
	$php_edit = "quiz_categories_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
    
	$quiz_categories = new QuizCategories();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$quiz_categories->addEditRow($params);
		header("Location: quiz_categories.php");
		die();
	}
    
	if ( $id ){
		$row = $quiz_categories->getRecord($id);
		$sm->assign("row", $row);
	}
    
	if( $params->has("SaveAndStay") ){
		$id = $quiz_categories->addEditRow($params);
        $row = $quiz_categories->getRecord($id);
		header("Location: quiz_categories_ae.php?id=$id&act=edit");
		die();
	}
    
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$quiz_categories->deleteField($id, $field);
		header("Location: quiz_categories_ae.php?id=$id&act=edit");
		die();
	}
    
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
    $manifacturers = Manifacturers::getManifacturersAll();
    $sm->assign("manifacturers", $manifacturers);
    
	$sm->display("admin/quiz_categories_ae.html");
?>