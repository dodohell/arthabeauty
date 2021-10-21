<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["quiz_questions"];
	
	$php_self = "quiz_questions.php";
	$php_edit = "quiz_questions_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$quiz_questions = new QuizQuestions();
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$quiz_questions->deleteRecord($value);
		}
	}
	
	if ( $act == "delete" ){
		$quiz_questions->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $quiz_questions->getQuizQuestions();
	$sm->assign("items", $items);
    
	$sm->display("admin/quiz_questions.html");
?>