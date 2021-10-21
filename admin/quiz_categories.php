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
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$quiz_categories->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$quiz_categories->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $quiz_categories->getQuizCategories();
	$sm->assign("items", $items);
    
	$sm->display("admin/quiz_categories.html");
?>