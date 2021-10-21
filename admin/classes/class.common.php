<?php
	class Common extends Settings{
		
		
		function getRecord($tag){
			global $db;
			global $lng;
			global $common_table;
			
			$row = $db->getRow("SELECT * FROM ".$common_table." WHERE tag = '".$tag."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $common_table;
			
			$row = $db->getRow("SELECT * FROM ".$common_table.""); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow($params){
			global $db;
			global $common_table;
			
			$act = $params["act"];
			$tag = $params["tag"];
			$fields = array(
				'description_bg'	=> trim($params['description_bg']),
				'description_en'	=> trim($params['description_en']),
				'description_de'	=> trim($params['description_de']),
				'description_ro'	=> trim($params['description_ro']),
				'description_ru'	=> trim($params['description_ru']),
			);
			
			if($act == "edit") {
				$tag = $params["tag"];
				$res = $db->autoExecute($common_table,$fields,DB_AUTOQUERY_UPDATE,"tag = '" . $tag . "'");safeCheck($res);
			}
			
		}
		
		function getCommon($page = 0){
			global $db;
			global $lng;
			global $common_table;
			
			$common = $db->getAll("SELECT * FROM ".$common_table." ORDER BY tag"); safeCheck($common);
			foreach($common as $k => $v){
				
				$common[$k] = $v;
			}
			return $common;
		}
		
	}
	
?>