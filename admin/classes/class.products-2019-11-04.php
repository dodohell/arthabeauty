<?php
	class Products extends Settings{
		
		public $pagination = "";
		
		public function getRecord($id){
			global $db;
			global $products_table;
            global $lng;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT c.*, c.name_$lng AS name FROM ".$products_table." AS c WHERE c.id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $products_table;
			
			$row = $db->getRow("SELECT * FROM ".$products_table.""); safeCheck($row);
			
			return $row;
		}
		
        /**
         * 
         * @global type $db
         * @global type $lng
         * @global type $products_table
         * @global type $attributes_table
         * @global type $product_type_to_attribute_table
         * @global type $product_to_attribute_option_table
         * @global type $product_to_category_table
         * @global type $product_to_product_table
         * @global type $variants_table
         * @param FilteredMap $params
         */
		function addEditRow($params){
			global $db;
            global $lng;
            global $products_table;
            global $attributes_table;
            global $product_type_to_attribute_table;
            global $product_to_attribute_option_table;
            global $product_to_category_table;
            global $product_to_product_table;
            global $variants_table;
            
            $act = $params->getString("act");
			$id = $params->getInt("id");
            //$product_id = $params->getInt('product_id') ? $params->getInt('product_id') : 0;
            $brand_id = $params->getInt("brand_id");
            $collection_id = $brand_id > 0 ? $params->getInt("collection_id") : 0;
            $color_id = $params->getInt("color_id");
            
			$fields = array(
                "code"                  => $params->getString('code'),
                "barcode"               => $params->getString('barcode'),
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
                'usage_bg'              => $params->get('usage_bg'),
                'usage_en'              => $params->get('usage_en'),
                'usage_de'              => $params->get('usage_de'),
                'usage_ru'              => $params->get('usage_ru'),
                'usage_ro'              => $params->get('usage_ro'),
                'ingredients_bg'        => $params->get('ingredients_bg'),
                'ingredients_en'        => $params->get('ingredients_en'),
                'ingredients_de'        => $params->get('ingredients_de'),
                'ingredients_ru'        => $params->get('ingredients_ru'),
                'ingredients_ro'        => $params->get('ingredients_ro'),
                
                "price_supply"          => $params->getNumber("price_supply"),
                "price"                 => $params->getNumber("price"),
                "brand_id"              => $brand_id,
                "collection_id"         => $collection_id,
                "color_id"              => $color_id > 0 ? $color_id : 0,
                "product_type_id"       => $params->getInt("product_type_id"),
                "specialoffer_id"       => $params->getInt("specialoffer_id"),
                "hotoffer"              => $params->getInt("hotoffer"),
                "promotion"             => $params->getInt("promotion"),
                "weight"                => $params->getNumber("weight"),
                "hand_made"             => $params->has("hand_made") ? 1 : 0,
                "marine_product"        => $params->has("marine_product") ? 1 : 0,
                'first_page'			=> $params->getInt('first_page'),
                "new_product"           => $params->has("new_product") ? 1 : 0,
                "cart_addon"            => $params->has("cart_addon") ? 1 : 0,
                "enable_bonus_points"   => $params->getInt("enable_bonus_points"),
                "bonus_points"          => $params->getInt("bonus_points"),
                "quantity"              => $params->getInt("quantity"),
                "gender"                => $params->getInt("gender"),
                "male"                  => $params->getInt("male"),
                "female"                => $params->getInt("female"),
                "unisex"                => $params->getInt("unisex"),
                "children"              => $params->getInt("children"),
                
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
                'meta_title_bg'			=> $params->getString('meta_title_bg'),
                'meta_title_en'			=> $params->getString('meta_title_en'),
                'meta_title_de'			=> $params->getString('meta_title_de'),
                'meta_title_ru'			=> $params->getString('meta_title_ru'),
                'meta_title_ro'			=> $params->getString('meta_title_ro'),
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
                'meta_description_bg'	=> $params->getString('meta_description_bg'),
                'meta_description_en'	=> $params->getString('meta_description_en'),
                'meta_description_de'	=> $params->getString('meta_description_de'),
                'meta_description_ru'	=> $params->getString('meta_description_ru'),
                'meta_description_ro'	=> $params->getString('meta_description_ro'),
                "last_update"           => $act == "edit" ? time() : NULL,
                'active'				=> $params->getInt('active')
            );
            if($act == "add"){
                $fields["postdate"] = time();
                
				shiftPos($db, $products_table);
				$res = $db->autoExecute($products_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$id = $params->getInt("id");
				$res = $db->autoExecute($products_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
            
            if ( $fields["product_type_id"] ){
                $product_type_id = $fields["product_type_id"];
                $attributes = $db->getAll("SELECT 
                                                attributes.id AS id,
                                                attributes.name_{$lng} AS name
                                            FROM ".$attributes_table." AS attributes,
                                                ".$product_type_to_attribute_table." AS ptta
                                            WHERE 
                                                attributes.edate = 0 
                                            AND ptta.product_type_id = '".$product_type_id."' 
                                            AND ptta.attribute_id = attributes.id
                                            ORDER BY attributes.pos"); safeCheck($attributes);
                $del = $db->Query("DELETE FROM ".$product_to_attribute_option_table." WHERE product_id = '".$id."'"); safeCheck($del);
                foreach($attributes as $k => $v){
                    $values = $params->get("attribute_".$v["id"]."");
                    if($values){
                        foreach ($values as $kk => $vv) {
                            $res = $db->Query("INSERT INTO ".$product_to_attribute_option_table." (product_id, attribute_id, attribute_option_id) VALUES ('".$id."','".$v["id"]."','".$vv."')"); safeCheck($res);
                        }
                    }
                }
            }
            
            $option_groups = $_REQUEST["option_groups"];
            
            $res = $db->autoExecute($variants_table, array( "edate" => time() ), DB_AUTOQUERY_UPDATE, "product_id = '".$id."' "); safeCheck($res);
            
            if($option_groups){
                foreach( $option_groups as $k => $v ){
                    
                    $option_groups_options = $_REQUEST["option_groups_options_".(int)$v];
                    if($option_groups_options){
                        foreach($option_groups_options as $kk => $vv){
                            $option_group_id = (int)$v;
                            $option_group_option_id = (int)$vv;

                            $option_id = $option_group_option_id;
                            $code = $_REQUEST["option_code_".$option_group_id."_".$option_id.""];
                            $price_supply = (double)$_REQUEST["option_price_supply_".$option_group_id."_".$option_id.""];
                            $price = (double)$_REQUEST["option_price_".$option_group_id."_".$option_id.""];
                            $bonus_points = (int)$_REQUEST["option_bonus_points_".$option_group_id."_".$option_id.""];
                            $weight = (double)$_REQUEST["option_weight_".$option_group_id."_".$option_id.""];
                            $quantity = (int)$_REQUEST["option_quantity_".$option_group_id."_".$option_id.""];

                            $option_default = (int)$_REQUEST["option_default_".$option_group_id];
                            $is_default = 0;
                            if ( $option_default == $option_id ){
                                $is_default = 1;
                            }

                            $fields_tmp = array(
                                                "product_id" => $id,
                                                "option_group_id" => $option_group_id,
                                                "option_id" => $option_id,
                                                "code" => $code,
                                                "price_supply" => $price_supply,
                                                "price" => $price,
                                                "bonus_points" => $bonus_points,
                                                "weight" => $weight,
                                                "quantity" => $quantity,
                                                "is_default" => $is_default,
                                                "edate" => 0
                                            );
                            $check = $db->getRow("SELECT * FROM ".$variants_table." WHERE option_group_id = '".$option_group_id."' AND option_id = '".$option_id."' AND product_id = '".$id."' "); 
                            
                            if ( $check["id"] ){
//                                echo "<pre>";
//                                var_dump($fields_tmp);
//                                echo "</pre>";
                                
                                $res = $db->autoExecute($variants_table, $fields_tmp, DB_AUTOQUERY_UPDATE, " id = '".$check["id"]."' "); safeCheck($res);
                            }else{
                                if ( $option_id ){
                                    $res = $db->autoExecute($variants_table, $fields_tmp, DB_AUTOQUERY_INSERT); safeCheck($res);
                                }else{
                                    $check = $db->getRow("SELECT * FROM ".$variants_table." WHERE option_group_id = '".$option_group_id."' AND product_id = '".$id."' AND edate = 0"); 
                                    $res = $db->autoExecute($variants_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, " id = '".$check["id"]."' "); safeCheck($res);
                                }
                            }
                        }
                    }
                }
            }
            
            $res = $db->Query("DELETE FROM ".$product_to_category_table." WHERE product_id = '{$id}'"); safeCheck($res);
            $categoriesRequest = $params->get("categories");
            if ($categoriesRequest){
                foreach($categoriesRequest as $k=>$v){
                    $res = $db->Query("INSERT INTO ".$product_to_category_table." (product_id, category_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
                }
            }
            
            //$res = $db->Query("DELETE FROM ".$product_to_product_table." WHERE main_product_id = '{$id}'"); safeCheck($res);
            if($productsRequest = explode(',', $_REQUEST['products'] ) ){
                $del = $db->Query("DELETE FROM {$product_to_product_table} WHERE main_product_id = '{$id}'"); safeCheck($del);
                foreach($productsRequest as $v){
                    $res = $db->Query("INSERT INTO ". $product_to_product_table . " (product_id, main_product_id) VALUES(" . $v . ", ".$id.")"); 
                }
            }
            
            
//            $res = $db->Query("DELETE FROM ".$category_to_category_type_table." WHERE product_id = '{$id}'"); safeCheck($res);
//            $category_typesRequest = $params->get("category_types");
//            if ($category_typesRequest && sizeof($category_typesRequest)){
//                foreach($category_typesRequest as $k=>$v){
//                    $res = $db->Query("INSERT INTO ".$category_to_category_type_table." (product_id, category_type_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
//                }
//            }
//            
//            $res = $db->Query("DELETE FROM ".$category_to_category_info_table." WHERE product_id = '{$id}'"); safeCheck($res);
//            $category_infoRequest = $params->get("products_info");
//            
//            if ($category_infoRequest && sizeof($category_infoRequest)){
//                foreach($category_infoRequest as $v){
//                    $res = $db->Query("INSERT INTO ".$category_to_category_info_table." (product_id, category_info_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
//                }
//            }
//            
//            $res = $db->Query("DELETE FROM ".$category_to_category_table." WHERE category_main_id = '{$id}'"); safeCheck($res);
//            $productsRequest = $params->get("products");
//            if ($productsRequest && sizeof($productsRequest)){
//                foreach($productsRequest as $k=>$v){
//                    $res = $db->Query("INSERT INTO ".$category_to_category_table." (category_main_id, product_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
//                }
//            }
//            
//            $res = $db->Query("DELETE FROM ".$products_to_products_top_table." WHERE product_id = '{$id}'"); safeCheck($res);
//            $productsRequest = explode(",",$params->get("products"));
//            if (sizeof($productsRequest)){
//                foreach($productsRequest as $k=>$v){
//                    $res = $db->Query("INSERT INTO ".$products_to_products_top_table." (product_id, product_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
//                }
//            }
			
			$htaccessUpdate = new Settings();
			$htaccess_type = "products";
			
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
        
		public function copyRelatedProducts(int $copy_from_id, int $copy_to_id){
			global $db;
            global $product_to_product_table;
            
            if(($copy_from_id > 0 && $copy_to_id > 0) == false){
                return false;
            }
            
            $resPtp = $db->getAll("SELECT * FROM ".$product_to_product_table." WHERE main_product_id = '".$copy_from_id."'"); safeCheck($resPtp);
            $del = $db->Query("DELETE FROM ".$product_to_product_table." WHERE main_product_id = {$copy_to_id}"); safeCheck($del);
            if ($resPtp){
                foreach($resPtp as $k => $v){
                    if($v["product_id"] == $copy_to_id){
                        //unset($resPtp[$k]);
                        continue;
                    }
                    unset($v['id']);
                    unset($v['pos']);
                    $v["main_product_id"] = $copy_to_id;
                    $sqlPtp = " INSERT INTO {$product_to_product_table}";
                    $sqlPtp .= " ( " .implode(", ",array_keys($v)).") ";
                    $sqlPtp .= " VALUES ('".implode("', '",array_values($v)). "')";
                    $res = $db->Query($sqlPtp); safeCheck($res);
                }
            }
            
            return true;
		}
        
		public function copyVariants(int $copy_from_id, int $copy_to_id){
			global $db;
            global $variants_table;
            
            if(($copy_from_id > 0 && $copy_to_id > 0) == false){
                return false;
            }
            
            $option_groups = $db->getAll("SELECT * FROM ".$variants_table." WHERE product_id = '".$copy_from_id."' AND edate = 0"); safeCheck($option_groups);
            $del = $db->Query("DELETE FROM ".$variants_table." WHERE product_id = '{$copy_to_id}' AND edate = 0"); safeCheck($del);
            if($option_groups){
                foreach( $option_groups as $v ){
                    unset($v['id']);
                    unset($v['availability']);
                    unset($v['availability_label_id']);
                    unset($v['pos']);
                    unset($v['edate']);
                    $v["product_id"] = $copy_to_id;
                    $sqlVariants = " INSERT INTO {$variants_table}";
                    $sqlVariants .= " ( " .implode(", ",array_keys($v)).") ";
                    $sqlVariants .= " VALUES ('".implode("', '",array_values($v)). "')";
                    
                    $resVariants = $db->query($sqlVariants); safeCheck($resVariants);
                }
            }
            
            return true;
		}
        
		public function cloneProduct(int $id){
			global $db;
			global $lng;
            global $products_table;
            global $products_images_table;
            global $product_to_attribute_option_table;
            global $product_to_category_table;
            global $product_to_product_table;
            global $variants_table;
            
            $result = $db->getRow("SELECT * FROM {$products_table}  WHERE id = " . $id);
            unset($result['id']);
            unset($result['last_update']);
            unset($result['url_bg']);
            unset($result['url_en']);
            unset($result['url_de']);
            unset($result['url_ru']);
            unset($result['url_ro']);
            unset($result['htaccess_url_bg']);
            unset($result['htaccess_url_en']);
            unset($result['htaccess_url_de']);
            unset($result['htaccess_url_ru']);
            unset($result['htaccess_url_ro']);
            
            $result["name_{$lng}"] = $result["name_{$lng}"]." - Copy";
            $result["postdate"] = time();
            
            $sql = " INSERT INTO {$products_table}";
            $sql .= " ( " .implode(", ",array_keys($result)).") ";
            $sql .= " VALUES ('".implode("', '",array_values($result)). "')";
            
            $res = $db->query($sql); safeCheck($res);
            $newId = mysqli_insert_id($db->connection);
            
            if($newId > 0 == false){
                exit("Възникна проблем и продуктът не беше дублиран!");
            }
            
            $resImages = $db->getAll("SELECT * FROM ".$products_images_table." WHERE product_id = '".$id."' AND edate = 0"); safeCheck($resImages);
            if($resImages){
                foreach($resImages as $v){
                    unset($v['id']);
                    unset($v['pos']);
                    unset($v['last_update']);
                    unset($v['edate']);
                    $v["product_id"] = $newId;
                    $sqlImages = " INSERT INTO {$products_images_table}";
                    $sqlImages .= " ( " .implode(", ",array_keys($v)).") ";
                    $sqlImages .= " VALUES ('".implode("', '",array_values($v)). "')";
                    
                    $resImage = $db->query($sqlImages); safeCheck($resImage);
                }
            }
            
            $resAttributes = $db->getAll("SELECT * FROM ".$product_to_attribute_option_table." WHERE product_id = '".$id."' AND edate = 0"); safeCheck($resAttributes);
            if($resAttributes){
                foreach($resAttributes as $v){
                    unset($v['id']);
                    unset($v['value']);
                    unset($v['pos']);
                    unset($v['edate']);
                    $v["product_id"] = $newId;
                    $sqlAttributes = " INSERT INTO {$product_to_attribute_option_table}";
                    $sqlAttributes .= " ( " .implode(", ",array_keys($v)).") ";
                    $sqlAttributes .= " VALUES ('".implode("', '",array_values($v)). "')";
                    
                    $resAttribute = $db->query($sqlAttributes); safeCheck($resAttribute);
                }
            }
            
            $option_groups = $db->getAll("SELECT * FROM ".$variants_table." WHERE product_id = '".$id."' AND edate = 0"); safeCheck($option_groups);
            if($option_groups){
                foreach( $option_groups as $v ){
                    unset($v['id']);
                    unset($v['availability']);
                    unset($v['availability_label_id']);
                    unset($v['pos']);
                    unset($v['edate']);
                    $v["product_id"] = $newId;
                    $sqlVariants = " INSERT INTO {$variants_table}";
                    $sqlVariants .= " ( " .implode(", ",array_keys($v)).") ";
                    $sqlVariants .= " VALUES ('".implode("', '",array_values($v)). "')";
                    
                    $resVariants = $db->query($sqlVariants); safeCheck($resVariants);
                }
            }
            
            $resCategories = $db->getAll("SELECT * FROM ".$product_to_category_table." WHERE product_id = '".$id."'"); safeCheck($resCategories);
            if ($resCategories){
                foreach($resCategories as $v){
                    unset($v['id']);
                    unset($v['pos']);
                    $v["product_id"] = $newId;
                    $sqlCategories = " INSERT INTO {$product_to_category_table}";
                    $sqlCategories .= " ( " .implode(", ",array_keys($v)).") ";
                    $sqlCategories .= " VALUES ('".implode("', '",array_values($v)). "')";
                    $res = $db->Query($sqlCategories); safeCheck($res);
                }
            }
            
            $resPtp = $db->getAll("SELECT * FROM ".$product_to_product_table." WHERE main_product_id = '".$id."'"); safeCheck($resPtp);
            if ($resPtp){
                foreach($resPtp as $v){
                    unset($v['id']);
                    unset($v['pos']);
                    $v["main_product_id"] = $newId;
                    $sqlPtp = " INSERT INTO {$product_to_product_table}";
                    $sqlPtp .= " ( " .implode(", ",array_keys($v)).") ";
                    $sqlPtp .= " VALUES ('".implode("', '",array_values($v)). "')";
                    $res = $db->Query($sqlPtp); safeCheck($res);
                }
            }
            
            return $id;
		}
		
        //---------- Images -----------------------------
        function getImages($product_id){
			global $db;
			global $products_images_table;
			$images = $db->getAll("SELECT * FROM ".$products_images_table." WHERE edate = 0 AND product_id = '".$product_id."' ORDER BY pos"); safeCheck($images);
			return $images;
		}
        
        function getImageForm($id){
			global $db;
			global $sm;
			global $products_images_table;
			
			$row = $db->getRow("SELECT * FROM ".$products_images_table." WHERE id = '".$id."' ORDER BY pos"); safeCheck($row);
			$sm->assign("row", $row);
			
			$sm->display("admin/products_images.html");
		}
        
		function postImage($file = "", $product_id = 0){
			global $db;
			global $products_images_table;
			
			$fields = array(
								"pic" => $file,
								"product_id" => $product_id,
								"postdate" => time(),
							);
			shiftPos($db, $products_images_table);
			$res = $db->autoExecute($products_images_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}

        function updateImage($params){
			global $db;
			global $products_images_table;
			
			$id = $params->getInt("id");
			
			$fields = array(
								'name_bg'        => $params->getString("name_bg"),
								'name_en'        => $params->getString("name_en"),
								'name_de'        => $params->getString("name_de"),
								'name_ru'        => $params->getString("name_ru"),
								'name_ro'        => $params->getString("name_ro"),
								'last_update'	=> time(),
							);
			$res = $db->autoExecute($products_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function deleteImage($id){
			global $db;
			global $products_images_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($products_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
        
        //------- Files --------------------------------------
		function getFiles($product_id){
			global $db;
			global $lng;
			global $products_files_table;
			
			$files = $db->getAll("SELECT *, name_$lng AS name FROM ".$products_files_table." WHERE edate = 0 AND product_id = '".$product_id."' ORDER BY pos"); safeCheck($files);
			return $files;
		}
        
        function getFileForm($id){
			global $db;
			global $sm;
			global $lng;
			global $products_files_table;
			
			$row = $db->getRow("SELECT * FROM ".$products_files_table." WHERE id = '".$id."' ORDER BY pos"); safeCheck($row);
			$sm->assign("row", $row);
			
			$sm->display("admin/products_files.html");
		}
        
        function postFile($file = "", $product_id = 0){
			global $db;
			global $products_files_table;
			
			$fields = array(
								"doc" => $file,
								"product_id" => $product_id,
								"postdate" => time(),
							);
			shiftPos($db, $products_files_table);
			$res = $db->autoExecute($products_files_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}
        
        function updateFile($params){
			global $db;
			global $products_files_table;
			
			$id = $params->getString("id");
			
			$fields = array(
								'name_bg'	=> $params->getString('name_bg'),
								'name_en'	=> $params->getString('name_en'),
								'name_de'	=> $params->getString('name_de'),
								'name_ru'	=> $params->getString('name_ru'),
								'name_ro'	=> $params->getString('name_ro'),
							);
			$res = $db->autoExecute($products_files_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function deleteFile($id){
			global $db;
			global $products_files_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($products_files_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		
		function getProducts($options = array()){
			global $db;
			global $lng;
			global $products_table;
			
			$brand_id = $options->getInt('brand_id');
			if ( $brand_id ){
				$sql_where .= " AND brand_id = '".$brand_id."' ";
			}
			
			$products = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$products_table." WHERE edate = 0 {$sql_where} ORDER BY pos"); safeCheck($products);
			
			return $products;
		}
		
		function deleteRecord($id){
			global $db;
			global $products_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($products_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
        
        function deleteField($id, $field){
			global $db;
			global $products_table;
			
			$res = $db->autoExecute($products_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
        
        public static function updateField($id, $field, $value){
			global $db;
			global $products_table;
			
			$res = $db->autoExecute($products_table, array( $field => $value ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
        
        public static function getVariantDetails(FilteredMap $params) {
            global $db;
            global $variants_table;
            global $products_table;
            
            $product_id = $params->getInt("product_id");
            $variant_id = $params->getInt("variant_id");
            $cart_id = $params->getInt("cart_id");
            
            $price_special_offer = getSpecialOfferPrice($product_id, 0, 0, $variant_id);
            
            $helpers = new Helpers();
            $users = new Users();
            $cartsObj = new Carts();
            $cart = $cartsObj->getRecord($cart_id);
            $user_id = $cart["user_id"];
            $user = $user_id > 0 ? $users->getRecord($user_id) : array();
            $user_group_id = isset($user["user_group_id"]) && $user["user_group_id"] > 0 ? $user["user_group_id"] : 0;
            
            if($variant_id){
                $row = $db->getRow("SELECT * FROM ".$variants_table." WHERE option_id = '".$variant_id."' AND product_id = '".$product_id."' AND edate = 0"); safeCheck($row);
            }else{
                $row = $db->getRow("SELECT p.price, p.weight FROM ".$products_table." AS p WHERE p.id = ".$product_id. " AND p.edate = 0"); safeCheck($row);
            }
            if ( $price_special_offer > 0.0 ){
                $row["price_strikethrough"] = $helpers->getDiscountedPrice($row["price"], 1, $user_group_id);
                $row["price"] = $helpers->getDiscountedPrice($price_special_offer, 1, $user_group_id);
            }else{
                $row["price"] = $helpers->getDiscountedPrice($row["price"], 0, $user_group_id);
            }
//            if ( $_SERVER["REMOTE_ADDR"] == "84.201.192.58" ){
//                // dbg($row);
//                // dbg($_REQUEST);
//            }
            
            $isFreeDelivery = false;
            if($row["weight"] > 0.0){
                $isFreeDelivery = CartDiscounts::isFreeDelivery((float)$row["price"], (float)$row["weight"]);
            }
            $row["isFreeDelivery"] = $isFreeDelivery;
//            $row["bonus_points_price"] = $row["price"] * $bonus_points_to_sell;
//            $row["bonus_points_won"] = round($row["price"],0);
            return $row;
        }
	}
	
?>