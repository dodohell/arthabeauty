<?php
	class Offers extends Settings{
		
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
		
		public function getPage($page = 0, FilteredMap $params){
			global $lng;
			global $db;
			global $sm;
			global $brands_table;
			global $products_table;
			global $product_types_table;
			global $products_images_table;
			global $products_comments_table;
			global $products_comments_table;
			global $product_to_category_table;
			global $language_file;
			global $htaccess_file;
			global $limit;

			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
			$sm->configLoad($htaccess_file);
			
			
			$htaccessVars = $sm->getConfigVars();
			$page_url = ($htaccessVars['htaccess_offers']) ? '/'.$htaccessVars['htaccess_offers'] : '/offers';

			$sort_by = $params->has("sort_by") ? $params->getString("sort_by") : "";
            $sm->assign("sort_by", $sort_by);
            $sql_order_by = " ORDER BY products.price ASC ";
            if($sort_by == "price-asc"){
                $sql_order_by = " ORDER BY products.price ASC ";
            }else if($sort_by == "price-desc"){
                $sql_order_by = " ORDER BY products.price DESC ";
            }else if($sort_by == "postdate-asc"){
                $sql_order_by = " ORDER BY products.postdate ASC ";
            }else if($sort_by == "postdate-desc"){
                $sql_order_by = " ORDER BY products.postdate DESC ";
            }
            
            if($sort_by == "price-asc"){
                $page_url .= "&sort_by=price-asc";
            }else if($sort_by == "price-desc"){
				$page_url .= "&sort_by=price-desc";
            }else if($sort_by == "postdate-asc"){
				$page_url .= "&sort_by=postdate-asc";
            }else if($sort_by == "postdate-desc"){
				$page_url .= "&sort_by=postdate-desc";
            }

            $today = date('Y-m-d');
			$discounts = Discounts::getValidDiscounts($today);
			
			$products = array();
			$categories = array();
			$collections = array();
			$brands = array();

			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();

            foreach($discounts as $discount) {
				if(!isset($discount['id']) || empty($discount['id'])) {
					continue;
				}
		
				$discountedCategories = Discounts::getDiscountCategories($discount['id']);
				foreach($discountedCategories as $discountedCategory) {
					$categories[] = $discountedCategory['category_id'];
				}
				
				$discountedProducts = Discounts::getDiscountProducts($discount['id']);
				foreach($discountedProducts as $discountedProduct) {
					$products[] = $discountedProduct['product_id'];
				}
				
				$discountedCollections = Discounts::getDiscountCollections($discount['id']);
				foreach($discountedCollections as $discountedCollection) {
					$collections[] = $discountedCollection['collection_id'];
				}

				$discountedBrands = Discounts::getDiscountBrands($discount['id']);
				foreach($discountedBrands as $discountedBrand) {
					$brands[] = $discountedBrand['brand_id'];
				}
			}

			$products = array_unique($products);
			$categories = array_unique($categories);
			$collections = array_unique($collections);
			$brands = array_unique($brands);
            
            $current_page = is_numeric($page) && $page > 0 ? (int)$page : 1;
            $limit = (int)$limit;
			
            $sqlAll = "SELECT 
							COUNT(DISTINCT products.id) as cntr
						FROM 
							".$products_table." AS products";
			if($categories) {
				if(!$products && !$collections && !$brands) {
					$sql_ptc_join_in = implode(",", $categories);
					$sqlAll .= " INNER JOIN {$product_to_category_table} AS ptc ON ptc.product_id = products.id AND ptc.category_id IN ({$sql_ptc_join_in})";
				} else {
					$sql_ptc_join_in = implode(",", $categories);
					$sqlAll .= " LEFT JOIN {$product_to_category_table} AS ptc ON ptc.product_id = products.id AND ptc.category_id IN ({$sql_ptc_join_in})";
				}
			}
			$sqlAll .= " WHERE
				products.edate = 0
				AND products.active = 1";
			if($products || $collections || $brands) {
				$sqlAll .= " AND (";
				if(!empty($products)) {
					$sql_in_dtp = implode(",", $products);
					$sqlAll .= " products.id IN ({$sql_in_dtp})";
				}
				if(!empty($collections)) {
					$condition = (!$products) ? '' : 'OR'; 
					$sql_in_dtc = implode(",", $collections);
					$sqlAll .= " {$condition} products.collection_id IN ({$sql_in_dtc})";
				}
				if(!empty($brands)) {
					$condition = (!$products && !$collections) ? '' : 'OR'; 
					$sql_in_dtb = implode(",", $brands);
					$sqlAll .= " {$condition} products.brand_id IN ({$sql_in_dtb})";
				}
				$sqlAll .= ") ";
			} else {
				$sqlAll .= " AND 0";
			}
			
			$sqlAll .= "{$sql_order_by} {$sql_limit}";

			$results = $db->getRow($sqlAll); safeCheck($results);
			// echo "<pre>";
			// var_dump($results);
			// echo "</pre>";
			// die;
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
					".$products_table." AS products";
			if($categories) {
				if(!$products && !$collections && !$brands) {
					$sql_ptc_join_in = implode(",", $categories);
					$sqlItems .= " INNER JOIN {$product_to_category_table} AS ptc ON ptc.product_id = products.id AND ptc.category_id IN ({$sql_ptc_join_in})";
				} else {
					$sql_ptc_join_in = implode(",", $categories);
					$sqlItems .= " LEFT JOIN {$product_to_category_table} AS ptc ON ptc.product_id = products.id AND ptc.category_id IN ({$sql_ptc_join_in})";
				}
			}
			$sqlItems .= " WHERE
				products.edate = 0
				AND products.active = 1";
			if($products || $collections || $brands) {
				$sqlItems .= " AND (";
				if(!empty($products)) {
					$sql_in_dtp = implode(",", $products);
					$sqlItems .= " products.id IN ({$sql_in_dtp})";
				}
				if(!empty($collections)) {
					$condition = (!$products) ? '' : 'OR'; 
					$sql_in_dtc = implode(",", $collections);
					$sqlItems .= " {$condition} products.collection_id IN ({$sql_in_dtc})";
				}
				if(!empty($brands)) {
					$condition = (!$products && !$collections) ? '' : 'OR'; 
					$sql_in_dtb = implode(",", $brands);
					$sqlItems .= " {$condition} products.brand_id IN ({$sql_in_dtb})";
				}
				$sqlItems .= ") ";
			} else {
				$sqlItems .= " AND 0";
			}
			$sqlItems .= "{$sql_order_by}
			{$sql_limit}";
			// var_export($sqlItems);
			// die;
			$productsList = $db->getAll($sqlItems); safeCheck($productsList);

			$helpers = new Helpers();
            $user_group_id = Helpers::getCurentUserGroupId();
            
            foreach ($productsList as $k => $v) {
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

                $productsList[$k] = $v;
            }

			$sm->assign("items", $productsList);
			
			$sm->assign("pages", $pages);
			$sm->assign("page", $page);
			$sm->assign("breadcrumbs", $breadcrumbs);
			$sm->display("offers.html");
		}
		
	}
	
?>