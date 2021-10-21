<?php
	class Collections extends Settings{
		
		
		public static function getRecord(int $id){
			global $db;
			global $lng;
			global $collections_table;
			
            $row = $db->getRow("SELECT *, name_{$lng} AS name, description_{$lng} AS description FROM ".$collections_table." WHERE id = {$id}"); safeCheck($row);
			
			return $row;
		}
		
		public static function searchCollection($name = ""){
			global $db;
			global $lng;
			global $collections_table;
			
			$name = strtolower($name);
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$collections_table." WHERE LOWER(name_{$lng}) = '".$name."' AND edate = 0"); safeCheck($row);
			
			return $row;
		}
		
		
		public static function getCollections($options = array()){
			global $db;
			global $lng;
			global $collections_table;
			
			$sql_where = '';
			if ( $options["first_page"] ){
				$sql_where .= "AND first_page = '1'";
			}
			
			if ( $options["brand_id"] ){
				$sql_where .= "AND brand_id = '".$options['brand_id']."'";
			}
			
			$collections = $db->getAll("SELECT *,
                                            name_{$lng} AS name,
                                            htaccess_url_{$lng} AS htaccess_url,
                                            description_{$lng} AS description
                                        FROM ".$collections_table."
                                        WHERE
                                           edate = 0
                                        AND name_{$lng} <> ''
                                        AND active = 1
										{$sql_where}
                                        ORDER BY pos"); safeCheck($collections);
			return $collections;
		}
        
        public function getPage(FilteredMap $params, $collection_id = 0) {
            global $db;
            global $sm;
            global $lng;
            global $host;
            global $user;
            global $language_file;
            global $htaccess_file;
            global $htaccess_file_bg;
            global $htaccess_file_en;
            global $htaccess_file_de;
            global $htaccess_file_ru;
            global $htaccess_file_ro;
            global $products_table;
            global $product_to_attribute_option_table;
            global $attributes_table;
            global $attributes_to_attribute_options_table;
            global $products_images_table;
            global $product_types_table;
            global $collections_table;
            global $products_comments_table;
            global $favourites_table;
            global $limit;
            global $brands_table;
            
            $sql_where = "";
            
            if ( !$collection_id ){
							$collection_id = $params->getInt("collection_id");
            }
            
            //$collection_id = $params->getInt("collection_id");
            
            if ($collection_id) {
                $sql_where .= " AND products.collection_id = ".$collection_id;
                $row = self::getRecord($collection_id);
            }

            $sm->assign("row", $row);
            
            $sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
            
            $brandPage = $htaccessVars["htaccess_brand"];
            
            $sort_by = $params->has("sort_by") ? $params->getString("sort_by") : "";
            $sm->assign("sort_by", $sort_by);
            $sql_order_by = " ORDER BY products.postdate DESC ";
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
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"]."/price-asc" : "/".$htaccessVars["htaccess_collection"]."/".$id."/price-asc";
            }else if($sort_by == "price-desc"){
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"]."/price-desc" : "/".$htaccessVars["htaccess_collection"]."/".$id."/price-desc";
            }else if($sort_by == "postdate-asc"){
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"]."/postdate-asc" : "/".$htaccessVars["htaccess_collection"]."/".$id."/postdate-asc";
            }else if($sort_by == "postdate-desc"){
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"]."/postdate-desc" : "/".$htaccessVars["htaccess_collection"]."/".$id."/postdate-desc";
            }else{
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"] : "/".$htaccessVars["htaccess_collection"]."/".$id;
            }

            /* Generate Pages Listings  */
            $counter = $db->getRow("SELECT 
                                        COUNT(products.id) AS cntr
                                    FROM 
                                        ".$products_table." AS products
                                    WHERE 
                                        products.edate = 0 
                                    AND products.active = 1
                                    -- AND products.name_{$lng} <> ''
                                    {$sql_where}"); safeCheck($counter);
            $count = $counter["cntr"];
            $sm->assign("resultsCount", $count);
            
            $page = $params->getInt("page");
            $start = $limit * $page;
            $pages = ceil($count / $limit);

            $generate_pages = '';

            $sm->assign("count", $count);
            $sm->assign("start", $start+1);
            $sm->assign("end", $start+$limit);
    //        $sm->configLoad($htaccess_file);
    //        $htaccessVars = $sm->getConfigVars();

            $url_prefix = $_SERVER["REQUEST_URI"] . "&page=";

            for ($i = 0; $i < $pages; $i++) {
                $selected = '';
                if ($i == $page) {
                    $selected = 'active';
                }
                if ($i == 0) {
                    $generate_pages .= '<a class="page-link '.$selected.'" href="' . $url_prefix . '">' . ($i + 1) . '</a>';
                } else {
                    $generate_pages .= '<a class="page-link '.$selected.'" href="' . $url_prefix . $i . '">' . ($i + 1) . '</a>';
                }
            }

            if ($page > 0) {
                if ($page == 1) {
                    $generate_pages = '<a href="' . $url_prefix . '" class="prev">&lt;</a>' . $generate_pages;
                } else {
                    $generate_pages = '<a href="' . $url_prefix . ($page - 1) . '" class="prev">&lt;</a>' . $generate_pages;
                }
            }

            if ($page < $pages - 1) {
                $generate_pages .= '<a href="' . $url_prefix . ($page + 1) . '" class="next">&gt;</a>';
            }

            $priceAscLink = $_SERVER["REQUEST_URI"] . "&sort_by_price=asc";
            $priceDescLink = $_SERVER["REQUEST_URI"] . "&sort_by_price=desc";
            $sm->assign("priceAscLink", $priceAscLink);
            $sm->assign("priceDescLink", $priceDescLink);

            $sort_by_price = "";
            if($params->getString("sort_by_price") == "asc"){
                $sort_by_price = "ASC";
            }elseif ($params->getString("sort_by_price") == "desc"){
                $sort_by_price = "DESC";
            }

            $sql_sort_by = $sort_by_price ? " ORDER BY products.price {$sort_by_price}" : " ORDER BY products.id DESC ";

            $sql = "SELECT
                        products.*,
                        products.htaccess_url_{$lng} AS htaccess_url,
                        products.name_{$lng} AS name,
                        products.excerpt_{$lng} AS excerpt,
                        products.description_{$lng} AS description,
                        products.meta_title_{$lng} AS meta_title,
                        products.meta_keywords_{$lng} AS meta_keywords,
                        products.meta_description_{$lng} AS meta_description,
                        products.meta_metatags_{$lng} AS meta_metatags,
                        (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
                        (SELECT collections.name_{$lng} FROM ".$collections_table." AS collections WHERE collections.edate = 0 AND products.collection_id = collections.id) AS collection_name,
                        (SELECT pi.pic FROM " . $products_images_table . " AS pi WHERE pi.product_id = products.id ORDER BY pi.pos LIMIT 1) as mainPic,
                        (SELECT product_types.name_{$lng} FROM ".$product_types_table." AS product_types WHERE products.product_type_id = product_types.id) AS product_type_name,
                        (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id) AS rating,
                    (SELECT COUNT(rating2.id) FROM ".$products_comments_table." AS rating2 WHERE rating2.product_id = products.id AND rating2.edate = 0) AS reviews_count
                    FROM 
                        ".$products_table." AS products
                    WHERE
                        products.edate = 0
                    AND products.active = 1
                    -- AND products.name_{$lng} <> ''
                    {$sql_where}
                    {$sql_sort_by}
                    LIMIT {$start}, {$limit}";
            $products = $db->getAll($sql); safeCheck($products);

            $helpers = new Helpers();
            $user_group_id = Helpers::getCurentUserGroupId();

            foreach ($products as $k => $v) {
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
                    $v["bonus_points_win"] = round($price_specialoffer["price_specialoffer"] * 1, 0);
                    $v["discount_date_to"] = $price_specialoffer["discount_date_to"];
                    $v["discountPic"] = $price_specialoffer["pic"];
                }else{
                    $v["price"] = $helpers->getDiscountedPrice($v["price"], 0, $user_group_id);
                    $v["bonus_points_win"] = round($v["price"] * 1, 0);
                    $v["discountPic"] = null;
                }

                $v["excerpt"] = htmlspecialchars_decode($v["excerpt"]);
                $v["description"] = htmlspecialchars_decode($v["description"]);

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

                $products[$k] = $v;
            }
            $sm->assign("items", $products);
            
            // START OF FILTERS SECTION
//			$showCatSubcatProducts = is_numeric($row["show_cat_subcat_products"]) ? (int)$row["show_cat_subcat_products"] : 0;
//			if($showCatSubcatProducts){
//				$subCategoriesArr = self::getSublevels($id);
//				$subCategoriesArr[] = $id;
//				$subCategoriesIds = implode(",", $subCategoriesArr);
//				if(count($subCategoriesArr) > 1){
//					$sqlSubCategories = " AND product_to_category.category_id IN ( $subCategoriesIds ) ";
//				}else{
//					$sqlSubCategories = " AND product_to_category.category_id = '{$id}' ";
//				}
//			}else{
//				$sqlSubCategories = " AND product_to_category.category_id = '{$id}' ";
//			}
//			$sm->assign("showCatSubcatProducts", $showCatSubcatProducts);
			
			$products_all = $db->getAll("SELECT 
                                            products.id, 
                                            products.price
                                        FROM 
                                            ".$products_table." AS products
                                        WHERE 
                                            products.edate = 0
                                        AND products.active = 1
                                        AND products.collection_id = {$collection_id}"); safeCheck($products_all);
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
				
				$sql = "SELECT DISTINCT collection_id FROM ".$products_table." AS products WHERE edate = 0 AND {$sql_search_in_products_id}";
				$distinct_collections = $db->getAll($sql); safeCheck($distinct_collections);
				
				if ( sizeof($distinct_collections) > 0 ){
					if ( sizeof($distinct_collections) == 1 ){
						$sql_collections_select = " AND id = '".$distinct_collections[0]["collection_id"]."' ";
					}else{
						foreach($distinct_collections as $k => $v){
							if ( $v["collection_id"] ){
								$distinct_collections_implode[] = $v["collection_id"];
							}
						}
						
						$sql_collections_select = " AND id IN(".implode(",", $distinct_collections_implode).") ";
					}
				}
				$sql = "SELECT id, pic, name_{$lng} AS name, LEFT(name_{$lng}, 1) AS nameLetter FROM ".$collections_table." WHERE edate = 0 {$sql_collections_select} ORDER BY name_{$lng}";
				$sqlFirstLetter = "SELECT DISTINCT LEFT(name_{$lng}, 1) AS nameLetter FROM ".$collections_table." WHERE edate = 0 {$sql_collections_select} ORDER BY name_{$lng}";
				$collections_selected = $db->getAll($sql); safeCheck($collections_selected);
				$collections_letters = $db->getAll($sqlFirstLetter); safeCheck($collections_letters);
				
				$sm->assign("collections_selected", $collections_selected);
				$sm->assign("collections_letters", $collections_letters);
			}
			// END OF FILTERS SECTION
            
            $sm->configLoad($htaccess_file_en);
			$htaccess_en = $sm->getConfigVars();
			if ( $row["htaccess_url_en"] ){
				$link_en = $row["htaccess_url_en"];
			}else{
				if ( $row["url_en"] ){
					$link_en = $row["url_en"];
				}else{
					$link_en = "/".$htaccess_en["htaccess_collection"]."/".$row["id"];
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
					$link_ro = "/".$htaccess_ro["htaccess_collection"]."/".$row["id"];
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
					$link_bg = "/".$htaccess_bg["htaccess_collection"]."/".$row["id"];
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
					$link_de = "/".$htaccess_de["htaccess_collection"]."/".$row["id"];
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
					$link_ru = "/".$htaccess_ru["htaccess_collection"]."/".$row["id"];
				}
			}
            
            $sm->configLoad($htaccess_file);
            
			$sm->assign("link_bg", $link_bg);
			$sm->assign("link_en", $link_en);
			$sm->assign("link_de", $link_de);
			$sm->assign("link_ru", $link_ru);
			$sm->assign("link_ro", $link_ro);
            
            $sm->configLoad($language_file);
            $configVars = $sm->getConfigVars();
            $breadcrumbs = '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>';
			
			$brandsObj = new Brands();
			$brand = $brandsObj->getRecord($products[0]["brand_id"]);
			if ( $brand['htaccess_url'] ){
				$brand_url = $brand['htaccess_url'];
			}else{
				$brand_url = $host.$brandPage.'/'.$brand['id'];
			}
			
            $breadcrumbs .= '<a href="'.$brand_url.'">'.$products[0]["brand_name"].'</a> <span>|</span>';
            $breadcrumbs .= '<span>'.$products[0]["collection_name"].'</span>';
            $sm->assign("breadcrumbs", $breadcrumbs);

            $sm->assign("page_collection", 1);
            $sm->assign("pages", $generate_pages);

            $sm->display("collection.html");
        }
        
        public function getCollectionsPage() {
            global $db;
            global $sm;
            global $host;
            global $language_file;
            global $htaccess_file_bg;
            global $htaccess_file_en;
            global $htaccess_file_de;
            global $htaccess_file_ru;
            global $htaccess_file_ro;
            global $htaccess_file;
            global $collections_table;
            
            /* Generate Pages Listings  */
            $counter = $db->getRow("SELECT 
                                        COUNT(collections.id) AS cntr
                                    FROM 
                                        ".$collections_table." AS collections
                                    WHERE 
                                        collections.edate = 0 
                                    AND collections.active = 1"); safeCheck($counter);
            $count = $counter["cntr"];
            $sm->assign("resultsCount", $count);
            
            $sql = "SELECT
                        id,
                        pic
                    FROM 
                        ".$collections_table." AS collections
                    WHERE
                        collections.edate = 0
                    AND collections.active = 1";
            $collections = $db->getAll($sql); safeCheck($collections);
            $sm->assign("items", $collections);
            
            $sm->configLoad($htaccess_file_bg);
			$htaccess_bg = $sm->getConfigVars();
            $link_bg = "/".$htaccess_bg["htaccess_collections"];
            
            $sm->configLoad($htaccess_file_en);
			$htaccess_en = $sm->getConfigVars();
            $link_en = "/".$htaccess_en["htaccess_collections"];
            
            $sm->configLoad($htaccess_file_de);
			$htaccess_de = $sm->getConfigVars();
            $link_de = "/".$htaccess_de["htaccess_collections"];
            
            $sm->configLoad($htaccess_file_ru);
			$htaccess_ru = $sm->getConfigVars();
            $link_ru = "/".$htaccess_ru["htaccess_collections"];
            
            $sm->configLoad($htaccess_file_ro);
			$htaccess_ro = $sm->getConfigVars();
            $link_ro = "/".$htaccess_ro["htaccess_collections"];
            
            $sm->configLoad($htaccess_file);
            
			$sm->assign("link_bg", $link_bg);
			$sm->assign("link_en", $link_en);
			$sm->assign("link_de", $link_de);
			$sm->assign("link_ru", $link_ru);
			$sm->assign("link_ro", $link_ro);
            
            $sm->configLoad($language_file);
            $configVars = $sm->getConfigVars();
            $breadcrumbs = '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>';
            $breadcrumbs .= '<span>'.$configVars["collections_breadcrumbs"].'</span>';
            $sm->assign("breadcrumbs", $breadcrumbs);
            
            $sm->assign("page_collections", 1);
            
            $sm->display("collections.html");
        }
		
	}
	