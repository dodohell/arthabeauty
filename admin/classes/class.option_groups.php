<?php
	class OptionGroups extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $lng;
			global $option_groups_table;

            $id = (int)$id;
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$option_groups_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
        
        public function getProductOptionGroupsSelectedOrNot($productId) {
            global $options_table;
            global $option_groups_table;
            global $variants_table;
            global $lng;
            global $db;
            
            $option_groups = $db->getAll("SELECT id, name_{$lng} AS name FROM ".$option_groups_table." WHERE edate = 0 ORDER BY pos"); safeCheck($option_groups);
//            $option_groups_selected = $db->getAll("SELECT * FROM ".$variants_table." WHERE product_id = '".$productId."' AND quantity > 0 AND edate = 0"); safeCheck($option_groups_selected);
            $option_groups_selected = $db->getAll("SELECT * FROM ".$variants_table." WHERE product_id = '".$productId."' AND edate = 0"); safeCheck($option_groups_selected);

            foreach($option_groups as $k => $v){
                foreach($option_groups_selected as $kk => $vv){
                    if ( $v["id"] == $vv["option_group_id"] ){
                        $v["checked"] = "checked";
                    }
                }
                $options = $db->getAll("SELECT id, option_text AS name FROM ".$options_table." WHERE edate = 0 AND option_group_id = '".$v["id"]."' ORDER BY pos"); safeCheck($options);
                foreach($options as $kk => $vv){
                    foreach($option_groups_selected as $kkk => $vvv){
                        if ( $vv["id"] == $vvv["option_id"] ){
                            $vv["selected_values"] = $vvv;
                            $vv["checked"] = "checked";
                        }
                    }
                    $options[$kk] = $vv;
                }
            
                $v["options"] = $options;
                $option_groups[$k] = $v;
            }
            return $option_groups;
        }
        
//        public function getOptionsByOptionGroupId($option_group_id) {
//            global $db;
//            global $options_table;
//            
//            $options = $db->getAll("SELECT id, option_text AS name FROM ".$options_table." WHERE edate = 0 AND option_group_id = '".$option_group_id."' ORDER BY pos"); safeCheck($options);
//            
//            return $options;
//        }
		
		function updateRow($test = ""){
			global $db;
			global $option_groups_table;
			
			$row = $db->getRow("SELECT * FROM ".$option_groups_table.""); safeCheck($row);
			
			return $row;
		}
		
        /**
         * 
         * @global type $db
         * @global type $option_groups_table
         * @param FilteredMap $params
         */
		function addEditRow($params){
			global $db;
			global $option_groups_table;
			
			$act = $params->getString("act");
			$id = $params->getInt("id");
			$fields = array(
				'name_bg'	=> $params->getString("name_bg"),
				'name_en'	=> $params->getString("name_en"),
				'name_de'	=> $params->getString("name_de"),
				'name_ru'	=> $params->getString("name_ru"),
				'name_ro'	=> $params->getString("name_ro"),
				'code'      => $params->getString("code"),
				'active'	=> $params->getInt("active"),
				'buttons'	=> $params->getInt("buttons"),
				'cms_user_id'	=> $_SESSION["uid"]
			);
			
			if($act == "add") {
				shiftPos($db, $option_groups_table);
				$res = $db->autoExecute($option_groups_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$id = $params->getInt("id");
				$res = $db->autoExecute($option_groups_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
		}
		
		function deleteField($id, $field){
			global $db;
			global $option_groups_table;
			
			$res = $db->autoExecute($option_groups_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function getOptionGroups($options = array()){
			global $db;
			global $lng;
			global $option_groups_table;
//            global $option_groups_to_attribute_options_table;
            
            $option_groups = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$option_groups_table." WHERE edate = 0 ORDER BY pos"); safeCheck($option_groups);
//			if ( is_array($options) && $options["selected"] && $options["product_id"] ){
//				$option_groupsSelected = $db->getAll("SELECT * FROM ".$product_to_product_type_table." WHERE product_id = '".$options["product_id"]."'"); safeCheck($option_groupsSelected);
//				foreach($option_groups as $k => $v){
//					foreach($option_groupsSelected as $kk => $vv){
//						if ( $vv["product_type_id"] == $v["id"] ){
//							$v["selected"] = "checked";
//						}
//					}
//					$option_groups[$k] = $v;
//				}
//			}
			return $option_groups;
		}
		
		function deleteRecord($id){
			global $db;
			global $option_groups_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($option_groups_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>