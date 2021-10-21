<?php
	include "globals.php";
	
	$action = $params->get("action");
    
	if($action == "move") {
		foreach($params->get("optionsID") as $key => $value) {
			$data = explode("_", $value);
			$db->query("UPDATE " . $attributes_to_attribute_options_table . "
						SET pos = " . $data[0] . " WHERE id = " . $data[1]);
		}
	}
	
	if($action == "delete") {
		$id = $params->getInt("id");
		$res = $db->autoExecute($attributes_to_attribute_options_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
	}
	
	if($action == "edit" || $action == "add") {
		$attribute_id = $params->getInt("attribute_id");
		$id = $params->getInt("id");
		$option_text_bg = $params->getString("option_text_bg");
		$option_text_en = $params->getString("option_text_en");
		$option_text_de = $params->getString("option_text_de");
		$option_text_ru = $params->getString("option_text_ru");
		$option_text_ro = $params->getString("option_text_ro");
		$hex = $params->get("hex");
		
		$fields = array(
							"attribute_id" => $attribute_id,
							"option_text_bg" => $option_text_bg,
							"option_text_en" => $option_text_en,
							"option_text_de" => $option_text_de,
							"option_text_ru" => $option_text_ru,
							"option_text_ro" => $option_text_ro,
							"hex" => $hex,
						);
		
		if ( $action == "add" ){
			shiftPos($db, $attributes_to_attribute_options_table);
			$res = $db->autoExecute($attributes_to_attribute_options_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
		}
		if ( $action == "edit" ){
			$res = $db->autoExecute($attributes_to_attribute_options_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
		}
	}
?>