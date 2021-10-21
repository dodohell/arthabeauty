<?php
	class Categories extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $categories_table;
                        global $lng;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT c.*, c.name_$lng AS name FROM ".$categories_table." AS c WHERE c.id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $categories_table;
			
			$row = $db->getRow("SELECT * FROM ".$categories_table.""); safeCheck($row);
			
			return $row;
		}
		
        /**
         * 
         * @global type $db
         * @global string $categories_table
         * @global string $categories_to_menus_table
         * @param FilteredMap $params
         */
		function addEditRow($params){
			global $db;
			global $categories_table;
            global $category_to_category_type_table;
            global $category_to_category_info_table;
            global $category_to_category_table;
            global $product_to_category_table;
            global $collection_to_category_table;
            global $brand_to_category_table;
//            global $categories_to_products_top_table;


            $act = $params->getString("act");
			$id = $params->getInt("id");
            $category_id = $params->getInt('category_id') ? $params->getInt('category_id') : 0;
            
			$fields = array(
                'category_id'			=> $category_id,
                'name_bg'				=> $params->getString('name_bg'),
                'name_en'				=> $params->getString('name_en'),
                'name_de'				=> $params->getString('name_de'),
                'name_ru'				=> $params->getString('name_ru'),
                'name_ro'				=> $params->getString('name_ro'),
                'excerpt_bg'            => $params->get('excerpt_bg'),
                'excerpt_en'            => $params->get('excerpt_en'),
                'excerpt_de'            => $params->get('excerpt_de'),
                'excerpt_ru'            => $params->get('excerpt_ru'),
                'excerpt_ro'            => $params->get('excerpt_ro'),
                'description_bg'		=> $params->get('description_bg'),
                'description_en'		=> $params->get('description_en'),
                'description_de'		=> $params->get('description_de'),
                'description_ru'		=> $params->get('description_ru'),
                'description_ro'		=> $params->get('description_ro'),
                'h1_bg'                 => $params->getString('h1_bg'),
                'h1_en'                 => $params->getString('h1_en'),
                'h1_de'                 => $params->getString('h1_de'),
                'h1_ru'                 => $params->getString('h1_ru'),
                'h1_ro'                 => $params->getString('h1_ro'),
                'category_type'			=> $params->getInt('category_type'),
                'pic_1_name_bg'			=> $params->getString('pic_1_name_bg'),
                'pic_1_name_en'			=> $params->getString('pic_1_name_en'),
                'pic_1_name_de'			=> $params->getString('pic_1_name_de'),
                'pic_1_name_ru'			=> $params->getString('pic_1_name_ru'),
                'pic_1_name_ro'			=> $params->getString('pic_1_name_ro'),
                'pic_2_name_bg'			=> $params->getString('pic_2_name_bg'),
                'pic_2_name_en'			=> $params->getString('pic_2_name_en'),
                'pic_2_name_de'			=> $params->getString('pic_2_name_de'),
                'pic_2_name_ru'			=> $params->getString('pic_2_name_ru'),
                'pic_2_name_ro'			=> $params->getString('pic_2_name_ro'),
                'url_bg'				=> $params->getString('url_bg'),
                'url_en'				=> $params->getString('url_en'),
                'url_de'				=> $params->getString('url_de'),
                'url_ru'				=> $params->getString('url_ru'),
                'url_ro'				=> $params->getString('url_ro'),
                'htaccess_url_bg'		=> $params->getString('htaccess_url_bg'),
                'htaccess_url_en'		=> $params->getString('htaccess_url_en'),
                'htaccess_url_de'		=> $params->getString('htaccess_url_de'),
                'htaccess_url_ru'		=> $params->getString('htaccess_url_ru'),
                'htaccess_url_ro'		=> $params->getString('htaccess_url_ro'),
                'affiliates'			=> $params->getString('affiliates'),
                'meta_title_bg'			=> $params->getString('meta_title_bg'),
                'meta_title_en'			=> $params->getString('meta_title_en'),
                'meta_title_de'			=> $params->getString('meta_title_de'),
                'meta_title_ru'			=> $params->getString('meta_title_ru'),
                'meta_title_ro'			=> $params->getString('meta_title_ro'),
                'meta_description_bg'	=> $params->getString('meta_description_bg'),
                'meta_description_en'	=> $params->getString('meta_description_en'),
                'meta_description_de'	=> $params->getString('meta_description_de'),
                'meta_description_ru'	=> $params->getString('meta_description_ru'),
                'meta_description_ro'	=> $params->getString('meta_description_ro'),
                'meta_keywords_bg'		=> $params->getString('meta_keywords_bg'),
                'meta_keywords_en'		=> $params->getString('meta_keywords_en'),
                'meta_keywords_de'		=> $params->getString('meta_keywords_de'),
                'meta_keywords_ru'		=> $params->getString('meta_keywords_ru'),
                'meta_keywords_ro'		=> $params->getString('meta_keywords_ro'),
                'meta_metatags_bg'		=> $params->getString('meta_metatags_bg'),
                'meta_metatags_en'		=> $params->getString('meta_metatags_en'),
                'meta_metatags_de'		=> $params->getString('meta_metatags_de'),
                'meta_metatags_ru'		=> $params->getString('meta_metatags_ru'),
                'meta_metatags_ro'		=> $params->getString('meta_metatags_ro'),
                'in_menu'				=> $params->getString('in_menu'),
                'menu_pos'				=> $params->getInt('menu_pos'),
                'dont_open'				=> $params->getInt('dont_open'),
                'first_page'			=> $params->getInt('first_page'),
                'show_subcategories_products' => $params->getInt('show_subcategories_products'),
                'active'				=> $params->getInt('active')
            );
			
			$pic_main = copyImage($_FILES['pic_main'], "../files/", "../files/tn/", "../files/tntn/", "250x");
			if(!empty($pic_main)) $fields['pic_main'] = $pic_main;
			
			$pic_1 = copyImage($_FILES['pic_1'], "../files/", "../files/tn/", "../files/tntn/", "250x");
			if(!empty($pic_1)) $fields['pic_1'] = $pic_1;
				
			$pic_2 = copyImage($_FILES['pic_2'], "../files/", "../files/tn/", "../files/tntn/", "250x");
			if(!empty($pic_2)) $fields['pic_2'] = $pic_2;

			$icon = copyImage($_FILES['icon'], "../files/", "../files/tn/", "../files/tntn/", "100x");
			if(!empty($icon)) $fields['icon'] = $icon;
			
			if($act == "add") {
				shiftPos($db, $categories_table);
				$res = $db->autoExecute($categories_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$id = $params->getInt("id");
				$res = $db->autoExecute($categories_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
            
            $res = $db->Query("DELETE FROM ".$category_to_category_type_table." WHERE category_id = '{$id}'"); safeCheck($res);
            $category_typesRequest = $params->get("category_types");
            if ($category_typesRequest && sizeof($category_typesRequest)){
                foreach($category_typesRequest as $k=>$v){
                    $res = $db->Query("INSERT INTO ".$category_to_category_type_table." (category_id, category_type_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
                }
            }
            
            $res = $db->Query("DELETE FROM ".$category_to_category_info_table." WHERE category_id = '{$id}'"); safeCheck($res);
            $category_infoRequest = $params->get("categories_info");
            
            if ($category_infoRequest && sizeof($category_infoRequest)){
                foreach($category_infoRequest as $v){
                    $res = $db->Query("INSERT INTO ".$category_to_category_info_table." (category_id, category_info_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
                }
            }
            
            $res = $db->Query("DELETE FROM ".$category_to_category_table." WHERE category_main_id = '{$id}'"); safeCheck($res);
            $categoriesRequest = $params->get("categories");
            if ($categoriesRequest && sizeof($categoriesRequest)){
                foreach($categoriesRequest as $k=>$v){
                    $res = $db->Query("INSERT INTO ".$category_to_category_table." (category_main_id, category_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
                }
            }
            
//            $res = $db->Query("DELETE FROM ".$categories_to_products_top_table." WHERE category_id = '{$id}'"); safeCheck($res);
//            $productsRequest = explode(",",$params->get("products"));
//            if (sizeof($productsRequest)){
//                foreach($productsRequest as $k=>$v){
//                    $res = $db->Query("INSERT INTO ".$categories_to_products_top_table." (category_id, product_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
//                }
//            }
            if($collectionRequest = explode(',', $params->get("collections") ) ){
                $del = $db->Query("DELETE FROM {$collection_to_category_table} WHERE category_id = '{$id}'");
                foreach($collectionRequest as $v){
                    $res = $db->Query("INSERT INTO ". $collection_to_category_table . " (collection_id, category_id) VALUES(" . $v . ", ".$id.")"); 
                }
            }

            if($brandRequest = explode(',', $params->get("brands") ) ){
                $del = $db->Query("DELETE FROM {$brand_to_category_table} WHERE category_id = '{$id}'");
                foreach($brandRequest as $v){
                    $res = $db->Query("INSERT INTO ". $brand_to_category_table . " (brand_id, category_id) VALUES(" . $v . ", ".$id.")"); 
                }
            }

            if($productsRequest = explode(',', $_REQUEST['products'] ) ){
                $del = $db->Query("DELETE FROM {$product_to_category_table} WHERE category_id = '{$id}'"); safeCheck($del);
                foreach($productsRequest as $v){
                    $res = $db->Query("INSERT INTO ". $product_to_category_table . " (product_id, category_id) VALUES(" . $v . ", ".$id.")"); 
                }
            }
			
			$htaccessUpdate = new Settings();
			$htaccess_type = "categories";
			
			if ( $params->getString("htaccess_url_bg") ){
				$htaccessUpdate->updateHtaccess("bg", $params->getString("htaccess_url_bg"), $htaccess_type, $id);
			}
			if ( $params->getString("htaccess_url_en") ){
				$htaccessUpdate->updateHtaccess("en", $params->getString("htaccess_url_en"), $htaccess_type, $id);
			}
			if ( $params->getString("htaccess_url_de") ){
				$htaccessUpdate->updateHtaccess("de", $params->getString("htaccess_url_de"), $htaccess_type, $id);
			}
			if ( $params->getString("htaccess_url_ru") ){
				$htaccessUpdate->updateHtaccess("ru", $params->getString("htaccess_url_ru"), $htaccess_type, $id);
			}
			if ( $params->getString("htaccess_url_ro") ){
				$htaccessUpdate->updateHtaccess("ro", $params->getString("htaccess_url_ro"), $htaccess_type, $id);
			}
            
			return $id;
		}
		
		function postImage($file = "", $category_id = 0){
			global $db;
			global $categories_images_table;
			
			$fields = array(
								"file" => $file,
								"category_id" => $category_id,
							);
			shiftPos($db, $categories_images_table);
			$res = $db->autoExecute($categories_images_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}
		
		function deleteImage($id){
			global $db;
			global $categories_images_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($categories_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function postFile($file = "", $category_id = 0){
			global $db;
			global $categories_files_table;
			
			$fields = array(
								"file" => $file,
								"category_id" => $category_id,
							);
			shiftPos($db, $categories_files_table);
			$res = $db->autoExecute($categories_files_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}
		
		function deleteFile($id){
			global $db;
			global $categories_files_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($categories_files_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function deleteField($id, $field){
			global $db;
			global $lng;
			global $categories_table;
			
			$res = $db->autoExecute($categories_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function getImages($category_id){
			global $db;
			global $lng;
			global $categories_images_table;
			
			$images = $db->getAll("SELECT * FROM ".$categories_images_table." WHERE edate = 0 AND category_id = '".$category_id."' ORDER BY pos"); safeCheck($images);
			return $images;
		}
		
		function getFiles($category_id){
			global $db;
			global $lng;
			global $categories_files_table;
			
			$files = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$categories_files_table." WHERE edate = 0 AND category_id = '".$category_id."' ORDER BY pos"); safeCheck($files);
			return $files;
		}
		
		
		function getCategories($productId, $activeOnly = 0){
			global $db;
			global $lng;
			global $categories_table;
			global $product_to_category_table;
			
            if($activeOnly){
                $activeOnlySQL = " AND c.active = 1 ";
            }
            
			$items = $db->getAll("  SELECT
                                        c.id,
                                        c.name_{$lng} AS name,
                                        c.active
                                    FROM
                                        {$categories_table} as c
                                    WHERE
                                        c.edate = 0
                                    AND c.category_id = 0
                                    {$activeOnlySQL}
                                    ORDER BY c.pos"); safeCheck($items);
            foreach($items as $k=>$v){
                $v["submenus"] = getSubmenusCheckboxesProduct($v["id"], 1, $productId);
                $v["level"] = 1;
                $v["first"] = 0;
                $v["current"] = $k;
                $selected = $db->getRow("SELECT count(*) AS cntr FROM ".$product_to_category_table." WHERE product_id = '{$productId}' AND category_id = '".$v["id"]."'"); safeCheck($selected);
                
                if ($selected["cntr"]){
                    $v["selected"] = "checked";
                }
                $v["last"] = sizeof($items)-1;
                $items[$k] = $v;
            }
            return $items;
        }
    
        
		public function getCategoriesList($activeOnly = 0){
			global $db;
			global $lng;
			global $categories_table;
			global $product_to_category_table;
			global $products_table;
			
            if($activeOnly){
                $activeOnlySQL = " AND c.active = 1 ";
            }
            
			$items = $db->getAll("  SELECT
                                        c.id,
                                        c.name_{$lng} AS name,
                                        c.active
                                    FROM
                                        {$categories_table} as c
                                    WHERE
                                        c.edate = 0
                                    AND c.category_id = 0
                                    {$activeOnlySQL}
                                    ORDER BY c.pos"); safeCheck($items);
            foreach($items as $k=>$v){
                $v["submenus"] = getSubcatsList($v["id"], 1);
                $v["level"] = 1;
                $v["first"] = 0;
                $v["current"] = $k;
                
                $v["last"] = sizeof($items)-1;

                $products = productsToCategories($v["id"]);
                $brands = brandsToCategories($v["id"]);
                $collections = collectionsToCategories($v["id"]);

                $sqlCount = "SELECT 
							COUNT(DISTINCT products.id) as cntr
						FROM 
                            ".$products_table." AS products
						WHERE";
                if($products || $brands || $collections) {
                    $sqlCount .= " (";
                    if(!empty($products)) {
                        $sql_in_ptc = implode(",", $products);
                        $sqlCount .= " products.id IN ({$sql_in_ptc})";
                    }
                    if(!empty($brands)) {
                        $condition = (!$products) ? '' : 'OR'; 
                        $sql_in_btc = implode(",", $brands);
                        $sqlCount .= " {$condition} products.brand_id IN ({$sql_in_btc})";
                    }
                    if(!empty($collections)) {
                        $condition = (!$products && !$brands) ? '' : 'OR'; 
                        $sql_in_ctc = implode(",", $collections);
                        $sqlCount .= " {$condition} products.collection_id IN ({$sql_in_ctc})";
                    }
                    $sqlCount .= ") ";
                } else {
                    $sqlCount .= " 0";
                }
                
                $results = $db->getRow($sqlCount); safeCheck($results);
                
                $v["products_count"] = (int)$results["cntr"];

                $items[$k] = $v;
            }
            
            return $items;
		}
		
		function getSubmenusCheckboxesProduct($id, $level = 0, $product_id){
            global $db;
            global $categories_table;
            global $product_to_category_table;
            global $lng;
            
            $items = $db->getAll("SELECT 
                                     name_" . $lng . " AS name,
                                     id,
                                     category_id
                              FROM " . $categories_table . "
                              WHERE category_id = " . $id . "
                              AND edate = 0
                              ORDER BY pos
                              "); safeCheck($items);

            foreach($items as $k=>$v){

                $v["level"] = $level + 1;
                
                $selected = $db->getRow("SELECT count(*) AS cntr FROM ".$product_to_category_table." WHERE product_id = '{$product_id}' AND category_id = '".$v["id"]."'"); safeCheck($selected);

                if ($selected["cntr"]){
                    $v["selected"] = "checked";
                }
                for($i = 0 ; $i <= $v["level"]; $i++){
                    $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
                }
                //$v["nbsps"].'<input type="checkbox" value="'.$v["id"].'" name="categories[]" /> '.$v["name"].'<br />';
                $v["submenus"] = getSubmenusCheckboxesProduct($v["id"], $level+1, $product_id);
                //$v["widthI"] = 100-$level*20;
                $v["first"] = 0;
                $v["current"] = $k;
                $v["last"] = sizeof($items)-1;
                $items[$k] = $v;

            }

            return $items;
        }
		
		function deleteRecord($id){
			global $db;
			global $categories_table;
			
			$fields = array(
								"edate" => time(),
                                "edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($categories_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>