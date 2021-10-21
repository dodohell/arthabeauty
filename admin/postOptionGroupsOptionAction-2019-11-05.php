<?php
	include "globals.php";
	
	if($_REQUEST['action'] == "move") {
		foreach($_REQUEST['optionsID'] as $key => $value) {
			$data = explode("_", $value);
			$db->query("UPDATE " . $options_table . "
						SET pos = " . $data[0] . " WHERE id = " . $data[1]);
		}
	}
	
	if($_REQUEST['action'] == "delete") {
		$id = (int)$_REQUEST["id"];
		$res = $db->autoExecute($options_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
	}
	
	if($_REQUEST['action'] == "edit" || $_REQUEST['action'] == "add") {
		$option_group_id = (int)$_REQUEST["option_group_id"];
		$id = (int)$_REQUEST["id"];
		$option_text = htmlspecialchars(trim($_REQUEST["option_text"]), ENT_QUOTES);
		
		$fields = array(
							"option_group_id" => $option_group_id,
							"option_text" => $option_text,
						);
		
		if ( $_REQUEST["action"] == "add" ){
			shiftPos($db, $options_table);
			$res = $db->autoExecute($options_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
		}
		if ( $_REQUEST["action"] == "edit" ){
			$res = $db->autoExecute($options_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
		}
	}
?>