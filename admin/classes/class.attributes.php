<?php
	class Attributes extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $attributes_table;

            $id = (int)$id;
			
			$row = $db->getRow("SELECT * FROM ".$attributes_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
        
        public function getProductTypeAttributesSelectedOrNot($productTypeId) {
            global $product_type_to_attribute_table;
            global $attributes_table;
            global $lng;
            global $db;
            
            $attributes = $db->getAll("SELECT id, name_" . $lng . " AS name FROM ".$attributes_table." WHERE edate = 0 ORDER BY pos"); safeCheck($attributes);
            $attributes_selected = $db->getAll("SELECT * FROM ".$product_type_to_attribute_table. "  WHERE product_type_id= ". $productTypeId); safeCheck($attributes_selected);
            foreach($attributes as $k=>$v){
                foreach($attributes_selected as $kk => $vv){
                    if ($v["id"] == $vv["attribute_id"]){
                        $v["selected"] = "checked";
                    }
                }
                $attributes[$k] = $v;
            }
            return $attributes;
        }
		
		function updateRow($test = ""){
			global $db;
			global $attributes_table;
			
			$row = $db->getRow("SELECT * FROM ".$attributes_table.""); safeCheck($row);
			
			return $row;
		}
		
        /**
         * 
         * @global type $db
         * @global type $attributes_table
         * @param FilteredMap $params
         */
		function addEditRow($params){
			global $db;
			global $attributes_table;
			
			$act = $params->getString("act");
			$id = $params->getInt("id");
			$fields = array(
				'name_bg'	=> $params->getString("name_bg"),
				'name_en'	=> $params->getString("name_en"),
				'name_de'	=> $params->getString("name_de"),
				'name_ru'	=> $params->getString("name_ru"),
				'name_ro'	=> $params->getString("name_ro"),
				'code'      => $params->getString("code"),
				'unit'      => $params->getString("unit"),
				'attribute_type_id'	=> $params->getInt("attribute_type_id"),
				'is_filterable'	=> $params->getInt("is_filterable"),
				'buttons'	=> $params->getInt("buttons"),
				'active'	=> $params->getInt("active"),
				'cms_user_id'	=> $_SESSION["uid"]
			);
			
			if($act == "add") {
				shiftPos($db, $attributes_table);
				$res = $db->autoExecute($attributes_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$id = $params->getInt("id");
				$res = $db->autoExecute($attributes_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			
			return $id;
			
		}
		
		function deleteField($id, $field){
			global $db;
			global $attributes_table;
			
			$res = $db->autoExecute($attributes_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function getAttributes($options = array()){
			global $db;
			global $lng;
			global $attributes_table;
            global $attributes_to_attribute_options_table;
            
            $attributes = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$attributes_table." WHERE edate = 0 ORDER BY pos"); safeCheck($attributes);
//			if ( is_array($options) && $options["selected"] && $options["product_id"] ){
//				$attributesSelected = $db->getAll("SELECT * FROM ".$product_to_product_type_table." WHERE product_id = '".$options["product_id"]."'"); safeCheck($attributesSelected);
//				foreach($attributes as $k => $v){
//					foreach($attributesSelected as $kk => $vv){
//						if ( $vv["product_type_id"] == $v["id"] ){
//							$v["selected"] = "checked";
//						}
//					}
//					$attributes[$k] = $v;
//				}
//			}
			return $attributes;
		}
		
		function deleteRecord($id){
			global $db;
			global $attributes_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($attributes_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>