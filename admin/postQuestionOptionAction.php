<?php
	include "globals.php";
	
	$action = $params->get("action");
    
	if($action == "move") {
		foreach($params->get("optionsID") as $key => $value) {
			$data = explode("_", $value);
			$db->query("UPDATE " . $quiz_questions_options_table . "
						SET pos = " . $data[0] . " WHERE id = " . $data[1]);
		}
	}
	
	if($action == "delete") {
		$id = $params->getInt("id");
		$res = $db->autoExecute($quiz_questions_options_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
	}
	
	if($action == "edit" || $action == "add") {
		$question_id = $params->getInt("question_id");
		$id = $params->getInt("id");
		$option_text_bg = $params->getString("option_text_bg");
		$option_text_en = $params->getString("option_text_en");
		$option_text_de = $params->getString("option_text_de");
		$option_text_ru = $params->getString("option_text_ru");
		$option_text_ro = $params->getString("option_text_ro");
		
		$fields = array(
							"question_id"    => $question_id,
							"option_text_bg" => $option_text_bg,
							"option_text_en" => $option_text_en,
							"option_text_de" => $option_text_de,
							"option_text_ru" => $option_text_ru,
							"option_text_ro" => $option_text_ro,
                            "cms_user_id"    => $_SESSION["uid"]
						);
		$pic = copyImage($_FILES['option_pic'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic))
            $fields['pic'] = $pic;
        
		if ( $action == "add" ){
            $fields["postdate"] = time();
			shiftPos($db, $quiz_questions_options_table);
			$res = $db->autoExecute($quiz_questions_options_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
		}
		if ( $action == "edit" ){
            $fields["updated_date"] = time();
			$res = $db->autoExecute($quiz_questions_options_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
		}
	}
?>