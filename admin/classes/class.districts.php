<?php
	class Districts extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $districts_table;
            global $lng;

            $id = (int)$id;
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$districts_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow($params){
			global $db;
			global $districts_table;
			
			$act = $params["act"];
			$id = (int)$params["id"];
			$fields = array(
				'name_bg'	=> htmlspecialchars(trim($params['name_bg'])),
				'name_en'	=> htmlspecialchars(trim($params['name_en'])),
				'name_de'	=> htmlspecialchars(trim($params['name_de'])),
				'name_ru'	=> htmlspecialchars(trim($params['name_ru'])),
				'name_ro'	=> htmlspecialchars(trim($params['name_ro'])),

				'description_bg'	=> trim($params['description_bg']),
				'description_en'	=> trim($params['description_en']),
				'description_de'	=> trim($params['description_de']),
				'description_ru'	=> trim($params['description_ru']),
				'description_ro'	=> trim($params['description_ro']),
				'active'			=> $params['active']
			);
            
			if($act == "add") {
				shiftPos($db, $districts_table);
				$res = $db->autoExecute($districts_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
				
			}
			
			if($act == "edit") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($districts_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
            
		}
        
		function getDistricts(){
			global $db;
			global $lng;
			global $districts_table;
			
			$districts = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$districts_table." WHERE edate = 0 ORDER BY pos"); safeCheck($districts);
//			if ($options && $options["selected"] && $options["customer_id"] ){
//				$districtsSelected = $db->getAll("SELECT * FROM ".$customers_to_districts_table." WHERE customer_id = '".$options["customer_id"]."'"); safeCheck($districtsSelected);
//			}
//			
//			foreach($districts as $k => $v){
//				foreach($districtsSelected as $kk => $vv){
//					if ( $vv["city_id"] == $v["id"] ){
//						$v["selected"] = "checked";
//					}
//				}
//				
//				$districts[$k] = $v;
//			}
			
			return $districts;
		}
		
		function deleteRecord($id){
			global $db;
			global $districts_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($districts_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>