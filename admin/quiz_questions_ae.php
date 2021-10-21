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
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$quiz_questions->addEditRow($params);
		header("Location: quiz_questions.php");
		die();
	}
    
	if ( $id ){
		$row = $quiz_questions->getRecord($id);
		$sm->assign("row", $row);
	}
    
	if( $params->has("SaveAndStay") ){
		$id = $quiz_questions->addEditRow($params);
        header("Location: quiz_questions_ae.php?id={$id}&act=edit");
		die();
	}
    
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$quiz_questions->deleteField($id, $field);
		header("Location: quiz_questions_ae.php?id=$id&act=edit");
		die();
	}
    
	if ( $act == "deleteOption" ){
        $option_id = $params->getInt("option_id");
		$field = $params->getString("field");
		$quiz_questions->deleteOptionField($option_id, $field);
		header("Location: quiz_questions_ae.php?id=$id&act=edit");
		die();
	}
    
//	$editor = $settings->generateEditor($languages,"description", $row);
//	$sm->assign("editor", $editor);
	
    $types = QuizQuestions::getTypes();
    $sm->assign("types", $types);
    
    $quiz_categories = QuizCategories::getQuizCategoriesAll();
    $sm->assign("quiz_categories", $quiz_categories);
    
	$sm->display("admin/quiz_questions_ae.html");
?>