<?php
	class DeliveryTypes extends Settings{
		
		public static function getRecord(int $id){
			global $db;
			global $delivery_types_table;
			
			$row = $db->getRow("SELECT * FROM ".$delivery_types_table." WHERE id = $id"); safeCheck($row);
			
			return $row;
		}
		
        /**
         * 
         * @global DB $db
         * @global type $delivery_types_table
         * @param FilteredMap $params
         */
		public static function addEditRow(FilteredMap $params){
			global $db;
			global $delivery_types_table;
			
			$act = $params->getString("act");
			$id = $params->getInt("id");
            
			$fields = array(
				'name_bg'	=> $params->getString("name_bg"),
				'name_en'	=> $params->getString("name_en"),
				'name_de'	=> $params->getString("name_de"),
				'name_ru'	=> $params->getString("name_ru"),
				'name_ro'	=> $params->getString("name_ro"),
				'code'          => $params->getString("code"),
				'active'        => $params->getInt("active"),
				'cms_user_id'	=> $_SESSION["uid"],
				'ip'            => $_SERVER["REMOTE_ADDR"],
			);
            
			if($act == "add") {
                $fields["postdate"] = time();
				shiftPos($db, $delivery_types_table);
				$res = $db->autoExecute($delivery_types_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
                $fields["updated_at"] = time();
				$res = $db->autoExecute($delivery_types_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
            
		}
        
		public static function getDeliveryTypes(){
			global $db;
			global $lng;
			global $delivery_types_table;
			
			$delivery_types = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$delivery_types_table." WHERE edate = 0 ORDER BY pos"); safeCheck($delivery_types);
//			if ($options && $options["selected"] && $options["customer_id"] ){
//				$delivery_typesSelected = $db->getAll("SELECT * FROM ".$customers_to_delivery_types_table." WHERE customer_id = '".$options["customer_id"]."'"); safeCheck($delivery_typesSelected);
//			}
//			
//			foreach($delivery_types as $k => $v){
//				foreach($delivery_typesSelected as $kk => $vv){
//					if ( $vv["city_id"] == $v["id"] ){
//						$v["selected"] = "checked";
//					}
//				}
//				
//				$delivery_types[$k] = $v;
//			}
			
			return $delivery_types;
		}
		
		public static function deleteRecord($id){
			global $db;
			global $delivery_types_table;
			
			$fields = array(
								"edate" => time(),
								"edate_cms_user_id" => $_SESSION["uid"],
							);
			$res = $db->autoExecute($delivery_types_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>