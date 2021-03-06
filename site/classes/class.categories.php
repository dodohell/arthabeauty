<?php
	class Categories extends Settings{
		public function getRecord(int $id){
			global $db;
			global $lng;
			global $categories_table;
			
			$row = $db->getRow("SELECT 
                                    *,
                                    name_{$lng} AS name,
                                    h1_{$lng} AS h1,
                                    excerpt_{$lng} AS excerpt,
                                    description_{$lng} AS description,
                                    pic_1_name_{$lng} AS pic_1_name,
                                    pic_2_name_{$lng} AS pic_2_name,
                                    htaccess_url_{$lng} AS htaccess_url,
                                    meta_keywords_{$lng} AS meta_keywords,
                                    meta_title_{$lng} AS meta_title,
                                    meta_description_{$lng} AS meta_description
								FROM ".$categories_table."
								WHERE id = '{$id}'
								"); safeCheck($row);
			return $row;
		}
		
		public function getImages($category_id){
			global $db;
			global $categories_images_table;
			
			$images = $db->getAll("SELECT * FROM ".$categories_images_table." WHERE edate = 0 AND category_id = '".$category_id."' ORDER BY pos"); safeCheck($images);
			return $images;
		}
		
		public function getCategories($noSubcategories = false){
			global $db;
			global $lng;
			global $categories_table;
            
			$categories = $db->getAll("SELECT *, 
										 name_{$lng} AS name,
										 h1_{$lng} AS h1,
										 url_{$lng} AS url,
										 excerpt_{$lng} AS excerpt,
										 url_target AS target,
										 htaccess_url_{$lng} AS htaccess_url
								  FROM ".$categories_table." 
								  WHERE edate = 0
                                  AND active = 1
								  AND name_{$lng} <> ''
								  AND category_id = 0 
								  ORDER BY pos"); safeCheck($categories);
            if($noSubcategories === false){
                foreach($categories as $k => $v){
                    $v["subcategories"] = $this->getSubcategories($v["id"], 1);
                    
                    $categories[$k] = $v;
                }
            }
			return $categories;
		}
		
		public function getSubcategories($id, $level = 0){
			global $db;
			global $lng;
			global $categories_table;
			
			$subcategories = $db->getAll("SELECT *, 
											name_{$lng} AS name,
											excerpt_{$lng} AS excerpt,
											url_{$lng} AS url,
											url_target AS target,
											htaccess_url_{$lng} AS htaccess_url
									 FROM ".$categories_table." 
									 WHERE edate = 0 
                                     AND active = 1
									 AND category_id = '".$id."' 
									 AND name_{$lng} <> ''
									 ORDER BY pos"); safeCheck($subcategories);
			
			foreach($subcategories as $k => $v){
				$v["subcategories"] = $this->getSubcategories($v["id"], $level+1);
				$v["level"] = $level;
				$subcategories[$k] = $v;
			}
			
			return $subcategories;
		}
		
		public static function generateBreadcrumbs($id, $tmp_breadcrumbs = ""){
			global $db;
			global $sm;
			global $lng;
			global $link_find;
			global $link_repl;
			global $host;
			global $language_file;
			global $htaccess_file;
			global $categories_table;
			global $left;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$row = $db->getRow("SELECT id, category_id, name_{$lng} AS name, url_{$lng} AS url, url_target AS target, htaccess_url_{$lng} AS htaccess_url FROM ".$categories_table." WHERE id = '".$id."'"); safeCheck($row);
			$row['link_title'] = str_replace($link_find, $link_repl, $row['name']);
			
			$htaccess_prefix = $htaccessVars["htaccess_categories"];
			if ( $row["url"] ){
				$tmp_breadcrumbs = '<a href="'.$row["url"].'" target="'.$row["target"].'">'.$row["name"].'</a> <span>|</span>'.$tmp_breadcrumbs;
			}elseif( $row["htaccess_url"] ){
				$tmp_breadcrumbs = '<a href="'.$row["htaccess_url"].'" target="'.$row["target"].'">'.$row["name"].'</a> <span>|</span>'.$tmp_breadcrumbs;
			}else{
				$tmp_breadcrumbs = '<a href="/'.$htaccess_prefix.'/'.$row["id"].'" target="'.$row["target"].'">'.$row["name"].'</a> <span>|</span>'.$tmp_breadcrumbs;
			}
			if ($row["category_id"] != 0){
				return self::generateBreadcrumbs($row["category_id"], $tmp_breadcrumbs);
			}else{
				return '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>'.$tmp_breadcrumbs;
			}
		}

		public function productsToCategories($id) {
			global $db;
            global $product_to_category_table;
			
			$products = array();
			$productsToCategory = $db->getAll("SELECT product_id FROM ".$product_to_category_table." WHERE category_id = $id"); safeCheck($productToCategory);

			foreach($productsToCategory as $productToCategory) {
				$products[] = $productToCategory['product_id'];
			}

			return $products;
		}

		public function brandsToCategories($id) {
			global $db;
			global $brand_to_category_table;
			
			$brands = array();
			$brandsToCategory = $db->getAll("SELECT brand_id FROM ".$brand_to_category_table." WHERE category_id = $id"); safeCheck($brandToCategory);

			foreach($brandsToCategory as $brandToCategory) {
				$brands[] = $brandToCategory['brand_id'];
			}

			return $brands;
		}

		public function collectionsToCategories($id) {
			global $db;
			global $collection_to_category_table;

			$collections = array();
			$collectionsToCategory = $db->getAll("SELECT collection_id FROM ".$collection_to_category_table." WHERE category_id = $id"); safeCheck($collectionToCategory);
			
			foreach($collectionsToCategory as $collectionToCategory) {
				$collections[] = $collectionToCategory['collection_id'];
			}

			return $collections;
		}
		
		public function getPage(int $id, $page = 0, FilteredMap $params){
            global $db;
			global $sm;
			global $lng;
			global $user;
			global $categories_table;
            global $products_table;
            global $favourites_table;
			global $product_to_category_table;
			global $brand_to_category_table;
			global $collection_to_category_table;
            global $products_images_table;
            global $product_types_table;
            global $products_comments_table;
            global $attributes_table;
            global $product_to_attribute_option_table;
            global $attributes_to_attribute_options_table;
            global $brands_table;
			global $description;
			global $language_file;
			global $htaccess_file;
			global $htaccess_file_bg;
			global $htaccess_file_en;
			global $htaccess_file_de;
			global $htaccess_file_ru;
			global $htaccess_file_ro;
			global $limit;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
//            $code = $params->getString("code");
//            $sm->assign("code", $code);
            
            $children = $db->getAll("SELECT * FROM ".$categories_table." WHERE edate = 0 AND category_id = ".$id); safeCheck($children);
            if ($children && count($children) > 0 ){
                $sql_ptc_join_in .= " ( ".$id;
                foreach($children as $k => $v){
                    $sql_ptc_join_in .= ",".$v["id"];
                }
                $sql_ptc_join_in .= " ) ";
            }else{
                $sql_ptc_join_in .= " ( ".$id.")";
			}
			
			$products = $this->productsToCategories($id);
			$brands = $this->brandsToCategories($id);
			$collections = $this->collectionsToCategories($id);

            $row = $this->getRecord($id);

            if($row["edate"] > 0 || (int)$row["active"] != 1){
            	header("HTTP/1.0 404 Not Found");
							header("Location: /messages/100");
							die();
            	
            }
            
            $sm->configLoad($htaccess_file);
            $htaccessVars = $sm->getConfigVars();

            $show = $params->has("show") ? $params->getString("show") : 24;
            $sm->assign("show", $show);

            $sort_by = $params->has("sort_by") ? $params->getString("sort_by") : "";
            $sm->assign("sort_by", $sort_by);
            $sql_order_by = " ORDER BY products.price ASC ";
            if($sort_by == "price-asc"){
                $sql_order_by = " ORDER BY products.price ASC";
            }else if($sort_by == "price-desc"){
                $sql_order_by = " ORDER BY products.price DESC ";
            }else if($sort_by == "postdate-asc"){
                $sql_order_by = " ORDER BY products.postdate ASC ";
            }else if($sort_by == "postdate-desc"){
                $sql_order_by = " ORDER BY products.postdate DESC ";
            }
            
            if($sort_by == "price-asc"){
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"]."/price-asc" : "/".$htaccessVars["htaccess_categories"]."/".$id."/price-asc";
            }else if($sort_by == "price-desc"){
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"]."/price-desc" : "/".$htaccessVars["htaccess_categories"]."/".$id."/price-desc";
            }else if($sort_by == "postdate-asc"){
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"]."/postdate-asc" : "/".$htaccessVars["htaccess_categories"]."/".$id."/postdate-asc";
            }else if($sort_by == "postdate-desc"){
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"]."/postdate-desc" : "/".$htaccessVars["htaccess_categories"]."/".$id."/postdate-desc";
            }else{
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"] : "/".$htaccessVars["htaccess_categories"]."/".$id;
            }
            
            $current_page = is_numeric($page) && $page > 0 ? (int)$page : 1;
            $limit = (int)$show;
            
            $sqlCount = "SELECT 
							COUNT(DISTINCT products.id) as cntr
						FROM 
							".$products_table." AS products
						".(!$products && !$brands && !$collections ? 'INNER' : 'LEFT')." JOIN {$product_to_category_table} AS ptc ON ptc.product_id = products.id AND ptc.category_id IN {$sql_ptc_join_in}
						WHERE
							products.edate = 0
						AND products.active = 1";
			if($products || $brands || $collections) {
				$sqlCount .= " AND (";
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
			}
			$sqlCount .= "{$sql_order_by} {$sql_limit}";
			
			$results = $db->getRow($sqlCount); safeCheck($results);

			$resultsCount = (int)$results["cntr"];
			
            $sm->assign("resultsCount", $resultsCount);
            
            $total_pages = (int)ceil($resultsCount/$limit); //break records into pages
            $start = (($current_page-1) * $limit); //get starting position
            $sql_limit = " LIMIT {$start}, {$limit}";
            $pages = Helpers::paginate($current_page, $total_pages, $page_url);
            $sm->assign("pages", $pages);
            
            $sqlItems = "SELECT DISTINCT
                            products.*,
                            products.name_{$lng} AS name,
                            products.htaccess_url_{$lng} AS htaccess_url,
                            products.excerpt_{$lng} AS excerpt,
                            products.htaccess_url_{$lng} AS htaccess_url,
                            products.description_{$lng} AS description,
                            products.meta_title_{$lng} AS meta_title,
                            products.meta_keywords_{$lng} AS meta_keywords,
                            products.meta_description_{$lng} AS meta_description,
                            products.meta_metatags_{$lng} AS meta_metatags, 
                            (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
                            (SELECT pi.pic FROM " . $products_images_table . " AS pi WHERE pi.product_id = products.id AND pi.edate = 0 ORDER BY pi.pos LIMIT 1) as mainPic,
                            (SELECT product_types.name_{$lng} FROM ".$product_types_table." AS product_types WHERE products.product_type_id = product_types.id) AS product_type_name,
                            (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id) AS rating,
                            (SELECT COUNT(rating2.id) FROM ".$products_comments_table." AS rating2 WHERE rating2.product_id = products.id AND rating2.edate = 0) AS reviews_count
                        FROM 
                            ".$products_table." AS products
						".(!$products && !$brands && !$collections ? 'INNER' : 'LEFT')." JOIN {$product_to_category_table} AS ptc ON ptc.product_id = products.id AND ptc.category_id IN {$sql_ptc_join_in}
                        WHERE
                            products.edate = 0
                        AND products.active = 1";
			
			if($products || $brands || $collections) {
				$sqlItems .= " AND (";
				if(!empty($products)) {
					$sql_in_ptc = implode(",", $products);
					$sqlItems .= " products.id IN ({$sql_in_ptc})";
				}
				if(!empty($brands)) {
					$condition = (!$products) ? '' : 'OR'; 
					$sql_in_btc = implode(",", $brands);	
					$sqlItems .= " {$condition} products.brand_id IN ({$sql_in_btc})";
				}
				if(!empty($collections)) {
					$condition = (!$products && !$brands) ? '' : 'OR'; 
					$sql_in_ctc = implode(",", $collections);
					$sqlItems .= " {$condition} products.collection_id IN ({$sql_in_ctc})";
				}
				$sqlItems .= ") ";
			}
			$sqlItems .= "{$sql_order_by} {$sql_limit}";

            $items = $db->getAll($sqlItems); safeCheck($items);
            $helpers = new Helpers();
            $user_group_id = Helpers::getCurentUserGroupId();
            
            foreach ($items as $k => $v) {
                $price_specialoffer = getSpecialOfferPrice($v["id"], $v["brand_id"], 1);
                
                if ( $price_specialoffer["price_specialoffer"] == $v["price"] ){
                    $v["price"] = $helpers->getDiscountedPrice($v["price"], 0, $user_group_id);
                    $v["price_specialoffer"] = 0.0;
                    $v["price_specialoffer_text"] = $price_specialoffer["price_specialoffer_text"];
                    $v["bonus_points"] = $price_specialoffer["bonus_points"];
                    $v["bonus_points_win"] = round($price_specialoffer["price_specialoffer"] * 1, 0) ;
                    $v["discount_date_to"] = $price_specialoffer["discount_date_to"];
                    $v["discountPic"] = $price_specialoffer["pic"];
                }else if ( $price_specialoffer["price_specialoffer"] > 0.0 ){
                    $v["price"] = $helpers->getDiscountedPrice($v["price"], 1, $user_group_id);
                    $v["price_specialoffer"] = $helpers->getDiscountedPrice($price_specialoffer["price_specialoffer"], 1, $user_group_id);
                    $v["price_specialoffer_text"] = $price_specialoffer["price_specialoffer_text"];
                    $v["bonus_points"] = $price_specialoffer["bonus_points"];
                    $v["bonus_points_win"] = round($price_specialoffer["price_specialoffer"] * 1, 0) ;
                    $v["discount_date_to"] = $price_specialoffer["discount_date_to"];
                    $v["discountPic"] = $price_specialoffer["pic"];
                }else{
                    $v["price"] = $helpers->getDiscountedPrice($v["price"], 0, $user_group_id);
                    $v["bonus_points_win"] = round($v["price"] * 1, 0);
                    $v["discountPic"] = null;
                }
                
                if ( $user["id"] ){
					$check = $db->getRow("SELECT * FROM ".$favourites_table." WHERE edate = 0 AND product_id = ".$v["id"]." AND user_id = ".$user["id"]); safeCheck($check);
					if ( $check["id"] ){
						$v["in_favourites"] = 1;
					}else{
						$v["in_favourites"] = 0;
					}
				}
                
                $v["excerpt"] = htmlspecialchars_decode($v["excerpt"]);
                $v["description"] = htmlspecialchars_decode($v["description"]);

                $number_of_options = 0;
                $quantity = 0;
                $display_options_as_buttons = 0;
                $tmp_counter = 0;

                $option_groups = Products::getOptionGroups($v["id"]);
                foreach($option_groups as $key => $value){
                    foreach( $value['options'] as $kk => $vv ){
                        if ( $v['info']['buttons'] ){
                            $display_options_as_buttons = 1;
                        }
                        if ( $vv['selected_values'] ){
                            $number_of_options++;
                            $quantity += $vv['selected_values']['quantity'];
                        }
                    }
                }
                $v["option_groups"] = $option_groups;
                $v["display_options_as_buttons"] = $display_options_as_buttons;
                $v["number_of_options"] = $number_of_options;
                if($number_of_options == 0){
                    $quantity = $v["quantity"];
                }
                $v["quantity"] = $quantity;

                $items[$k] = $v;
            }

            $sm->assign("items", $items);
            
			
			// START OF FILTERS SECTION
			$showCatSubcatProducts = is_numeric($row["show_cat_subcat_products"]) ? (int)$row["show_cat_subcat_products"] : 0;
			if($showCatSubcatProducts){
				$subCategoriesArr = self::getSublevels($id);
				$subCategoriesArr[] = $id;
				$subCategoriesIds = implode(",", $subCategoriesArr);
				if(count($subCategoriesArr) > 1){
					$sqlSubCategories = " AND product_to_category.category_id IN ( $subCategoriesIds ) ";
				}else{
					$sqlSubCategories = " AND product_to_category.category_id = '{$id}' ";
				}
			}else{
				$sqlSubCategories = " AND product_to_category.category_id = '{$id}' ";
			}

			$sqlCondition = "";
			if($products || $brands || $collections) {
				$sqlCondition .= " AND (";
				if(!empty($products)) {
					$sql_in_ptc = implode(",", $products);
					$sqlCondition .= " products.id IN ({$sql_in_ptc})";
				}
				if(!empty($brands)) {
					$condition = (!$products) ? '' : 'OR'; 
					$sql_in_btc = implode(",", $brands);	
					$sqlCondition .= " {$condition} products.brand_id IN ({$sql_in_btc})";
				}
				if(!empty($collections)) {
					$condition = (!$products && !$brands) ? '' : 'OR'; 
					$sql_in_ctc = implode(",", $collections);
					$v .= " {$condition} products.collection_id IN ({$sql_in_ctc})";
				}
				$sqlCondition .= ") ";
			}
			
			$sm->assign("showCatSubcatProducts", $showCatSubcatProducts);
			
			$products_all = $db->getAll("SELECT products.id, products.price
								FROM ".$products_table." AS products 					  
								WHERE products.edate = 0
								
								$sqlCondition
								AND products.active = '1'
								"); safeCheck($products_all);

			$price_min = 20000;
			$price_max = 0;
			foreach($products_all as $k => $v){
				$product_ids[] = $v["id"];
				if ( $v["price"] > $price_max ){
					$price_max = $v["price"];
				}
				if ( $v["price"] < $price_min ){
					$price_min = $v["price"];
				}
			}
			$sm->assign("price_max", $price_max);
			$sm->assign("price_min", $price_min);
			
			
			if ($product_ids && sizeof($product_ids) > 0 ){
				if ( sizeof($product_ids) == 1 ){
					$sql_search_in_products = " product_id = '".$product_ids[0]."' ";
					$sql_search_in_products_id = " id = '".$product_ids[0]."' ";
				}else{
					$sql_search_in_products = " product_id IN (".implode(",", $product_ids).")";
					$sql_search_in_products_id = " id IN (".implode(",", $product_ids).")";
				}
				
				
				// GET DISTINCT PRODUCT ATTRIBUTE OPTIONS FROM WITHIN THE PRODUCT SELECTION TO CREATE LEFT HAND SIDE SEARCH
				$distinct_options = $db->getAll("SELECT DISTINCT attribute_option_id FROM ".$product_to_attribute_option_table." WHERE edate = 0 AND {$sql_search_in_products}"); safeCheck($distinct_options);
				foreach($distinct_options as $k => $v){
					if ( $v["attribute_option_id"] ){
						$attribute_options_ids[] = $v["attribute_option_id"];
					}
				}
				
				if ($attribute_options_ids && sizeof($attribute_options_ids) > 0 ){
					if ( sizeof($attribute_options_ids) == 1 ){
						$sql_search_in_attribute_options = " AND id = '".$attribute_options_ids[0]."' ";
					}else{
						$sql_search_in_attribute_options = " AND id IN (".implode(",", $attribute_options_ids).")";
					}
				}
				
				// GET DISTINCT PRODUCT ATTRIBUTES FROM WITHIN THE PRODUCT SELECTION TO CREATE LEFT HAND SIDE SEARCH
				$distinct_attributes = $db->getAll("SELECT DISTINCT attribute_id FROM ".$product_to_attribute_option_table." WHERE edate = 0 AND {$sql_search_in_products}"); safeCheck($distinct_attributes);
				foreach($distinct_attributes as $k => $v){
					$attribute_ids[] = $v["attribute_id"];
				}
				
				if ( sizeof($attribute_ids) > 0 ){
					if ( sizeof($attribute_ids) == 1 ){
						$sql_search_in_attributes = " AND id = '".$attribute_ids[0]."' ";
					}else{
						$sql_search_in_attributes = " AND id IN (".implode(",", $attribute_ids).")";
					}
					
					// GET DISTINCT PRODUCT ATTRIBUTES BASED ON THE ATTRIBUTES USED WITHIN THE PRODUCTS OF THIS CATEGORY
					$attributes = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$attributes_table." WHERE edate = 0 AND is_filterable = 1 {$sql_search_in_attributes} ORDER BY pos"); safeCheck($attributes);
					foreach($attributes as $k => $v){
						$attribute_options = $db->getAll("SELECT *, option_text_{$lng} AS option_text FROM ".$attributes_to_attribute_options_table." AS atao WHERE atao.attribute_id = '".$v["id"]."' {$sql_search_in_attribute_options} ORDER BY pos"); safeCheck($attribute_options);
						$v["attribute_options"] = $attribute_options;
						$attributes[$k] = $v;
					}
					$sm->assign("attributes", $attributes);
					// dbg($attributes);
				}
				
				$sql = "SELECT DISTINCT brand_id FROM ".$products_table." AS products WHERE edate = 0 AND {$sql_search_in_products_id}";
				$distinct_brands = $db->getAll($sql); safeCheck($distinct_brands);
				
				if ( sizeof($distinct_brands) > 0 ){
					if ( sizeof($distinct_brands) == 1 ){
						$sql_brands_select = " AND id = '".$distinct_brands[0]["brand_id"]."' ";
					}else{
						foreach($distinct_brands as $k => $v){
							if ( $v["brand_id"] ){
								$distinct_brands_implode[] = $v["brand_id"];
							}
						}
						
						$sql_brands_select = " AND id IN(".implode(",", $distinct_brands_implode).") ";
					}
				}
				$sql = "SELECT id, pic, name_{$lng} AS name, LEFT(name_{$lng}, 1) AS nameLetter FROM ".$brands_table." WHERE edate = 0 {$sql_brands_select} ORDER BY name_{$lng}";
				$sqlFirstLetter = "SELECT DISTINCT LEFT(name_{$lng}, 1) AS nameLetter FROM ".$brands_table." WHERE edate = 0 {$sql_brands_select} ORDER BY name_{$lng}";
				$brands_selected = $db->getAll($sql); safeCheck($brands_selected);
				$brands_letters = $db->getAll($sqlFirstLetter); safeCheck($brands_letters);
				
				$sm->assign("brands_selected", $brands_selected);
				$sm->assign("brands_letters", $brands_letters);
				
				
			}
			// END OF FILTERS SECTION
			
			if ( $params->getString("old_link") && $row["htaccess_url"] ){
				header ('HTTP/1.1 301 Moved Permanently');
				if ( $params->getInt("sort_by_type") ){
					header('Location: '.$row["htaccess_url"]."/".strtolower($params->getInt("sort_by_type")));
				}else{
					header('Location: '.$row["htaccess_url"]);
				}
                die();
			}
			$sm->assign("infoKeys", $row['meta_keywords']);
			if ( $page ){
				$meta_page = ", ".$configVars["page_short"]." ".$page;
				$meta_description_page = " ".$configVars["page_short"]." ".$page;
			}
			
			if ( $row["meta_title"] ){
				$sm->assign("infoTitle", $row['meta_title'].$meta_page);
			}else{
				$sm->assign("infoTitle", $row['name'].$meta_page);
			}
			if ( $row["meta_description"] ){
				$sm->assign("infoDescr", $row['meta_description'].$meta_description_page);
			}else{
				$sm->assign("infoDescr", $row['name'].". ".$description["description"].$meta_description_page);
			}
			
			
			$row["excerpt"] = htmlspecialchars_decode($row["excerpt"]);
			$row["description"] = $row["description"];	
			$sm->assign("row", $row);

			$breadcrumbs = self::generateBreadcrumbs($id);
			$sm->assign("breadcrumbs", $breadcrumbs);
			
			$s = $row["category_id"];
			if ($s){
				$category_id = $s;
			}else{
				$category_id = $row["id"];
			}
			if ($category_id == 0){
				$row = $db->getRow("SELECT id,
                                        name_{$lng} AS name,
                                        description_{$lng} AS description,
                                        pic_1,
                                        category_id
									FROM ".$categories_table."
									WHERE id = '{$id}'
									"); safeCheck($row);
				$category_id = $row["id"];
			}
			
			
			$sm->assign("pages", $pages);
			$sm->assign("page", $page);

			$sm->configLoad($htaccess_file_en);
			$htaccess_en = $sm->getConfigVars();
			if ( $row["htaccess_url_en"] ){
				$link_en = $row["htaccess_url_en"];
			}else{
				if ( $row["url_en"] ){
					$link_en = $row["url_en"];
				}else{
					$link_en = "/".$htaccess_en["htaccess_categories"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_ro);
			$htaccess_ro = $sm->getConfigVars();
			if ( $row["htaccess_url_ro"] ){
				$link_ro = $row["htaccess_url_ro"];
			}else{
				if ( $row["url_ro"] ){
					$link_ro = $row["url_ro"];
				}else{
					$link_ro = "/".$htaccess_ro["htaccess_categories"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_bg);
			$htaccess_bg = $sm->getConfigVars();
			if ( $row["htaccess_url_bg"] ){
				$link_bg = $row["htaccess_url_bg"];
			}else{
				if ( $row["url_bg"] ){
					$link_bg = $row["url_bg"];
				}else{
					$link_bg = "/".$htaccess_bg["htaccess_categories"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_de);
			$htaccess_de = $sm->getConfigVars();
			if ( $row["htaccess_url_de"] ){
				$link_de = "/".$row["htaccess_url_de"];
			}else{
				if ( $row["url_de"] ){
					$link_de = $row["url_de"];
				}else{
					$link_de = "/".$htaccess_de["htaccess_categories"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_ru);
			$htaccess_ru = $sm->getConfigVars();
			if ( $row["htaccess_url_ru"] ){
				$link_ru = "/".$row["htaccess_url_ru"];
			}else{
				if ( $row["url_ru"] ){
					$link_ru = $row["url_ru"];
				}else{
					$link_ru = "/".$htaccess_ru["htaccess_categories"]."/".$row["id"];
				}
			}
            
            $sm->configLoad($htaccess_file);
            
			$sm->assign("link_bg", $link_bg);
			$sm->assign("link_en", $link_en);
			$sm->assign("link_de", $link_de);
			$sm->assign("link_ru", $link_ru);
			$sm->assign("link_ro", $link_ro);
            
			$sm->assign("s", $_REQUEST["s"]);
			$sm->assign("breadcrumbs", $breadcrumbs);
			$sm->assign("filter_category_id", $id);
			$sm->assign("page_categories", 1);
            
            $filter_categories = $this->getCategories();
            $sm->assign("filter_categories", $filter_categories);
			
			
			$subcategories = $this->getSubcategories($id);
			$sm->assign("subcategories", $subcategories);
			
			$sm->display("categories.html");
		}
                
		public function contactToCategory($id, $options = array()){
			global $db;
			global $sm;
			global $lng;
			global $host;
			global $categories_table;
			
			$row = $db->getRow("SELECT *, id,
									name_{$lng} AS name,
									h1_{$lng} AS h1,
									excerpt_{$lng} AS excerpt,
									for_offer_{$lng} AS for_offer,
									description_{$lng} AS description,
									pic_1,
									pic_2,
									pic_1_name_{$lng} AS pic_1_name,
									pic_2_name_{$lng} AS pic_2_name,
									htaccess_url_{$lng} AS htaccess_url,
									contactsform,
									category_id,
									restricted,
									meta_keywords_{$lng} AS meta_keywords,
									meta_title_{$lng} AS meta_title,
									meta_description_{$lng} AS meta_description
								FROM ".$categories_table."
								WHERE id = '{$id}'
								"); safeCheck($row);
									
			$cityObj = new Cities();
			$cities = $cityObj->getCities();
			$sm->assign("cities", $cities);
			$sm->assign("row", $row);
			
			$sm->display("contact-to-category-ro.html");
		}
		
		
		public static function getSublevels($id, $sublevels = array()){
			global $db;
			global $categories_table;
			global $lng;

			$levels = $db->getAll("SELECT id
							  FROM " . $categories_table . "
							  WHERE category_id = " . $id . "
							  AND edate = 0
							  AND active = 'checked'
							  ORDER BY pos
							  "); safeCheck($levels);
		
			foreach($levels as $k=>$v){
				$sublevels[] = $v['id'];
				$sublevels = getSublevels($v["id"], $sublevels);
			}		
			return $sublevels;
		}
		
		public static function processAdvancedSearch(FilteredMap $params){
			global $db;
			global $lng;
			global $sm;
			global $user;
			global $install_path;
			global $language_file;
			global $htaccess_file;
			global $brands_table;
			global $attributes_table;
			global $attributes_to_attribute_options_table;
			global $products_table;
			global $products_images_table;
			global $products_comments_table;
			global $product_to_category_table;
			global $product_to_attribute_option_table;
			global $favourites_table;
			global $limit;
			
			$category_id = $params->getInt('category_id');
			$brand_id = $params->getInt('single_brand_id');
			$collection_id = $params->getInt('collection_id');
            
			$search_string = $params->getString('search_string');
			$search_page = $params->getInt('search_page');
            
            $sql_where_search = "";
            if($search_string){
                $sql_where_search = " AND (MATCH(products.name_en,products.name_{$lng}) AGAINST('{$search_string}') OR products.name_en LIKE '%{$search_string}%' OR products.name_{$lng} LIKE '%{$search_string}%' OR products.brand_id IN (SELECT id FROM {$brands_table} WHERE name_{$lng} LIKE '%{$search_string}%') OR products.barcode LIKE '%{$search_string}%' )";
            }
            
            $sm->assign("search_string", $search_string);

            $show = ($params->has("show") && $params->getString("show")) ? $params->getString("show") : 24;
            $sm->assign("show", $show);

			$page = $params->getInt('page');
			if(!$page){
				$page = 1;
			}
			$items_per_page = $show;
			if ( $page ){
				$start = ($page-1)*$items_per_page;
			}else{
				$start = 0;
			}
			
			
//			if ( $params->has("sortby") ){
//				$sortby = $params->getInt("sortby");
//				if ($sortby == 1)	{
//					$sortby_sql = " price ";	
//					$_SESSION["sortby"] = 1;
//				}
//				if ($sortby == 2){
//					$sortby_sql = " name_{$lng} ";	
//					$_SESSION["sortby"] = 2;
//				}
//				if ($sortby == 3){
//					$sortby_sql = " name_{$lng} ";
//					$_SESSION["sortby"] = 3;
//				}
//				if ($sortby == 4){
//					$sortby_sql = " products.id DESC ";
//					$_SESSION["sortby"] = 4;
//				}
//			}else{
//				$sortby = (int)$_SESSION["sortby"];
//				if ($sortby == 1 || $sortby == 0)	{
//					$sortby_sql = " price ";	
//					$_SESSION["sortby"] = 1;
//				}
//				if ($sortby == 2){
//					$sortby_sql = " code ";	
//					$_SESSION["sortby"] = 2;
//				}
//				if ($sortby == 3){
//					$sortby_sql = " name_{$lng} ";
//					$_SESSION["sortby"] = 3;
//				}
//				if ($sortby == 4){
//					$sortby_sql = " products.id DESC ";
//					$_SESSION["sortby"] = 4;
//				}
//			}

            $sort_by = $params->has("sort_by") ? $params->getString("sort_by") : "";
            $sm->assign("sort_by", $sort_by);
            $sql_order_by = " ORDER BY products.price ASC ";
            if($sort_by == "price-asc"){
                $sql_order_by = " ORDER BY products.price ASC";
            }else if($sort_by == "price-desc"){
                $sql_order_by = " ORDER BY products.price DESC ";
            }else if($sort_by == "postdate-asc"){
                $sql_order_by = " ORDER BY products.postdate ASC ";
            }else if($sort_by == "postdate-desc"){
                $sql_order_by = " ORDER BY products.postdate DESC ";
            }
			
			$results['sortby'] = $sortby;
            
			// CONFIGURE BRAND SEARCH
			$brand_id_request = array();
			if($params->has("brands") && is_array($params->get("brands"))){
				foreach($params->get("brands") as $k => $v){
					$remove = 0;
                    if($params->has("remove_brand_id") && is_array($params->get("remove_brand_id"))){
                        foreach($params->get("remove_brand_id") as $kk => $vv){
                            if ($vv == $v){
                                $remove = 1;
                            }
                        }
                    }
					if ($remove == 0){
						$brand_id_request[] = (int)$v;
						$query_string .= "&brands[]=".(int)$v;
					}
				}
			}
			if ( sizeof($brand_id_request) > 0 ){
				if ( sizeof($brand_id_request) == 1 ){
					$sql_brands = " AND products.brand_id = '".$brand_id_request[0]."' ";
					$sql_brands_select = " AND id = '".$brand_id_request[0]."' ";
					$sql_brands_select_not = " AND id != '".$brand_id_request[0]."' ";
				}else{
					$sql_brands = " AND products.brand_id IN (".implode(",", $brand_id_request).")";
					$sql_brands_select = " AND id IN (".implode(",", $brand_id_request).")";
					$sql_brands_select_not = " AND id NOT IN (".implode(",", $brand_id_request).")";
				}
				
				$sql = "SELECT id, name_{$lng} AS name FROM ".$brands_table." WHERE edate = 0 {$sql_brands_select} ORDER BY pos";
				$brands_selected = $db->getAll($sql); safeCheck($brands_selected);
				
				$results['brands_selected'] = $brands_selected;
			}
			
			// CONFIGURE ATTRIBUTES SEARCH
			$attribute_options = $_REQUEST["attribute_options"] ? $_REQUEST["attribute_options"] : array();
			foreach($attribute_options as $k => $v){
				$remove = 0;
				if(isset($_REQUEST["remove_attribute_options"])){
					foreach($_REQUEST["remove_attribute_options"] as $kk => $vv){
						if ($vv == $v){
							$remove = 1;
						}
					}
				}
				if ($remove == 0){
					$attribute_options_request[] = (int)$v;
					$query_string .= "&attribute_options[]=".(int)$v;
				}
				
			}
			if ($attribute_options_request){
				if ( sizeof($attribute_options_request) > 0 ){
					$sql_attribute_options_from = ", ".$product_to_attribute_option_table." AS ptao";
					if ( sizeof($attribute_options_request) == 1 ){
						$sql_attribute_options = " AND ptao.product_id = products.id AND ptao.attribute_option_id = '".$attribute_options_request[0]."' ";
						$sql_attribute_options_not = " AND ptao.attribute_option_id != '".$attribute_options_request[0]."' ";
						$sql_attributes_selected = "AND ptao.attribute_option_id = '".$attribute_options_request[0]."' ";
						$sql_attribute_options_selected = "AND id = '".$attribute_options_request[0]."' ";
					}else{
						$sql_attributes_selected = " AND ptao.attribute_option_id IN (".implode(",", $attribute_options_request).")";
						$sql_attribute_options_selected = " AND id IN (".implode(",", $attribute_options_request).")";
                        
                        //1.This is the option when we want to show products that have at least one of the attributes selected
                        $imploded_attribute_options = " AND (ptao.attribute_option_id = ".implode(" OR ptao.attribute_option_id = ", $attribute_options_request).")";
						$sql_attribute_options = " AND ptao.product_id = products.id ".$imploded_attribute_options." ";
                        //---END show products that have at least one of the attributes selected----------------------------
                        
                        //--- OR ------------------------
                        
                        //2.This is the option when we want to show products that have all the attributes selected
//                      $attributeOptionsCount = count($attribute_options_request);
//						$imploded_attribute_options = " AND ptao.attribute_option_id IN ( ".implode(",", $attribute_options_request).")";
//                        
//						$sql_attribute_options = " AND ptao.product_id = products.id ".$imploded_attribute_options."
//                                                    GROUP BY products.id
//                                                    HAVING count(*) = {$attributeOptionsCount}";
						//---END show products that have all the attributes selected-----------------------------------------
                        
						$imploded_attribute_options_not = " AND ptao.attribute_option_id != ".implode(" AND ptao.attribute_option_id != ", $attribute_options_request);
						$sql_attribute_options_not = " ".$imploded_attribute_options." ";
					}
					
					$distinct_attributes_selected = $db->getAll("SELECT DISTINCT ptao.attribute_id, 
																	(SELECT attributes.name_{$lng} AS name FROM ".$attributes_table." AS attributes WHERE attributes.id = ptao.attribute_id) AS name
															FROM ".$product_to_attribute_option_table." AS ptao 
															WHERE edate = 0 
															{$sql_attributes_selected}"); safeCheck($distinct_attributes_selected);
					foreach($distinct_attributes_selected as $k => $v){
						$options = $db->getAll("SELECT * FROM ".$attributes_to_attribute_options_table." WHERE edate = 0 AND attribute_id = '".$v["attribute_id"]."' {$sql_attribute_options_selected} ORDER BY pos"); safeCheck($options);
						$v["attribute_options"] = $options;
						$distinct_attributes_selected[$k] = $v;
					}
					
					$results['distinct_attributes_selected'] = $distinct_attributes_selected;
					
				}
			}
			
			if ( $params->has("alphabet_search") ){
				$alphabet = htmlspecialchars(trim($params->getString("alphabet_search"), ENT_QUOTES));
				$sql_alphabet = " AND products.name_{$lng} LIKE '".$alphabet."%' ";
			}
			
			$price_min = $params->getNumber('price_min');
			if ( $price_min ){
				$sql_price .= " AND products.price >= '".$price_min."' ";
			}
			$price_max = $params->getNumber('price_max');
			if ( $price_max ){
				$sql_price .= " AND products.price <= '".$price_max."' ";
			}
			$collection_id = $params->getInt('collection_id');
			if ( $collection_id ){
				$sql_price .= " AND products.collection_id = '".$collection_id."' ";
			}
			
			if($category_id){
                $results["page"] = "categories";
                
                $showCatSubcatProducts = is_numeric($row["show_cat_subcat_products"]) ? (int)$row["show_cat_subcat_products"] : 0;
                if($showCatSubcatProducts){
                    $subCategoriesArr = getSublevels($category_id);
                    $subCategoriesArr[] = $category_id;
                    $subCategoriesIds = implode(",", $subCategoriesArr);
                    if(count($subCategoriesArr) > 1){
                        $sqlSubCategories = " AND product_to_category.category_id IN ( $subCategoriesIds ) ";
                    }else{
                        $sqlSubCategories = " AND product_to_category.category_id = '{$category_id}' ";
                    }
                }else{
                    $sqlSubCategories = " AND product_to_category.category_id = '{$category_id}' ";
                }
                $results['showCatSubcatProducts'] = $showCatSubcatProducts;

				$products = productsToCategories($category_id);
				$brands = brandsToCategories($category_id);
				$collections = collectionsToCategories($category_id);

				$sqlBrandCollection = "";
				if($products || $brands || $collections) {
					$sqlBrandCollection .= " AND (";
					if(!empty($products)) {
						$sql_in_ptc = implode(",", $products);
						$sqlBrandCollection .= " products.id IN ({$sql_in_ptc})";
					}
					if(!empty($brands)) {
						$condition = (!$products) ? '' : 'OR'; 
						$sql_in_btc = implode(",", $brands);
						$sqlBrandCollection .= " {$condition} products.brand_id IN ({$sql_in_btc})";
					}
					if(!empty($collections)) {
						$condition = (!$products && !$brands) ? '' : 'OR'; 
						$sql_in_ctc = implode(",", $collections);
						$sqlBrandCollection .= " {$condition} products.collection_id IN ({$sql_in_ctc})";
					}
					$sqlBrandCollection .= ") ";
				}

				$sql_join_attributes = "";
				if($sql_attribute_options) {
					$sql_join_attributes = " INNER JOIN {$product_to_attribute_option_table} AS ptao ON ptao.product_id = products.id {$sql_attribute_options}";
				}
		
                $sql_count = "SELECT count(products.id) AS cntr
                            FROM ".$products_table." AS products
                            {$sql_join_attributes}
							".(!$products && !$brands && !$collections ? 'INNER' : 'LEFT')." JOIN {$product_to_category_table} AS product_to_category ON product_to_category.product_id = products.id {$sqlSubCategories}
                            WHERE 
                                products.edate = 0
                            AND products.active = 1 
							{$sqlBrandCollection}
                            {$sql_alphabet}
                            {$sql_price}
                            {$sql_brands}	
                            ";
				// die($sql+);
            }else if($brand_id){
                $results["page"] = "brand";
                
                $sql_count = "SELECT count(products.id) AS cntr
                                FROM ".$products_table." AS products
                                      {$sql_attribute_options_from}
                                WHERE 
                                    products.edate = 0
                                AND products.active = 1
                                AND products.brand_id = {$brand_id}
                                {$sql_alphabet}
                                {$sql_price}
                                {$sql_attribute_options}";
            }else if($search_string || $search_page){
                $results["page"] = "search";
                
                $sql_count = "SELECT count(products.id) AS cntr
                                FROM ".$products_table." AS products
                                      {$sql_attribute_options_from}
                                WHERE 
                                    products.edate = 0
                                AND products.active = 1
                                {$sql_where_search}
                                {$sql_alphabet}
                                {$sql_price}
                                {$sql_brands}
                                {$sql_attribute_options}";
            }
            
            if($category_id){
                $sql = "SELECT DISTINCT products.id, 
									products.name_{$lng} AS name, 
									products.name_en, 
									products.excerpt_{$lng} AS excerpt, 
									products.htaccess_url_{$lng} AS htaccess_url, 
									products.price,
									products.new_product,
									products.brand_id,
									products.code,
									products.promotion,
									products.hotoffer,
                                    (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
									(SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id AND rating.edate = 0) AS rating,
									(SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id AND images.edate = 0 ORDER BY pos LIMIT 1) AS mainPic
					FROM ".$products_table." AS products
					{$sql_join_attributes}
					".(!$products && !$brands && !$collections ? 'INNER' : 'LEFT')." JOIN {$product_to_category_table} AS product_to_category ON product_to_category.product_id = products.id {$sqlSubCategories}
					 WHERE products.edate = 0
					 AND products.active = 1
					 {$sqlBrandCollection}
					 {$sql_alphabet}
					 {$sql_price}
					 {$sql_brands}
					 {$sql_order_by}
					 LIMIT {$start}, {$items_per_page}
					 ";

				$sqlAll = "SELECT DISTINCT products.id, 
                                products.name_{$lng} AS name, 
                                products.name_en, 
                                products.excerpt_{$lng} AS excerpt, 
                                products.htaccess_url_{$lng} AS htaccess_url, 
                                products.price,
                                products.new_product,
                                products.brand_id,
                                products.code,
                                products.promotion,
                                products.hotoffer,
                                (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
                                (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id AND rating.edate = 0) AS rating,
                                (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id AND images.edate = 0 ORDER BY pos LIMIT 1) AS mainPic
                            FROM ".$products_table." AS products
							{$sql_join_attributes}
							".(!$products && !$brands && !$collections ? 'INNER' : 'LEFT')." JOIN {$product_to_category_table} AS product_to_category ON product_to_category.product_id = products.id {$sqlSubCategories}
                            WHERE products.edate = 0
                            AND products.active = 1
							{$sqlBrandCollection}
                            {$sql_alphabet}
                            {$sql_price}
                            {$sql_brands}
                            {$sql_order_by}
                            ";
            }else if($brand_id){
                $sql = "SELECT 
                            products.id, 
                            products.name_{$lng} AS name, 
                            products.name_en, 
                            products.htaccess_url_{$lng} AS htaccess_url, 
                            products.excerpt_{$lng} AS excerpt, 
                            products.price,
                            products.new_product,
                            products.brand_id,
                            products.code,
                            products.promotion,
                            products.hotoffer,
                            (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
                            (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id AND rating.edate = 0) AS rating,
                            (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id AND images.edate = 0 ORDER BY pos LIMIT 1) AS mainPic
                        FROM ".$products_table." AS products
                              {$sql_attribute_options_from}
                        WHERE products.edate = 0
                        AND products.active = 1
                        AND products.brand_id = {$brand_id}
                        {$sql_alphabet}
                        {$sql_price}
                        {$sql_attribute_options}
                        {$sql_order_by}
                        LIMIT {$start}, {$items_per_page}
                        ";
                $sqlAll = "SELECT 
                            products.id, 
                            products.name_{$lng} AS name, 
                            products.name_en, 
                            products.htaccess_url_{$lng} AS htaccess_url, 
                            products.excerpt_{$lng} AS excerpt, 
                            products.price,
                            products.new_product,
                            products.brand_id,
                            products.code,
                            products.promotion,
                            products.hotoffer,
                            (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
                            (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id AND rating.edate = 0) AS rating,
                            (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id AND images.edate = 0 ORDER BY pos LIMIT 1) AS mainPic
                        FROM ".$products_table." AS products
                              {$sql_attribute_options_from}
                        WHERE products.edate = 0
                        AND products.active = 1
                        AND products.brand_id = {$brand_id}
                        {$sql_alphabet}
                        {$sql_price}
                        {$sql_attribute_options}
                        {$sql_order_by}
                        ";
            }else if($search_string || $search_page){
                $sql = "SELECT 
                            products.id, 
                            products.name_{$lng} AS name, 
                            products.name_en, 
                            products.htaccess_url_{$lng} AS htaccess_url, 
                            products.excerpt_{$lng} AS excerpt, 
                            products.price,
                            products.new_product,
                            products.brand_id,
                            products.code,
                            products.promotion,
                            products.hotoffer,
                            (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
                            (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id AND rating.edate = 0) AS rating,
                            (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id AND images.edate = 0 ORDER BY pos LIMIT 1) AS mainPic
                        FROM ".$products_table." AS products
                            {$sql_attribute_options_from}
                        WHERE 
                            products.edate = 0
                        AND products.active = 1
                        {$sql_where_search}
                        {$sql_alphabet}
                        {$sql_price}
                        {$sql_brands}
                        {$sql_attribute_options}
                        {$sql_order_by}
                        LIMIT {$start}, {$items_per_page}
                        ";
                $sqlAll = "SELECT 
                            products.id, 
                            products.name_{$lng} AS name, 
                            products.name_en, 
                            products.htaccess_url_{$lng} AS htaccess_url, 
                            products.excerpt_{$lng} AS excerpt, 
                            products.price,
                            products.new_product,
                            products.brand_id,
                            products.code,
                            products.promotion,
                            products.hotoffer,
                            (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
                            (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id AND rating.edate = 0) AS rating,
                            (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id AND images.edate = 0 ORDER BY pos LIMIT 1) AS mainPic
                        FROM ".$products_table." AS products
                            {$sql_attribute_options_from}
                        WHERE 
                            products.edate = 0
                        AND products.active = 1
                        {$sql_where_search}
                        {$sql_alphabet}
                        {$sql_price}
                        {$sql_brands}
                        {$sql_attribute_options}
                        {$sql_order_by}
                        ";
            }
			$products = $db->getAll($sql); safeCheck($products);
            $helpers = new Helpers();
            $user_group_id = Helpers::getCurentUserGroupId();
            
			foreach($products as $k=>$v){
				$v['link_title'] = str_replace($link_find, $link_repl, $v['product_name']);
				if(file_exists($install_path."/files/tn/".$v["pic"])){
					$sizes = getImageSize($install_path."/files/tn/".$v["pic"]);
				}else{
					$sizes = array();
				}
				$v["image_width"] = $sizes[0];
				$v["image_height"] = $sizes[1];
				
				$v["price"] = number_format($v["price"], 2, '.', '0');
				//$v["price_specialoffer"] = number_format($v["price_specialoffer"], 2, '.', '0');
				$price_specialoffer = getSpecialOfferPrice($v["id"], $v["brand_id"], 1);
				$v["price_specialoffer"] = $helpers->getDiscountedPrice($price_specialoffer["price_specialoffer"], 1, $user_group_id);
				$v["discountPic"] = $price_specialoffer["pic"];
				
                if ( $user["id"] ){
					$check = $db->getRow("SELECT * FROM ".$favourites_table." WHERE edate = 0 AND product_id = ".$v["id"]." AND user_id = ".$user["id"]); safeCheck($check);
					if ( $check["id"] ){
						$v["in_favourites"] = 1;
					}else{
						$v["in_favourites"] = 0;
					}
				}
                
				$products[$k] = $v;
			}
			
			$results['products'] = $products;
			$results['sqlProductsAll'] = $sqlAll;
			
//			$products_count = $db->getRow("SELECT count(products.id) AS cntr
//														FROM ".$products_table." AS products,
//							  ".$product_to_category_table." AS product_to_category 
//							  {$sql_attribute_options_from}
//					 WHERE products.edate = 0
//					 AND product_to_category.product_id = products.id 
//					 {$sqlSubCategories}
//					 AND products.active = '1'
//					 {$sql_attribute_options}
//					 {$sql_alphabet}
//					 {$sql_brands}"); safeCheck($products_count);
			
			$counter = $db->getRow($sql_count); safeCheck($counter);
            $results['results_found'] = $counter['cntr'];

			$pages_count = ceil($counter['cntr']/$items_per_page);
//			$sm->configLoad($language_file);
//			$configVars = $sm->getConfigVars();
//			
//			$sm->configLoad($htaccess_file);
//			$htaccessVars = $sm->getConfigVars();
			
			$results['server'] = $_SERVER;
			
			$params_tmp = str_replace("&response_type=3", "", http_build_query($_REQUEST));
			
			$query_string = "advanced-search?".$params_tmp;
			
			$parent_id = $id;
			$generate_pages .= '';
			
			if ( $page > 1  ){
				$generate_pages .= '<a href="/'.$query_string.'&page='.($page-1).'">&laquo;</a>';
			}else{
				$generate_pages .= '';
			}
			
			if ($page > 5 ){
				$starting_page = $page-5;
			}else{
				$starting_page = 0;
			}
			if (($pages_count-$page) < 5 ){
				$ending_page = $pages_count;
			}else{
				$ending_page = $page+5;
			}
			
			for( $i = $starting_page ; $i < $ending_page ; $i++ ){
				if ( $i+1 == $page){
					if ( $i == 1 ){
						$generate_pages .= '<a class="active" href="/'.$query_string.'&page='.($i+1).'">'.($i+1).'</a>';
					}else{
						$generate_pages .= '<a class="active" href="/'.$query_string.'&page='.($i+1).'">'.($i+1).'</a>';
					}
				}else{
					if ( $i == 1 ){
						$generate_pages .= '<a href="/'.$query_string.'&page='.($i+1).'">'.($i+1).'</a>';
					}else{
						$generate_pages .= '<a href="/'.$query_string.'&page='.($i+1).'">'.($i+1).'</a>';
					}
				}
				
				
			}
			if ( $pages_count > 1 && $pages_count != $page){
				$generate_pages .= '
										<a  href="/'.$query_string.'&page='.($page+1).'" aria-label="Next">
											<span aria-hidden="true">&raquo;</span>
											<span class="sr-only">Next</span>
										</a>
									';
			}else{
				$generate_pages .= '';
			}
			$generate_pages .= '';
			
			$results['generate_pages'] = $generate_pages;
			$results['return_url'] = "/".$query_string;
			
			

			// RESPONSE TYPE RETURN
			if ( $params->getInt('response_type') == 3 ){
				echo json_encode($results);
			}elseif ( $params->getInt('response_type') == 2 ){
				return $results;
			}else{
				$results['id'] = $brand_id > 0 ? $brand_id : $category_id;
				$results['params'] = $params;
				self::getPageAdvancedSearch($results);
			}
		}
		
		public static function getPageAdvancedSearch($content){
			global $db;
			global $sm;
			global $lng;
			global $host;
			global $categories_table;
            global $products_table;
            global $product_to_category_table;
            global $attributes_table;
            global $product_to_attribute_option_table;
            global $attributes_to_attribute_options_table;
            global $brands_table;
			global $description;
			global $language_file;
			global $htaccess_file;
			global $htaccess_file_bg;
			global $htaccess_file_en;
			global $htaccess_file_de;
			global $htaccess_file_ru;
			global $htaccess_file_ro;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
			$id = (int)$content['id'];
			$params = $content['params'];
			
            if($content["page"] != "brand" && $content["page"] != "search"){
//                $children = $db->getAll("SELECT * FROM ".$categories_table." WHERE edate = 0 AND category_id = ".$id); safeCheck($children);
//                if ($children && count($children) > 0 ){
//                    $sql_ptc_join_in .= " ( ".$id;
//                    foreach($children as $k => $v){
//                        $sql_ptc_join_in .= ",".$v["id"];
//                    }
//                    $sql_ptc_join_in .= " ) ";
//                }else{
//                    $sql_ptc_join_in .= " ( ".$id.")";
//                }
                $categoriesObj = new Categories();
                $row = $categoriesObj->getRecord($id);
                
                $showCatSubcatProducts = is_numeric($row["show_cat_subcat_products"]) ? (int)$row["show_cat_subcat_products"] : 0;
                if($showCatSubcatProducts){
                    $subCategoriesArr = self::getSublevels($id);
                    $subCategoriesArr[] = $id;
                    $subCategoriesIds = implode(",", $subCategoriesArr);
                    if(count($subCategoriesArr) > 1){
                        $sqlSubCategories = " AND product_to_category.category_id IN ( $subCategoriesIds ) ";
                    }else{
                        $sqlSubCategories = " AND product_to_category.category_id = '{$id}' ";
                    }
                }else{
                    $sqlSubCategories = " AND product_to_category.category_id = '{$id}' ";
                }
                $sm->assign("showCatSubcatProducts", $showCatSubcatProducts);

				$products = productsToCategories($id);
				$brands = brandsToCategories($id);
				$collections = collectionsToCategories($id);

				$sqlBrandCollection = "";
				if($products || $brands || $collections) {
					$sqlBrandCollection .= " AND (";
					if(!empty($products)) {
						$sql_in_ptc = implode(",", $products);
						$sqlBrandCollection .= " products.id IN ({$sql_in_ptc})";
					}
					if(!empty($brands)) {
						$condition = (!$products) ? '' : 'OR'; 
						$sql_in_btc = implode(",", $brands);
						$sqlBrandCollection .= " {$condition} products.brand_id IN ({$sql_in_btc})";
					}
					if(!empty($collections)) {
						$condition = (!$products && !$brands) ? '' : 'OR'; 
						$sql_in_ctc = implode(",", $collections);
						$sqlBrandCollection .= " {$condition} products.collection_id IN ({$sql_in_ctc})";
					}
					$sqlBrandCollection .= ") ";
				}


                $products_all = $db->getAll("SELECT products.id, products.price
                                    FROM ".$products_table." AS products	
									".(!$products && !$brands && !$collections ? 'INNER' : 'LEFT')." JOIN {$product_to_category_table} AS product_to_category ON product_to_category.product_id = products.id {$sqlSubCategories}			  
                                    WHERE products.edate = 0 
									$sqlBrandCollection
                                    AND products.active = '1'
                                    "); safeCheck($products_all);
            }else if($content["page"] == "search"){
                $row = array();
                
                //$products_all = $content["products"];
                $products_all = $db->getAll($content["sqlProductsAll"]); safeCheck($products_all);
            }else{
                $row = Brands::getRecord($id);
                
                //$products_all = $content["products"];
                $products_all = $db->getAll($content["sqlProductsAll"]); safeCheck($products_all);
            }
            
			$results_found = $content['results_found'];
            $sm->assign("resultsCount", $results_found);
			$pages = $content['generate_pages'];
            $sm->assign("pages", $pages);
            
			// START OF FILTERS SECTION
            if($content["page"] == "categories"){
                $sm->assign("showCatSubcatProducts", $content["showCatSubcatProducts"]);
            }
            
            
			$price_min = 20000;
			$price_max = 0;
            $product_ids = array();
			foreach($products_all as $k => $v){
				$product_ids[] = $v["id"];
				if ( $v["price"] > $price_max ){
					$price_max = $v["price"];
				}
				if ( $v["price"] < $price_min ){
					$price_min = $v["price"];
				}
			}
			$sm->assign("price_max", $price_max);
			$sm->assign("price_min", $price_min);
			
			
			if ( sizeof($product_ids) > 0 ){
				if ( sizeof($product_ids) == 1 ){
					$sql_search_in_products = " product_id = '".$product_ids[0]."' ";
					$sql_search_in_products_id = " id = '".$product_ids[0]."' ";
				}else{
					$sql_search_in_products = " product_id IN (".implode(",", $product_ids).")";
					$sql_search_in_products_id = " id IN (".implode(",", $product_ids).")";
				}
				
				
				// GET DISTINCT PRODUCT ATTRIBUTE OPTIONS FROM WITHIN THE PRODUCT SELECTION TO CREATE LEFT HAND SIDE SEARCH
				$distinct_options = $db->getAll("SELECT DISTINCT attribute_option_id FROM ".$product_to_attribute_option_table." WHERE edate = 0 AND {$sql_search_in_products}"); safeCheck($distinct_options);
				foreach($distinct_options as $k => $v){
					if ( $v["attribute_option_id"] ){
						$attribute_options_ids[] = $v["attribute_option_id"];
					}
				}
				
				if ( sizeof($attribute_options_ids) > 0 ){
					if ( sizeof($attribute_options_ids) == 1 ){
						$sql_search_in_attribute_options = " AND id = '".$attribute_options_ids[0]."' ";
					}else{
						$sql_search_in_attribute_options = " AND id IN (".implode(",", $attribute_options_ids).")";
					}
				}
				
				// GET DISTINCT PRODUCT ATTRIBUTES FROM WITHIN THE PRODUCT SELECTION TO CREATE LEFT HAND SIDE SEARCH
				$distinct_attributes = $db->getAll("SELECT DISTINCT attribute_id FROM ".$product_to_attribute_option_table." WHERE edate = 0 AND {$sql_search_in_products}"); safeCheck($distinct_attributes);
				foreach($distinct_attributes as $k => $v){
					$attribute_ids[] = $v["attribute_id"];
				}
				
				if ( sizeof($attribute_ids) > 0 ){
					if ( sizeof($attribute_ids) == 1 ){
						$sql_search_in_attributes = " AND id = '".$attribute_ids[0]."' ";
					}else{
						$sql_search_in_attributes = " AND id IN (".implode(",", $attribute_ids).")";
					}
					
					// GET SELECTED ATTRIBUTES
					if ( $content['params']->has('attribute_options') ){
						$attribute_options_selected = $content['params']->get('attribute_options');
					}
					
					// GET DISTINCT PRODUCT ATTRIBUTES BASED ON THE ATTRIBUTES USED WITHIN THE PRODUCTS OF THIS CATEGORY
					$attributes = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$attributes_table." WHERE edate = 0 AND is_filterable = 1 {$sql_search_in_attributes} ORDER BY pos"); safeCheck($attributes);
					foreach($attributes as $k => $v){
						$attribute_options = $db->getAll("SELECT *, option_text_{$lng} AS option_text FROM ".$attributes_to_attribute_options_table." AS atao WHERE atao.attribute_id = '".$v["id"]."' {$sql_search_in_attribute_options} ORDER BY pos"); safeCheck($attribute_options);
						
						foreach( $attribute_options as $kk => $vv ){
							if ( $attribute_options_selected ){
								foreach( $attribute_options_selected as $kkk => $vvv ){
									if ( $vv['id'] == $vvv ){
										$vv['selected'] = 'checked';
									}
								}
							}
							$attribute_options[$kk] = $vv;
						}
						
						$v["attribute_options"] = $attribute_options;
						$attributes[$k] = $v;
					}
					$sm->assign("attributes", $attributes);
					// dbg($attributes);
				}
                
				if($content["page"] != "brand"){
                    $sql = "SELECT DISTINCT brand_id FROM ".$products_table." AS products WHERE edate = 0 AND {$sql_search_in_products_id}";
                    $distinct_brands = $db->getAll($sql); safeCheck($distinct_brands);

                    if ( sizeof($distinct_brands) > 0 ){
                        if ( sizeof($distinct_brands) == 1 ){
                            $sql_brands_select = " AND id = '".$distinct_brands[0]["brand_id"]."' ";
                        }else{
                            foreach($distinct_brands as $k => $v){
                                if ( $v["brand_id"] ){
                                    $distinct_brands_implode[] = $v["brand_id"];
                                }
                            }

                            $sql_brands_select = " AND id IN(".implode(",", $distinct_brands_implode).") ";
                        }
                    }
                    $sql = "SELECT id, pic, name_{$lng} AS name, LEFT(name_{$lng}, 1) AS nameLetter FROM ".$brands_table." WHERE edate = 0 {$sql_brands_select} ORDER BY name_{$lng}";
                    $sqlFirstLetter = "SELECT DISTINCT LEFT(name_{$lng}, 1) AS nameLetter FROM ".$brands_table." WHERE edate = 0 {$sql_brands_select} ORDER BY name_{$lng}";
                    $brands_selected = $db->getAll($sql); safeCheck($brands_selected);
                    $brands_letters = $db->getAll($sqlFirstLetter); safeCheck($brands_letters);

                    $sm->assign("brands_selected", $brands_selected);
                    $sm->assign("brands_letters", $brands_letters);
                }
                
			}
			// END OF FILTERS SECTION
			
			if ( $params->getString("old_link") && $row["htaccess_url"] ){
				header ('HTTP/1.1 301 Moved Permanently');
				if ( $params->getInt("sort_by_type") ){
					header('Location: '.$row["htaccess_url"]."/".strtolower($params->getInt("sort_by_type")));
				}else{
					header('Location: '.$row["htaccess_url"]);
				}
                die();
			}
			$sm->assign("infoKeys", $row['meta_keywords']);
			if ( $page ){
				$meta_page = ", ".$configVars["page_short"]." ".$page;
				$meta_description_page = " ".$configVars["page_short"]." ".$page;
			}
			
			if ( $row["meta_title"] ){
				$sm->assign("infoTitle", $row['meta_title'].$meta_page);
			}else{
				$sm->assign("infoTitle", $row['name'].$meta_page);
			}
			if ( $row["meta_description"] ){
				$sm->assign("infoDescr", $row['meta_description'].$meta_description_page);
			}else{
				$sm->assign("infoDescr", $row['name'].". ".$description["description"].$meta_description_page);
			}
			
			
			$row["excerpt"] = htmlspecialchars_decode($row["excerpt"]);
			$row["description"] = $row["description"];	
			$sm->assign("row", $row);
            
            if($content["page"] == "categories"){
                $breadcrumbs = self::generateBreadcrumbs($id);
            }else if($content["page"] == "brand"){
                $sm->configLoad($htaccess_file);
                $htaccessVars = $sm->getConfigVars();
                
                $brandsLink = $htaccessVars["htaccess_brands"];
                
                $breadcrumbs = '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>';
                $breadcrumbs .= '<a href="'.$host.$brandsLink.'">'.$configVars["brands_breadcrumbs"].'</a> <span>|</span>';
                $breadcrumbs .= '<span>'.$row["name"].'</span>';
            }else if($content["page"] == "search"){
                $sm->configLoad($language_file);
                $configVars = $sm->getConfigVars();
                $breadcrumbs = '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>';
                $breadcrumbs .= '<span>'.$configVars["search_breadcrumbs"].'</span>';
            }
			$sm->assign("breadcrumbs", $breadcrumbs);
            
			$s = $row["category_id"];
			if ($s){
				$category_id = $s;
			}else{
				$category_id = $row["id"];
			}
			if ($category_id == 0){
				$row = $db->getRow("SELECT id,
                                        name_{$lng} AS name,
                                        description_{$lng} AS description,
                                        pic_1,
                                        category_id
									FROM ".$categories_table."
									WHERE id = '{$id}'
									"); safeCheck($row);
				$category_id = $row["id"];
			}
			
			
			$sm->assign("pages", $pages);
			$sm->assign("page", $page);

			$sm->configLoad($htaccess_file_en);
			$htaccess_en = $sm->getConfigVars();
			if ( $row["htaccess_url_en"] ){
				$link_en = $row["htaccess_url_en"];
			}else{
				if ( $row["url_en"] ){
					$link_en = $row["url_en"];
				}else{
					$link_en = "/".$htaccess_en["htaccess_categories"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_ro);
			$htaccess_ro = $sm->getConfigVars();
			if ( $row["htaccess_url_ro"] ){
				$link_ro = $row["htaccess_url_ro"];
			}else{
				if ( $row["url_ro"] ){
					$link_ro = $row["url_ro"];
				}else{
					$link_ro = "/".$htaccess_ro["htaccess_categories"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_bg);
			$htaccess_bg = $sm->getConfigVars();
			if ( $row["htaccess_url_bg"] ){
				$link_bg = $row["htaccess_url_bg"];
			}else{
				if ( $row["url_bg"] ){
					$link_bg = $row["url_bg"];
				}else{
					$link_bg = "/".$htaccess_bg["htaccess_categories"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_de);
			$htaccess_de = $sm->getConfigVars();
			if ( $row["htaccess_url_de"] ){
				$link_de = "/".$row["htaccess_url_de"];
			}else{
				if ( $row["url_de"] ){
					$link_de = $row["url_de"];
				}else{
					$link_de = "/".$htaccess_de["htaccess_categories"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_ru);
			$htaccess_ru = $sm->getConfigVars();
			if ( $row["htaccess_url_ru"] ){
				$link_ru = "/".$row["htaccess_url_ru"];
			}else{
				if ( $row["url_ru"] ){
					$link_ru = $row["url_ru"];
				}else{
					$link_ru = "/".$htaccess_ru["htaccess_categories"]."/".$row["id"];
				}
			}
            
            $sm->configLoad($htaccess_file);
            
			$sm->assign("link_bg", $link_bg);
			$sm->assign("link_en", $link_en);
			$sm->assign("link_de", $link_de);
			$sm->assign("link_ru", $link_ru);
			$sm->assign("link_ro", $link_ro);
            
			$sm->assign("s", $_REQUEST["s"]);
			$sm->assign("breadcrumbs", $breadcrumbs);
			$sm->assign("filter_category_id", $id);
            
            if($content["page"] != "brand"){
                $brands_checked = $content['params']->get('brands');
                if ( is_array($brands_selected) ){
                    foreach($brands_selected as $k => $v){
                        if ( is_array($brands_checked)  ){
                            foreach( $brands_checked as $kk => $vv ){
                                if ( $v['id'] == $vv ){
                                    $v['selected'] = 'checked';
                                }
                            }
                            $brands_selected[$k] = $v;
                        }
                    }
                }
                $sm->assign("brands_selected", $brands_selected);
                $sm->assign("showCatSubcatProducts", $content['showCatSubcatProducts']);
                if($content["page"] != "search"){
                    $subcategories = $categoriesObj->getSubcategories($id);
                    $sm->assign("subcategories", $subcategories);
                    $sm->assign("page_categories", 1);
                }
            }
			$sm->assign("sortby", $content['sortby']);
			
			$sm->assign("distinct_attributes_selected", $content['distinct_attributes_selected']);
			$sm->assign("resultsCount", $content['results_found']);
			$sm->assign("items", $content['products']);
			$sm->assign("generate_pages", $content['generate_pages']);
			
            if($content["page"] == "brand"){
                $sm->display("brand.html");
            }else if($content["page"] == "search"){
                $sm->display("search.html");
            }else{
                $sm->display("categories.html");
            }
		}
		
	}
	
?>