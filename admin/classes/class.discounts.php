<?php
	class Discounts extends Settings{
		
		public $pagination = "";
		
		public static function getRecord(int $id){
			global $db;
			global $discounts_table;
			
			$row = $db->getRow("SELECT * FROM ".$discounts_table." WHERE id = $id AND edate = 0"); safeCheck($row);
			
			return $row;
		}
		
		public static function addEditRow(FilteredMap $params){
			global $db;
			global $discounts_table;
            global $product_to_discount_table;
            global $brand_to_discount_table;
            global $collection_to_discount_table;
            global $category_to_discount_table;

            $act = $params->getString("act");
			$id = $params->getInt("id");
			$fields = array(
                'active'	=> $params->getInt("active"),
                'in_offers'	=> $params->getInt("in_offers"),
                'name_bg'	=> $params->getString("name_bg"),
                'name_en'	=> $params->getString("name_en"),
                'name_de'	=> $params->getString("name_de"),
                'name_ru'	=> $params->getString("name_ru"),
                'name_ro'	=> $params->getString("name_ro"),
                'discount_type'	=> $params->getInt("discount_type"),
                'discount_date_from'	=> $params->getString("discount_date_from"),
                'discount_date_to'	=> $params->getString("discount_date_to"),
                'discount_value'	=> $params->getNumber("discount_value"),
                'items_count_exceeds'	=> $params->getNumber("items_count_exceeds"),
            );

            $pic = copyImage($_FILES['pic'], "../files/", "../files/tn/", "../files/tntn/", "250x");
            if (!empty($pic)) $fields['pic'] = $pic;
            
            if($act === "add") {
                shiftPos($db, $discounts_table);
                $res = $db->autoExecute($discounts_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
                $id = mysqli_insert_id($db->connection);
            }

            if($act === "edit") {
                $res = $db->autoExecute($discounts_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id); safeCheck($res);
            }

            if($productRequest = explode(',', $params->get("products") ) ){
                $del = $db->Query("DELETE FROM {$product_to_discount_table} WHERE discount_id = '{$id}'");
                foreach($productRequest as $v){
                    $res = $db->Query("INSERT INTO ". $product_to_discount_table . " (product_id, discount_id) VALUES(" . $v . ", ".$id.")"); 
                }
            }

            if($collectionRequest = explode(',', $params->get("collections") ) ){
                $del = $db->Query("DELETE FROM {$collection_to_discount_table} WHERE discount_id = '{$id}'");
                foreach($collectionRequest as $v){
                    $res = $db->Query("INSERT INTO ". $collection_to_discount_table . " (collection_id, discount_id) VALUES(" . $v . ", ".$id.")"); 
                }
            }
            
            if($brandRequest = explode(',', $params->get("brands") ) ){
                $del = $db->Query("DELETE FROM {$brand_to_discount_table} WHERE discount_id = '{$id}'");
                foreach($brandRequest as $v){
                    $res = $db->Query("INSERT INTO ". $brand_to_discount_table . " (brand_id, discount_id) VALUES(" . $v . ", ".$id.")"); 
                }
            }
            /*
            if($categoryRequest = explode(',', $params->get("categories") ) ){
                $del = $db->Query("DELETE FROM {$category_to_discount_table} WHERE discount_id = '{$id}'");
                foreach($categoryRequest as $v){
                        $res = $db->Query("INSERT INTO ". $category_to_discount_table . " (category_id, discount_id) VALUES(" . $v . ", ".$id.")"); 
                }	
            }*/

            $res = $db->Query("DELETE FROM ". $category_to_discount_table . " WHERE discount_id=".$id); safeCheck($res);
            $categoriesRequest = $params->get("categories");
            if ($categoriesRequest){
                foreach($categoriesRequest as $value){
                    $res = $db->Query("INSERT INTO ". $category_to_discount_table . " (category_id, discount_id) VALUES(".$value.", ".$id.")"); safeCheck($res);
                }
            }
            return $id;
		}

		function deleteField($id, $field){
			global $db;
			global $discounts_table;
			
			$res = $db->autoExecute($discounts_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		public static function getDiscounts($name = '', $status = NULL, $discount_date_from = '', $discount_date_to = ''){
			global $db;
			global $lng;
			global $discounts_table;

			$sql_where = '';

            if($status === 1){
                $sql_where .= ' AND active = "1"';
            }

            if($status === 0){
                $sql_where .= ' AND active = "0"';
            }

            if($name){
                $sql_where .= ' AND name_bg LIKE "%'.$name .'%"';
            }

            if($discount_date_from){
                $sql_where .= " AND  UNIX_TIMESTAMP( STR_TO_DATE(discount_date_from, '%Y-%m-%d')) <=". strtotime($discount_date_from);
            }

            if($discount_date_to){
                $sql_where .= " AND  UNIX_TIMESTAMP( STR_TO_DATE(discount_date_to, '%Y-%m-%d')) >=". strtotime($discount_date_to);
            }

            $sql = "SELECT id, name_{$lng} AS name, pos, active, discount_date_to
                                  FROM " . $discounts_table . "
                                  WHERE edate = 0 ". $sql_where ."
                                  ORDER BY pos";

            $items = $db->getAll($sql); safeCheck($items);
            
			return $items;
		}
		
		public static function deleteRecord($id){
			global $db;
			global $discounts_table;
			
			$fields = array(
								"edate" => time(),
								"edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($discounts_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
        
        public static function getDiscountProducts($discountId) {
            global $db;
            global $lng;
            global $products_table;
            global $product_to_discount_table;

            $products = $db->getAll("SELECT id, name_{$lng} AS name, code FROM ".$products_table." WHERE edate = 0 ORDER BY code, name "); safeCheck($products);
            if($discountId){
                $productsSelected = $db->getAll("SELECT product_id FROM ".$product_to_discount_table." WHERE discount_id = $discountId"); safeCheck($productsSelected);
                foreach($products as $k=>$v){
                    foreach($productsSelected as $vv){
                        if ($v["id"] == $vv["product_id"]){
                            $v["selected"] = "checked";
                        }
                    }
                    $products[$k] = $v;
                }
            }
            return $products;
        }
        
        public static function getDiscountCollections($discountId) {
            global $db;
            global $lng;
            global $collections_table;
            global $collection_to_discount_table;

            $collections = $db->getAll("SELECT id, name_{$lng} AS name FROM ".$collections_table." WHERE edate = 0 ORDER BY name_{$lng} "); safeCheck($collections);
            if($discountId){
                $collectionsSelected = $db->getAll("SELECT collection_id FROM ".$collection_to_discount_table." WHERE discount_id = $discountId"); safeCheck($collectionsSelected);
                foreach($collections as $k=>$v){
                    foreach($collectionsSelected as $vv){
                        if ($v["id"] == $vv["collection_id"]){
                            $v["selected"] = "checked";
                        }
                    }
                    $collections[$k] = $v;
                }
            }
            return $collections;
        }
        
        public static function getDiscountBrands($discountId) {
            global $db;
            global $lng;
            global $brands_table;
            global $brand_to_discount_table;

            $brands = $db->getAll("SELECT id, name_{$lng} AS name FROM ".$brands_table." WHERE edate = 0 ORDER BY name_{$lng} "); safeCheck($brands);
            if($discountId){
                $brandsSelected = $db->getAll("SELECT brand_id FROM ".$brand_to_discount_table." WHERE discount_id = $discountId"); safeCheck($brandsSelected);
                foreach($brands as $k=>$v){
                    foreach($brandsSelected as $vv){
                        if ($v["id"] == $vv["brand_id"]){
                            $v["selected"] = "checked";
                        }
                    }
                    $brands[$k] = $v;
                }
            }
            return $brands;
        }
        
        public static function getDiscountCategories($discountId) {
            global $db;
            global $lng;
            global $categories_table;
            global $category_to_discount_table;

            $items = $db->getAll("SELECT
                                    id, 
                                    name_{$lng} AS name
                                FROM 
                                    $categories_table
                                WHERE 
                                    edate = 0 
                                AND category_id = 0
                                ORDER BY pos"); safeCheck($items);
	
            foreach($items as $k=>$v){
                $v["submenus"] = getSubmenusCheckboxesDiscount($v["id"], 1, $discountId);
                $v["level"] = 1;
                $v["first"] = 0;
                $v["current"] = $k;
                
                if($discountId){
                    $selected = $db->getRow("SELECT count(*) AS cntr FROM ".$category_to_discount_table." WHERE discount_id = $discountId AND category_id = ".$v["id"]); safeCheck($selected);
                    if ($selected["cntr"]){
                        $v["selected"] = "checked";
                    }
                }
                
                $v["last"] = sizeof($items)-1;
                $items[$k] = $v;
            }
            return $items;
        }
		
	}