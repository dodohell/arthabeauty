<?php
	class ProductTypes extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $product_types_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT * FROM ".$product_types_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $product_types_table;
			
			$row = $db->getRow("SELECT * FROM ".$product_types_table.""); safeCheck($row);
			
			return $row;
		}
		
        /**
         * 
         * @global type $db
         * @global type $product_types_table
         * @param FilteredMap $params
         */
		function addEditRow($params){
			global $db;
			global $product_types_table;
            global $product_type_to_attribute_table;
            
			$act = $params->getString("act");
			$id = $params->getInt("id");
            
			$fields = array(
				'name_bg'	=> $params->getString("name_bg"),
				'name_en'	=> $params->getString("name_en"),
				'name_de'	=> $params->getString("name_de"),
				'name_ru'	=> $params->getString("name_ru"),
				'name_ro'	=> $params->getString("name_ro"),
				'active'	=> $params->getInt("active"),
				'cms_user_id'	=> $_SESSION["uid"]
			);
            
            
            $attributes = [];
            if($params->has("attributes")){
                $attributes = $params->get("attributes");
            }
			
			if($act == "add") {
				shiftPos($db, $product_types_table);
				$res = $db->autoExecute($product_types_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$id = $params->getInt("id");
				$res = $db->autoExecute($product_types_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
            }
            if($attributes){
                $res = $db->Query("DELETE FROM ". $product_type_to_attribute_table . " WHERE product_type_id=".$id); safeCheck($res);
                foreach ($attributes as $k => $v) {
                    $fieldsTmp["product_type_id"] = $id;
                    $fieldsTmp["attribute_id"] = $v;
                    $res = $db->autoExecute($product_type_to_attribute_table ,$fieldsTmp,DB_AUTOQUERY_INSERT); safeCheck($res);
                }
            }
			
			return $id;
			
		}
		
		function deleteField($id, $field){
			global $db;
			global $product_types_table;
			
			$res = $db->autoExecute($product_types_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function getProductTypes($options = array()){
			global $db;
			global $lng;
			global $product_types_table;
            global $product_to_product_type_table;


            $product_types = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$product_types_table." WHERE edate = 0 ORDER BY pos"); safeCheck($product_types);
			if ( is_array($options) && $options["selected"] && $options["product_id"] ){
				$product_typesSelected = $db->getAll("SELECT * FROM ".$product_to_product_type_table." WHERE product_id = '".$options["product_id"]."'"); safeCheck($product_typesSelected);
				foreach($product_types as $k => $v){
					foreach($product_typesSelected as $kk => $vv){
						if ( $vv["product_type_id"] == $v["id"] ){
							$v["selected"] = "checked";
						}
					}
					$product_types[$k] = $v;
				}
			}
			return $product_types;
		}
		
        function getProductTypesAllActive(){
			global $db;
			global $lng;
			global $product_types_table;

            $product_types = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$product_types_table." WHERE edate = 0 AND active = 1 ORDER BY pos"); safeCheck($product_types);
			
			return $product_types;
		}
        
		function deleteRecord($id){
			global $db;
			global $product_types_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($product_types_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>