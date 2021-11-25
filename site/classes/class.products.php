<?php
	class Products extends Settings{
		
		public function getRecord(int $id){
			global $db;
			global $products_table;
            global $lng;
			
			$row = $db->getRow("SELECT p.*, p.name_$lng AS name FROM ".$products_table." AS p WHERE p.id = ".$id); safeCheck($row);
			
			return $row;
		}
		
		public function getProducts(FilteredMap $params){
			global $db;
			global $lng;
			global $products_table;
			
			$products = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$products_table." WHERE edate = 0 ORDER BY pos"); safeCheck($products);
			
			return $products;
		}
        
		public static function getPresentProducts(){
			global $db;
			global $lng;
			global $products_table;
			global $brands_table;
			global $collections_table;
			
			$products = $db->getAll("SELECT *,(SELECT name_{$lng} FROM {$brands_table} WHERE id = brand_id) as bname, (SELECT name_{$lng} FROM {$collections_table} WHERE id = collection_id) as cname, CONCAT_WS(', ', name_en, name_{$lng}, (SELECT name_{$lng} FROM {$brands_table} WHERE id = brand_id), (SELECT name_{$lng} FROM {$collections_table} WHERE id = collection_id), price, 'лв.') AS name FROM ".$products_table." WHERE edate = 0 AND is_present = 1 ORDER BY pos"); safeCheck($products);
			
			return $products;
		}
        
        public static function getHotOffers() {
            global $db;
            global $lng;
            global $products_table;
            global $products_images_table;
            global $product_types_table;
            global $products_comments_table;
            
            $sql = "SELECT DISTINCT
                            products.*,
                            products.name_{$lng} AS name,
                            products.excerpt_{$lng} AS excerpt,
                            products.htaccess_url_{$lng} AS htaccess_url,
                            products.description_{$lng} AS description,
                            products.meta_title_{$lng} AS meta_title,
                            products.meta_keywords_{$lng} AS meta_keywords,
                            products.meta_description_{$lng} AS meta_description,
                            products.meta_metatags_{$lng} AS meta_metatags, 
                            (SELECT pi.pic FROM " . $products_images_table . " AS pi WHERE pi.product_id = products.id ORDER BY pi.pos LIMIT 1) as mainPic,
                            (SELECT product_types.name_{$lng} FROM ".$product_types_table." AS product_types WHERE products.product_type_id = product_types.id) AS product_type_name,
                            (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id) AS rating,
                            (SELECT COUNT(rating2.id) FROM ".$products_comments_table." AS rating2 WHERE rating2.product_id = products.id) AS reviews_count
                        FROM 
                            ".$products_table." AS products
                        WHERE
                            products.edate = 0
                        AND products.active = 1
                        AND products.hotoffer = 1
                        ORDER BY products.pos";
            $hotoffers = $db->getAll($sql); safeCheck($hotoffers);
            
            $helpers = new Helpers();
            $user_group_id = Helpers::getCurentUserGroupId();
            
            foreach ($hotoffers as $k => $v) {
                $v["excerpt"] = htmlspecialchars_decode($v["excerpt"]);
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
                
                $hotoffers[$k] = $v;
            }
            
            return $hotoffers;
        }
		
		public static function getNewOffers($limit = 6) {
            global $db;
            global $lng;
            global $products_table;
            global $products_images_table;
            global $product_types_table;
            global $brands_table;
            global $products_comments_table;
            
            $sql = "SELECT DISTINCT
                            products.*,
                            products.name_{$lng} AS name,
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
                            (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id AND rating.edate = 0) AS rating,
                            (SELECT COUNT(rating2.id) FROM ".$products_comments_table." AS rating2 WHERE rating2.product_id = products.id AND rating2.edate = 0) AS reviews_count
                        FROM 
                            ".$products_table." AS products
                        WHERE
                            products.edate = 0
                        AND products.active = 1
                        AND products.new_product = 1
                        ORDER BY products.pos
                        LIMIT {$limit}";
            $results = $db->getAll($sql); safeCheck($results);
            
            $helpers = new Helpers();
            $user_group_id = Helpers::getCurentUserGroupId();
            
            foreach ($results as $k => $v) {
                $v["excerpt"] = htmlspecialchars_decode($v["excerpt"]);
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
                
                $results[$k] = $v;
            }
            
            return $results;
        }
        
        public function getProductPage(int $id) {
            global $sm;
            global $db;
            global $lng;
            global $user;
            global $params;
            global $products_table;
            global $product_types_table;
            global $products_comments_table;
            global $product_to_category_table;
            global $categories_table;
            global $collections_table;
            global $favourites_table;
            global $brands_table;
            global $language_file;
            global $htaccess_file;
            
			
            $row = $db->getRow("SELECT
                                    products.*,
                                    products.id,
                                    products.name_{$lng} AS name,
                                    products.htaccess_url_{$lng} AS htaccess_url,
                                    products.excerpt_{$lng} AS excerpt,
                                    products.useful_{$lng} AS useful,
                                    products.description_{$lng} AS description,
                                    products.usage_{$lng} AS `usage`,
                                    products.ingredients_{$lng} AS ingredients,
                                    products.video_{$lng} AS video,
                                    products.htaccess_url_{$lng} AS htaccess_url,
                                    products.meta_title_{$lng} AS meta_title,
                                    products.meta_keywords_{$lng} AS meta_keywords,
                                    products.meta_description_{$lng} AS meta_description,
                                    products.meta_metatags_{$lng} AS meta_metatags,
                                    products.active,
                                    products.price,
                                    (SELECT brands.pic FROM ".$brands_table." AS brands WHERE products.brand_id = brands.id AND brands.edate = 0) AS brand_pic,
                                    (SELECT collections.name_{$lng} FROM ".$collections_table." AS collections WHERE products.collection_id = collections.id AND collections.active = 1 AND collections.edate = 0) AS collection_name,
                                    (SELECT product_types.name_{$lng} FROM ".$product_types_table." AS product_types WHERE products.product_type_id = product_types.id) AS product_type_name,
                                    (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id AND rating.edate = 0) AS rating,
                                    (SELECT COUNT(rating2.id) FROM ".$products_comments_table." AS rating2 WHERE rating2.product_id = products.id AND rating2.edate = 0) AS reviews_count,
                                    (SELECT COUNT(rating3.id)/reviews_count*100 FROM ".$products_comments_table." AS rating3 WHERE rating3.product_id = products.id AND rating3.edate = 0 AND rating3.rating >= 4.5 AND rating3.rating <= 5) AS reviews_count_5,
                                    (SELECT COUNT(rating4.id)/reviews_count*100 FROM ".$products_comments_table." AS rating4 WHERE rating4.product_id = products.id AND rating4.edate = 0 AND rating4.rating >= 3.5 AND rating4.rating < 4.5) AS reviews_count_4,
                                    (SELECT COUNT(rating5.id)/reviews_count*100 FROM ".$products_comments_table." AS rating5 WHERE rating5.product_id = products.id AND rating5.edate = 0 AND rating5.rating >= 2.5 AND rating5.rating < 3.5) AS reviews_count_3,
                                    (SELECT COUNT(rating6.id)/reviews_count*100 FROM ".$products_comments_table." AS rating6 WHERE rating6.product_id = products.id AND rating6.edate = 0 AND rating6.rating >= 1.5 AND rating6.rating < 2.5) AS reviews_count_2,
                                    (SELECT COUNT(rating7.id)/reviews_count*100 FROM ".$products_comments_table." AS rating7 WHERE rating7.product_id = products.id AND rating7.edate = 0 AND rating7.rating >= 0.5 AND rating7.rating < 1.5) AS reviews_count_1
								FROM ".$products_table." AS products
                                WHERE edate = 0
                                AND active = 1
								AND id = {$id}
                             "); safeCheck($row);
                             
            if (!$row) {
                header("Location: /messages/100");
				die();
            }

            if (!$row["rating"]){
                $row["rating"] = 0;
            }
            
            $row["excerpt"] = htmlspecialchars_decode($row["excerpt"]);
            $row["description"] = htmlspecialchars_decode($row["description"]);
            
            $price_specialoffer = getSpecialOfferPrice($row["id"], $row["brand_id"], 1);
            
            $helpers = new Helpers();
            $user_group_id = Helpers::getCurentUserGroupId();
            
            if ( $price_specialoffer["price_specialoffer"] == $row["price"] ){
                $row["price"] = $helpers->getDiscountedPrice($row["price"], 0, $user_group_id);
                $row["price_specialoffer"] = 0.0;
                $row["price_specialoffer_text"] = $price_specialoffer["price_specialoffer_text"];
                $row["bonus_points"] = $price_specialoffer["bonus_points"];
                $row["bonus_points_win"] = round($price_specialoffer["price_specialoffer"] * 1, 0) ;
                $row["discount_date_to"] = $price_specialoffer["discount_date_to"];
                $row["discountPic"] = $price_specialoffer["pic"];
            }else if($price_specialoffer["price_specialoffer"] > 0.0){
                $row["price"] = $helpers->getDiscountedPrice($row["price"], 1, $user_group_id);
                $row["price_specialoffer"] = $helpers->getDiscountedPrice($price_specialoffer["price_specialoffer"], 1, $user_group_id);
                $row["price_specialoffer_text"] = $price_specialoffer["price_specialoffer_text"];
                $row["bonus_points"] = $price_specialoffer["bonus_points"];
                $row["bonus_points_win"] = round($price_specialoffer["price_specialoffer"] * 1, 0) ;
                $row["discount_date_to"] = $price_specialoffer["discount_date_to"];
                $row["discountPic"] = $price_specialoffer["pic"];
            }else{
                $row["price"] = $helpers->getDiscountedPrice($row["price"], 0, $user_group_id);
                $row["bonus_points_win"] = round($row["price"] * 1, 0);
                $row["discountPic"] = null;
            }
            $row["product_price_clear"] = round($row["product_price"],2);
            
            $manifacturer = array();
            if((int)$row["manifacturer_id"] > 0){
                $manifacturer = Manifacturers::getRecord($row["manifacturer_id"]);
            }
            $sm->assign("manifacturer", $manifacturer);
            
            $brand = array();
            $same_manifacturer = array();
            if((int)$row["brand_id"] > 0){
                $brand = Brands::getRecord($row["brand_id"]);
                $same_manifacturer = self::getManufacturerProducts($row["brand_id"], 6);
            }
            $sm->assign("brand", $brand);
            $sm->assign("same_manifacturer", $same_manifacturer);
            
            $row["rating"] = round($row["rating"], 1);
            
            if ( $row["enable_bonus_points"] ){
                if ( $row["bonus_points"] <= 0 ){
                    $row["bonus_points"] = $row["price"] * $bonus_points_to_sell;

                }
            }
            
            if ( $user["id"] ){
                $check = $db->getRow("SELECT * FROM ".$favourites_table." WHERE edate = 0 AND product_id = ".$id." AND user_id = ".$user["id"]); safeCheck($check);
                if ( $check["id"] ){
                    $row["in_favourites"] = 1;
                }else{
                    $row["in_favourites"] = 0;
                }
            }
            
            $sm->assign("row", $row);
            
            if ( $params->getString("old_link") && $row["htaccess_url"] ){
				header ('HTTP/1.1 301 Moved Permanently');
				header('Location: '.$row["htaccess_url"]);
                die();
			}
            
//            $sm->assign("freeDelivery", 0);
//            $currentPrice = $row["price_specialoffer"] > 0.0 ? $row["price_specialoffer"] : $row["price"];
//            if( $currentPrice > 49 ){
//                if ( $row["weight"] < 1 ){ // IF WEIGHT IS BELOW 1kg
//                    $sm->assign("freeDelivery", 1);
//                }
//                if( $currentPrice >= 100 ){
//                    if($row["weight"] < 2){
//                        $sm->assign("freeDelivery", 1);
//                    }
//                }
//            }
            
            $similar_products = self::getSimilarProducts($id, 0);

            foreach ($similar_products as $key => $similar_product) {
                $s_price_specialoffer = getSpecialOfferPrice($similar_product["id"], $similar_product["brand_id"], 1);

                /*$helpers = new Helpers();
                $user_group_id = Helpers::getCurentUserGroupId();*/

//                echo '<pre>'.print_r($s_price_specialoffer, true).'</pre>';

                if ($s_price_specialoffer["price_specialoffer"] == $similar_product["price"] ){
                    $similar_products[$key]["price"] = $helpers->getDiscountedPrice($similar_product["price"], 0, $user_group_id);
                    $similar_products[$key]["price_specialoffer"] = 0.0;
                    $similar_products[$key]["price_specialoffer_text"] = $s_price_specialoffer["price_specialoffer_text"];
                    $similar_products[$key]["bonus_points"] = $s_price_specialoffer["bonus_points"];
                    $similar_products[$key]["bonus_points_win"] = round($s_price_specialoffer["price_specialoffer"] * 1, 0) ;
                    $similar_products[$key]["discount_date_to"] = $s_price_specialoffer["discount_date_to"];
                    $similar_products[$key]["discountPic"] = $s_price_specialoffer["pic"];
                }else if($s_price_specialoffer["price_specialoffer"] > 0.0){
                    $similar_products[$key]["price"] = $helpers->getDiscountedPrice($similar_product["price"], 1, $user_group_id);
                    $similar_products[$key]["price_specialoffer"] = $helpers->getDiscountedPrice($s_price_specialoffer["price_specialoffer"], 1, $user_group_id);
                    $similar_products[$key]["price_specialoffer_text"] = $s_price_specialoffer["price_specialoffer_text"];
                    $similar_products[$key]["bonus_points"] = $s_price_specialoffer["bonus_points"];
                    $similar_products[$key]["bonus_points_win"] = round($s_price_specialoffer["price_specialoffer"] * 1, 0) ;
                    $similar_products[$key]["discount_date_to"] = $s_price_specialoffer["discount_date_to"];
                    $similar_products[$key]["discountPic"] = $s_price_specialoffer["pic"];
                }else{
                    $similar_products[$key]["price"] = $helpers->getDiscountedPrice($similar_product["price"], 0, $user_group_id);
                    $similar_products[$key]["bonus_points_win"] = round($similar_product["price"] * 1, 0);
                    $similar_products[$key]["discountPic"] = null;
                }
                $similar_products[$key]["product_price_clear"] = round($similar_product["product_price"],2);
            }

            /*echo '<pre>'.print_r($similar_products, true).'</pre>';
           exit;*/

			if (sizeof($similar_products) < 8){
				$total_products = sizeof($similar_products);
				if ( $total_products > 0 ){
					for ( $i = 0 ; $i < 8; $i++ ){
						$similar_products_use[$i] = $similar_products[$i%$total_products];
					}
				}
			}
			
			
            $sm->assign("similar_products", $similar_products_use);
            
//            $related_products = self::getRelatedProducts($id, 2);
//            $sm->assign("related_products", $related_products);
            
            $category = $db->getRow("SELECT *, (SELECT categories.name_{$lng} AS name FROM ".$categories_table." AS categories WHERE ptc.category_id = categories.id) AS name FROM ".$product_to_category_table." AS ptc WHERE ptc.product_id = {$id} AND category_id IN (SELECT id FROM ".$categories_table." WHERE category_id = 0 AND edate = 0) ORDER BY ptc.category_id"); safeCheck($category);
            $sm->assign("category", $category);
            
            
            $sm->configLoad($htaccess_file);
            $htaccessVars = $sm->getConfigVars();
            
            $sm->configLoad($language_file);
            $configVars = $sm->getConfigVars();
            
            $breadcrumbs = '<a href="/">'.$configVars["home_breadcrumbs"].'</a>';
            if ($category["category_id"]){
            	
                $breadcrumbs .= $this->getBreadcrumbs($category["category_id"], "");
                
                $categoryMain = $db->getRow("SELECT id, name_{$lng} AS name, htaccess_url_{$lng} AS htaccess_url FROM ".$categories_table." WHERE edate = 0 AND id = '".$category["category_id"]."'"); safeCheck($categoryMain);
                $sm->assign("categoryMain", $categoryMain);
                
		            $subCategory = $db->getRow("SELECT *, (SELECT categories.name_{$lng} AS name FROM ".$categories_table." AS categories WHERE ptc.category_id = categories.id) AS name,  (SELECT categories.htaccess_url_{$lng} AS htaccess_url FROM ".$categories_table." AS categories WHERE ptc.category_id = categories.id) AS htaccess_url FROM ".$product_to_category_table." AS ptc WHERE ptc.product_id = {$id} AND category_id IN (SELECT id FROM {$categories_table} WHERE edate = 0 and active = 1 AND category_id = ".$category["category_id"]." ) ORDER BY ptc.category_id"); safeCheck($category);
		            $sm->assign("subCategory", $subCategory);
		            
		            if($subCategory["category_id"]){
				          if($subCategory['htaccess_url']){
				              $breadcrumbs = $breadcrumbs.' <span>|</span> <a href="'.$subCategory['htaccess_url'].'">'.$subCategory["name"]. "</a>";
				          }else{
				              $breadcrumbs = $breadcrumbs.' <span>|</span> <a href="/'.$htaccessVars["htaccess_categories"].'/'.$subCategory["id"].'">'.$subCategory["name"]. "</a>";
				          }
		            }
		            //echo $subCategory["category_id"];
		            //$breadcrumbs .= $this->getBreadcrumbs($subCategory["category_id"], "");
                
            }
            $sm->assign("breadcrumbs", $breadcrumbs);
            
            $images = self::getProductImages($id);
            $sm->assign("images", $images);
            
			$images_count = sizeof($images);
			
			$number_of_options = 0;
			$quantity = 0;
            
			$tmp_counter = 0;
			$tmp_counter2 = $images_count;
			
			$display_options_as_buttons = 0 ;
			$display_variant_images = 0 ;
			
            $option_groups = self::getOptionGroups($id);
			foreach($option_groups as $k => $v){
				foreach( $v['options'] as $kk => $vv ){
					if ( $v['display_in_list'] ){
						if ( $v['info']['buttons'] ){
							$display_options_as_buttons = 1;
							if ( $vv['pic'] ){
								//$vv['color_index'] = $images_count + $tmp_counter;
								$vv['color_index'] = $tmp_counter;
								$tmp_counter++;
							}
						}
						if ( $vv['selected_values'] ){
							$number_of_options++;
                            $quantity += $vv['selected_values']['quantity'];
						}
					}
                    if ( $vv['pic'] ){
                        $display_variant_images = 1;
                        $vv['pic_index'] = $tmp_counter2;
                        $tmp_counter2++;
                    }
					$v['options'][$kk] = $vv;
				}
				$option_groups[$k] = $v;
			}
            
			
            if($number_of_options == 0){
                $quantity = $row["quantity"];
            }
            $sm->assign("quantity", $quantity);

            $sm->assign("option_groups", $option_groups);
            $sm->assign("display_options_as_buttons", $display_options_as_buttons);
            $sm->assign("display_variant_images", $display_variant_images);
            $sm->assign("number_of_options", $number_of_options);
            
            $files = self::getProductFiles($id);
            $sm->assign("files", $files);
            
            $attributeOptions = self::getProductAttributeOptions($id);
            $sm->assign("attributeOptions", $attributeOptions);
            
            $productTotalAmount = isset($row["price_specialoffer"]) && $row["price_specialoffer"] > 0.0 ? $row["price_specialoffer"] : $row["price"];
            $isFreeDelivery = false;
            if($row["weight"]){
                $isFreeDelivery = CartDiscounts::isFreeDelivery($productTotalAmount, $row["weight"]);
            }
            $sm->assign("freeDelivery", $isFreeDelivery);
            
            $product_categories = $db->getAll("SELECT categories.name_{$lng} AS name
									   FROM ".$categories_table." AS categories,
											".$product_to_category_table." AS ptc
									   WHERE categories.edate = 0
									   AND categories.id = ptc.category_id
									   AND ptc.product_id = '".$id."'
										"); safeCheck($product_categories);
            
            Helpers::generateMetaInfo($row, null, $product_categories);
            if($row["meta_title"]){
            }
            else{
            	$sm->assign("infoTitle", $row["name"].(($row["name_en"] && $row["name_bg"])?", ":"").$row["name_en"]." - ArthaBeauty");
            }
            if($row["meta_description"]){
            }
            else{
                //print_r();
                $meta_result = mb_substr(strip_tags($row["excerpt_bg"]), 0, 180);
            	$sm->assign("infoDescr", $meta_result);
            }
            
            $relatedProducts = self::getRelatedProductsImagesMyMainProductId($id);
            $sm->assign("relatedProducts", $relatedProducts);
            
            $productComments = ProductsRating::getProductComments($id);
            $sm->assign("productComments", $productComments);
            
            $sm->display("show_product.html");
        }
        
        public function getBreadcrumbs($id, $breadcrumbs){
            global $db;
            global $categories_table;
            global $lng;
            global $link_find;
            global $link_repl;
            global $sm;
            global $htaccess_file;
            
            $sm->configLoad($htaccess_file);
            $htaccessVars = $sm->getConfigVars();

            $parent = $db->getRow("SELECT
                                        id,
                                        category_id,
                                        name_{$lng} AS name,
                                        htaccess_url_{$lng} AS htaccess_url
                                    FROM
                                        " . $categories_table . "
                                    WHERE
                                        id = " . $id . "
                                    AND edate = 0
                                    AND active = 1
                                    ORDER BY pos
                                    "); safeCheck($parent);
            $parent['link_title'] = str_replace($link_find, $link_repl, $parent['name']);
            if($parent['htaccess_url']){
                $breadcrumbs = ' <span>|</span> <a href="'.$parent['htaccess_url'].'">'.$parent["name"]. "</a>" .$breadcrumbs;
            }else{
                $breadcrumbs = ' <span>|</span> <a href="/'.$htaccessVars["htaccess_categories"].'/'.$parent["id"].'">'.$parent["name"]. "</a>" .$breadcrumbs;
            }

            if ( $parent["category_id"] ){
                $breadcrumbs = getBreadcrumbs($parent["category_id"], $breadcrumbs);
            }
            return $breadcrumbs;
        }

        public static function getProductAttributeOptions($product_id) {
            global $db;
            global $lng;
            global $product_to_attribute_option_table;
            global $attributes_table;
            global $attributes_to_attribute_options_table;

            $res = $db->getAll("SELECT
                                    ptao.product_id,
                                    ptao.attribute_id,
                                    ptao.attribute_option_id,
                                    a.name_{$lng} AS attribute_name,
                                    atao.option_text_{$lng} AS option_text
                                FROM
                                    $product_to_attribute_option_table AS ptao
                                INNER JOIN $attributes_table AS a ON a.id = ptao.attribute_id
                                INNER JOIN $attributes_to_attribute_options_table AS atao ON atao.id = ptao.attribute_option_id
                                WHERE
                                    ptao.product_id = $product_id
                                AND ptao.edate = 0
                                AND a.edate = 0
                                AND a.active = 1
                                AND atao.edate = 0"); safeCheck($res);
            
            $attribute_ids = array_values(array_unique(array_column($res, 'attribute_id')));
            $sortedRes = array();
            foreach ($attribute_ids as $v) {
                foreach ($res as $kk => $vv) {
                    if($v == $vv["attribute_id"]){
                        $sortedRes[$v]["attribute_id"] = $v;
                        $sortedRes[$v]["attribute_name"] = $vv["attribute_name"];
                        $sortedRes[$v]["options"][] = $vv;
                    }
                }
            }
            
            return array_values($sortedRes);
            
        }
        
        public static function getRelatedProductsImagesMyMainProductId(int $main_product_id) {
            global $db;
            global $products_images_table;
            global $product_to_product_table;
            
            $res = $db->getAll("SELECT
                                    pi.*
                                FROM
                                    {$products_images_table} AS pi
                                WHERE
                                    pi.edate = 0
                                AND pi.product_id IN (
                                    SELECT
                                        ptp.product_id
                                    FROM
                                        {$product_to_product_table} AS ptp
                                    WHERE
                                        ptp.main_product_id = {$main_product_id}
                                )"); safeCheck($res);
            return $res;
        }
        
        public static function getSimilarProducts(int $product_id, int $limit = 0) {
            global $db;
            global $lng;
            global $products_table;
            global $products_images_table;
            global $product_to_product_similar_table;
            global $brands_table;
            
            $sql_limit = "";
            if($limit > 0){
                $sql_limit = "LIMIT {$limit}";
            }
            
            $res = $db->getAll("SELECT
                                    products.*,
                                    products.name_{$lng} AS name,
                                    products.excerpt_{$lng} AS excerpt,
                                    products.htaccess_url_{$lng} AS htaccess_url,
                                    (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE products.brand_id = brands.id) AS brand_name,
                                    (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.edate = 0 AND images.product_id = products.id ORDER BY pos LIMIT 1) AS pic
                                FROM ".$products_table." AS products,
                                     ".$product_to_product_similar_table." AS ptps
                                WHERE 
                                    ptps.main_product_id = {$product_id}
                                AND ptps.product_id = products.id
                                AND products.edate = 0
                                AND products.active = 1
                                {$sql_limit}
                               "); safeCheck($res);

            return $res;
        }
        
        public static function getRelatedProducts(int $product_id, int $limit = 2) {
            global $db;
            global $lng;
            global $products_table;
            global $products_images_table;
            global $product_to_product_table;
            
            $res = $db->getAll("SELECT DISTINCT
                                                products.*,
                                                products.name_{$lng} AS name,
                                                products.excerpt_{$lng} AS excerpt,
                                                products.htaccess_url_{$lng} AS htaccess_url,
                                                (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id ORDER BY pos LIMIT 1) AS pic
                                            FROM ".$products_table." AS products,
                                                 ".$product_to_product_table." AS ptp
                                            WHERE 
                                                ptp.main_product_id = {$product_id}
                                            AND ptp.product_id = products.id
                                            AND products.edate = 0
                                            AND products.active = 1
                                            LIMIT {$limit}
                                           "); safeCheck($res);
            return $res;
        }
        
        public static function getManufacturerProducts(int $brand_id, int $limit = 6) {
            global $db;
            global $lng;
            global $products_table;
            global $products_images_table;
            
            $res = $db->getAll("SELECT DISTINCT
                                                products.*, 
                                                products.name_{$lng} AS name,
                                                products.excerpt_{$lng} AS excerpt,
                                                products.htaccess_url_{$lng} AS htaccess_url,
                                                (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id ORDER BY pos LIMIT 1) AS pic
                                            FROM ".$products_table." AS products
                                            WHERE products.edate = 0
                                            AND products.active = 1
                                            AND products.brand_id = {$brand_id}
                                            ORDER BY RAND()
                                            LIMIT {$limit}
                                            "); safeCheck($res);
            return $res;
        }
        
        public static function getOptionGroups(int $product_id) {
            global $db;
            global $lng;
            global $variants_table;
            global $options_table;
            global $option_groups_table;
            global $allowZeroQuantityVariantOrder;
            
            $variantQuantitySQL = " AND quantity > 0 ";
            if($allowZeroQuantityVariantOrder){
                $variantQuantitySQL = "";
            }
            $option_groups = $db->getAll("SELECT DISTINCT option_group_id FROM ".$variants_table." WHERE product_id = '".$product_id."' AND edate = 0"); safeCheck($option_groups);
            $option_groups_selected = $db->getAll("SELECT * FROM ".$variants_table." WHERE product_id = '".$product_id."' {$variantQuantitySQL} AND edate = 0"); safeCheck($option_groups_selected);
            foreach($option_groups as $k => $v){
                $row_tmp = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$option_groups_table." WHERE edate = 0 AND id = '".$v["option_group_id"]."' "); safeCheck($row_tmp);
                $v["info"] = $row_tmp;

                $options = $db->getAll("SELECT *, id, option_text AS name FROM ".$options_table." WHERE edate = 0 AND option_group_id = '".$v["option_group_id"]."' ORDER BY pos"); safeCheck($options);
				
                $display_in_list = 0;
                foreach($options as $kk => $vv){
                    foreach($option_groups_selected as $kkk => $vvv){
                        if ( $vv["id"] == $vvv["option_id"] ){
                            $vv["selected_values"] = $vvv;
                            $vv["checked"] = "checked";
                            $vv["pic"] = $vvv['pic'];
                            $display_in_list = 1;
                        }
                    }
                    $options[$kk] = $vv;
                }
                $v["display_in_list"] = $display_in_list;
                $v["options"] = $options;
                $option_groups[$k] = $v;
            }
            return $option_groups;
        }
        
        public static function getProductImages(int $product_id) {
            global $db;
            global $products_images_table;
            
            $res = $db->getAll("SELECT
                                    *
                                FROM
                                    ".$products_images_table."
                                WHERE
                                    product_id = {$product_id}
                                AND edate = 0
                                ORDER BY pos"); safeCheck($res);
            return $res;
        }
        
        public static function getProductFiles(int $product_id) {
            global $db;
            global $lng;
            global $products_files_table;
            
            $res = $db->getAll("SELECT
                                    *,
                                    name_{$lng} AS name
                                FROM
                                    ".$products_files_table."
                                WHERE
                                    product_id = {$product_id}
                                AND edate = 0
                                ORDER BY pos"); safeCheck($res);
            return $res;
        }
        
        public static function getFastOrderPage(FilteredMap $params) {
            global $sm;
            
            $product_id = $params->getInt("product_id");
            $variant_id = $params->getInt("variant_id");

            $sm->assign("checkSuccess", $params->getInt("checkSuccess"));

            $sm->assign("product_id", $product_id );
            $sm->assign("variant_id", $variant_id );
            $sm->assign("star", '<span style="color: #ff0000;">*</span>');
            
            $sm->display("fastOrder.html");
        }
        
        public static function getVariantDetails(FilteredMap $params) {
            global $db;
            global $variants_table;
            global $products_table;

            $product_id = $params->getInt("product_id");
            $variant_id = $params->getInt("variant_id");
            
            $price_special_offer = getSpecialOfferPrice($product_id, 0, 0, $variant_id);
            
            $helpers = new Helpers();
            $user_group_id = Helpers::getCurentUserGroupId();
            
            if($variant_id){
                $row = $db->getRow("SELECT * FROM ".$variants_table." WHERE option_id = '".$variant_id."' AND product_id = '".$product_id."' AND edate = 0"); safeCheck($row);
            }else{
                $row = $db->getRow("SELECT p.price, p.weight FROM ".$products_table." AS p WHERE p.id = ".$product_id. " AND p.edate = 0"); safeCheck($row);
            }
            
            if ( $price_special_offer == $row["price"]){
                $row["price"] = $helpers->getDiscountedPrice($row["price"], 0, $user_group_id);
            }else if($price_special_offer > 0.0){
                $row["price_strikethrough"] = $helpers->getDiscountedPrice($row["price"], 1, $user_group_id);
                $row["price"] = $helpers->getDiscountedPrice($price_special_offer, 1, $user_group_id);
            }else{
                $row["price"] = $helpers->getDiscountedPrice($row["price"], 0, $user_group_id);
            }
//            if ( $_SERVER["REMOTE_ADDR"] == "84.201.192.58" ){
//                //	dbg($row);
//                // dbg($_REQUEST);
//            }

            $isFreeDelivery = false;
            if($row["weight"] > 0.0){
                $isFreeDelivery = CartDiscounts::isFreeDelivery((float)$row["price"], (float)$row["weight"]);
            } 
            $row["isFreeDelivery"] = $isFreeDelivery;
//            $row["bonus_points_price"] = $row["price"] * $bonus_points_to_sell;
//            $row["bonus_points_won"] = round($row["price"],0);
            echo json_encode($row);
        }
        
        public static function postFastOrder(FilteredMap $params) {
            global $host;
            global $db;
            global $sm;
            global $language_file;
            global $fast_orders_table;
            global $emails_test;
            global $ordersEmail;
            global $user;

            $product_id = $params->getInt("product_id");
            $variant_id = $params->getInt("variant_id");
            $variant_name = $params->getString("variant_name");
//            $option_id = $params->getInt("option_id");
            $name = $params->getString("name");
            $phone = $params->getString("phone");
            $email = $params->getString("email");
            $quantity = $params->getInt("quantity");
            $user_id = $user["id"];
//            $agree_terms = $params->getInt("agree_terms");
//            $agree_terms_gdpr = $params->getInt("agree_terms_gdpr");
            $agree_terms = 1;
            $agree_terms_gdpr = 1;

            if ( /*$name &&*/ $phone && $agree_terms && $agree_terms_gdpr/*&& $email */){
                //$message .= "Име: ".$name."<br />";
                $message .= "Телефон: ".$phone."<br />";
                //$message .= "E-mail: ".$email."<br />";
                $message .= "Product: <a href='".$host."productbg/".$product_id."' target='_blank'>".$host."productbg/".$product_id."</a><br />";
                if($variant_id){
                    $message .= "Вариант: ".$variant_name."<br />";
                }
                $message .= "Количество: ".$quantity."<br />";
                $fields = array(
                                    "name" => $name,
                                    "user_id" => $user_id > 0 ? $user_id : 0,
                                    "phone" => $phone,
                                    "email" => $email,
                                    "product_id" => $product_id,
                                    "variant_id" => $variant_id,
                                    "variant_name" => $variant_name,
                                    "agree_terms" => $agree_terms,
                                    "agree_terms_gdpr" => $agree_terms_gdpr,
                                    "quantity" => $quantity,
                                    "postdate" => time(),
                                    "postdate_simple" => date("Y-m-d"),
                                    "ip" => $_SERVER["REMOTE_ADDR"],
                                );
                $db->autoExecute($fast_orders_table, $fields, DB_AUTOQUERY_INSERT);
                $id = mysqli_insert_id($db->connection);
                $order_number = "ARTHABEAUTY".date("Y").str_pad($id, 6, "0", STR_PAD_LEFT);

                $db->autoExecute($fast_orders_table, array("order_number" => $order_number), DB_AUTOQUERY_UPDATE, " id = '".$id."' ");

                $subject = "Бърза поръчка #".$order_number;
                foreach($emails_test as $v){
                    mailSender($v, $subject, $message);
                }
                mailSender($ordersEmail, $subject, $message);
                //mailSender($row["email"], $subject, $message);
                
                $sm->configLoad($language_file);
                $configVars = $sm->getConfigVars();
                echo $configVars["message_description_700"];
            }else{
                echo "Невалидна информация";
            }
        }
        
        /**
         * 
         * @global DB $db
         * @global type $sm
         * @global type $lng
         * @global type $carts_table
         * @global type $htaccess_file
         * @param FilteredMap $params
         */
        public function addToCart(FilteredMap $params){
			global $db;
			global $sm;
			global $carts_table;
			global $carts_products_table;
            global $products_table;
			global $htaccess_file;
			global $variants_table;
			
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();

            $id = $params->getInt("product_id");

            $totalQuantity = $params->getInt("quantity");

            $option_id = null;
            if ($params->has("option_id")) {
                $option_id = $params->getInt("option_id");
            }

			// Check product quantity
            if ($_SESSION["cart_id"]){
                $cart_id = (int)$_SESSION["cart_id"];
                $sqlCheckQuantity = "SELECT * FROM ".$carts_products_table." WHERE cart_id = ".$cart_id . " AND edate = 0 AND product_id = ". $id;
                if ($option_id) {
                    $sqlCheckQuantity .= " AND option_id = ".$option_id;
                }
                $cartProduct = $db->getRow($sqlCheckQuantity); safeCheck($cartProduct);

                $totalQuantity += $cartProduct['quantity'];
            }

            if ($option_id) {
                $product = $db->getRow("SELECT * FROM ".$variants_table." WHERE product_id = " .$id . " AND option_id = " . $option_id); safeCheck($product);
            } else {
                $product = $db->getRow("SELECT * FROM ".$products_table." WHERE id = ".$id); safeCheck($product);
            }

            if ($product['quantity'] < $totalQuantity) {
                header('Content-Type: application/json');
                echo json_encode(['error' => true, 'message' => 'Недостатъчна наличност', 'quantity' => $totalQuantity, 'available' => $product['quantity']]);
                exit;
            }

			if ($_SESSION["cart_id"]){
				$cart_id = (int)$_SESSION["cart_id"];
                $cart = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = ".$cart_id); safeCheck($cart);
                $cart_id = $cart["id"];
			} else{
				$cart_id = 0;
			}
			
			if ($id){
				if (!$cart_id){
					$res = $db->autoExecute($carts_table, array("postdate" => time(), "status" => "0", "session_id" => session_id(), "ip" => $_SERVER["REMOTE_ADDR"], "user_id" => $_SESSION["userID"]), DB_AUTOQUERY_INSERT ); safeCheck($res);
					$cart_id = mysqli_insert_id($db->connection);
					$this->addProductToCart($id, $cart_id, $params, $params->getInt("quantity"));
					
					$_SESSION["cart_id"] = $cart_id;
				}else{
                    $quantity = $params->has("quantity") && $params->getInt("quantity") > 1 ? $params->getInt("quantity") : 1;
					$this->addProductToCart($id, $cart_id, $params, $quantity);
				}
				header("Location: /".$htaccessVars["htaccess_cart"]."/");
				die();
			}
            
		}
        
        public function addProductToCart($product_id, $cart_id, $params, $quantity = 1 ){
			global $db;
			global $lng;
			global $products_table;
			global $products_images_table;
			global $brands_table;
			global $options_table;
			global $variants_table;
			global $carts_products_table;
            global $allowZeroQuantityVariantOrder;

            $variantQuantitySQL = " AND quantity > 0";
            if($allowZeroQuantityVariantOrder){
                $variantQuantitySQL = "";
            }
			
			$option_id = $params->getInt("option_id");
            $sql_option = "";
			if ( $option_id ){
				$sql_option = " AND option_id = ".$option_id;
				$option = $db->getRow("SELECT * FROM ".$options_table." WHERE edate = 0 AND id = '".$option_id."'"); safeCheck($option);
			}
			
			$product = $db->getRow("SELECT products.*,
                                        products.name_{$lng} AS name,
                                        products.htaccess_url_{$lng} AS htaccess_url,
                                        (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE products.brand_id = brands.id) AS brand_name,
                                        (SELECT images.id FROM ".$products_images_table." AS images WHERE images.product_id = products.id AND images.edate = 0 ORDER BY pos LIMIT 1) AS product_image_id
                                    FROM ".$products_table." AS products
                                    WHERE id = ".$product_id."
								"); safeCHeck($product);
            $check_product = $db->getRow("SELECT id, quantity, product_weight FROM ".$carts_products_table." WHERE product_id = ".$product_id." AND cart_id = ".$cart_id." {$sql_option} AND edate = 0"); safeCheck($check_product);
            
            $variant = $db->getRow("SELECT * FROM ".$variants_table." WHERE product_id = ".$product_id." AND option_id = '".$option_id."' AND edate = 0 {$variantQuantitySQL}"); safeCheck($variant);
            
            $price_specialoffer = getSpecialOfferPrice($product["id"], $product["brand_id"], 1, $option_id);
            
            $helpers = new Helpers();
            $user_group_id = Helpers::getCurentUserGroupId();
            
            if ( $variant["price"] ){
                $price_use = $helpers->getDiscountedPrice($variant["price"], 0, $user_group_id, false);
                $price_bonus_points_use = $variant["bonus_points"];
                $product_weight = $variant["weight"];
				$price_supply = $variant['price_supply'];
				$price_profit = $variant['price_profit'];
            }else{
                $price_use = $helpers->getDiscountedPrice($product["price"], 0, $user_group_id, false);
                $product_weight = $product["weight"];
				$price_supply = $product['price_supply'];
				$price_profit = $product['price_profit'];
            }
            $price_use_discounted = 0.0;
            if ( $price_specialoffer["price_specialoffer"] > 0.0 ){
                if ( $variant["price"] > 0.0 ){
                    $price_use = $helpers->getDiscountedPrice($variant["price"], 1, $user_group_id, false);
					$price_supply = $variant['price_supply'];
					$price_profit = $variant['price_profit'];
                }else{
                    $price_use = $helpers->getDiscountedPrice($product["price"], 1, $user_group_id, false);
					$price_supply = $product['price_supply'];
					$price_profit = $product['price_profit'];
                }
                $price_use_discounted = $helpers->getDiscountedPrice($price_specialoffer["price_specialoffer"], 1, $user_group_id, false);
            }
            
            $product_price_total = $price_use_discounted > 0.0 ? $price_use_discounted * $quantity : $price_use * $quantity;
            $product_weight_total = $product_weight * $quantity;
            
            $fields = array(
                                "cart_id" => $cart_id,
                                "product_id" => $product_id,
                                "option_id" => $variant["option_id"],
                                "brand_id" => $product["brand_id"],
                                "product_image_id" => $product["product_image_id"],
                                "product_code" => $product["product_image_id"],
                                "product_name" => $product["name"],
                                "product_code" => $product["code"],
                                "product_price" => $price_use,
                                "product_price_supply" => $price_supply,
                                "product_price_profit" => $price_profit,
                                "product_price_discount" => $price_use_discounted,
                                "product_price_total" => $product_price_total,
                                "product_weight" => $product_weight,
                                "product_weight_total" => $product_weight_total,
                                "brand_name" => $product["brand_name"],
                                "quantity" => $quantity,
                                "postdate" => time(),
                                "ip_address" => $_SERVER["REMOTE_ADDR"]
                            );
//            if ( $_SERVER["REMOTE_ADDR"] == "84.201.192.58" ){}
            
            if ($check_product["quantity"] == 0){
                $res = $db->autoExecute($carts_products_table, $fields, DB_AUTOQUERY_INSERT ); safeCheck($res);
            }else{
                $fields["quantity"] = $quantity + $check_product["quantity"];
                $fields["product_price_total"] = $price_use_discounted > 0.0 ? $price_use_discounted * $fields["quantity"] : $price_use * $fields["quantity"];
                $fields["product_weight_total"] = $fields["quantity"] * $check_product["product_weight"];
                $res = $db->autoExecute($carts_products_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$check_product["id"]."'" ); safeCheck($res);
            }
		}
        
        public function postProductToFavourites(FilteredMap $params, int $return_type = 3){
			global $db;
			global $user;
			global $favourites_table;
			
			$act = $params->getString("act");
			$product_id = $params->getInt("product_id");
			
            $total_count = 0;
			if ( $user["id"] && $product_id ){
				if ( $act == "remove" ){
					$res = $db->autoExecute($favourites_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, " user_id = ".$user["id"]." AND product_id = ".$product_id); safeCheck($res);
				}
				if ( $act == "add" ){
                    $check = $db->getRow("SELECT * FROM ".$favourites_table." WHERE edate = 0 AND user_id = '".$user["id"]."' AND product_id = ".$product_id); safeCheck($check);
                    if ( !$check["id"] ){
                        $fields = array(
                                            "ip" => $_SERVER["REMOTE_ADDR"],
                                            "user_id" => $user["id"],
                                            "product_id" => $product_id,
                                            "postdate" => time()
                                        );
                        $res = $db->autoExecute($favourites_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
                    }
                    $id = mysqli_insert_id($db->connection);
				}
                $total_count = $db->getRow("SELECT COUNT(id) AS cntr FROM ".$favourites_table." WHERE edate = 0 AND user_id = '".$user["id"]."'"); safeCheck($total_count);
                
			}
            $result["id"] = $id;
            $result["total_count"] = $total_count["cntr"];
            if($return_type == 3){
                echo json_encode($result);
            }
            
			return $result;
		}
        
        public static function getSuggestedProducts($limit = 0) {
            global $db;
            global $lng;
            global $products_table;
            global $products_images_table;
            global $brands_table;
            global $product_types_table;
            global $products_comments_table;
            
            if($limit){
                $sql_limit = " LIMIT {$limit} ";
            }
            
            $sqlItems = "SELECT
                            products.*,
                            products.name_{$lng} AS name,
                            products.excerpt_{$lng} AS excerpt,
                            products.htaccess_url_{$lng} AS htaccess_url,
                            products.description_{$lng} AS description,
                            products.meta_title_{$lng} AS meta_title,
                            products.meta_keywords_{$lng} AS meta_keywords,
                            products.meta_description_{$lng} AS meta_description,
                            products.meta_metatags_{$lng} AS meta_metatags,
                            -- (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
                            (SELECT pi.pic FROM " . $products_images_table . " AS pi WHERE pi.product_id = products.id AND pi.edate = 0 ORDER BY pi.pos LIMIT 1) as mainPic,
                            -- (SELECT product_types.name_{$lng} FROM ".$product_types_table." AS product_types WHERE products.product_type_id = product_types.id) AS product_type_name,
                            -- (SELECT COUNT(rating2.id) FROM ".$products_comments_table." AS rating2 WHERE rating2.product_id = products.id AND rating2.edate = 0) AS reviews_count,
                            (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id) AS rating
                        FROM 
                            ".$products_table." AS products
                        WHERE
                            products.active = 1
                        AND products.edate = 0
                        AND products.is_suggested = 1
                        ORDER BY products.postdate DESC
                        {$sql_limit}";                        
            $items = $db->getAll($sqlItems); safeCheck($items);
            
//            $helpers = new Helpers();
//            $user_group_id = Helpers::getCurentUserGroupId();
//            
//            foreach ($items as $k => $v) {
//                $price_specialoffer = getSpecialOfferPrice($v["id"], $v["brand_id"], 1);
//                
//                if ( $price_specialoffer["price_specialoffer"] > 0.0 ){
//                    $v["price"] = $helpers->getDiscountedPrice($v["price"], 1, $user_group_id);
//                    $v["price_specialoffer"] = $helpers->getDiscountedPrice($price_specialoffer["price_specialoffer"], 1, $user_group_id);
//                    $v["price_specialoffer_text"] = $price_specialoffer["price_specialoffer_text"];
//                    $v["bonus_points"] = $price_specialoffer["bonus_points"];
//                    $v["bonus_points_win"] = round($price_specialoffer["price_specialoffer"] * 1, 0) ;
//                    $v["discount_date_to"] = $price_specialoffer["discount_date_to"];
//                }else{
//                    $v["price"] = $helpers->getDiscountedPrice($v["price"], 0, $user_group_id);
//                    $v["bonus_points_win"] = round($v["price"] * 1, 0);
//                }
//                
//                $v["excerpt"] = htmlspecialchars_decode($v["excerpt"]);
//                $v["description"] = htmlspecialchars_decode($v["description"]);
//                
//                $items[$k] = $v;
//            }
            
            return $items;
        }
        
        public static function getFavouriteProducts($limit = 0, $user_id = null) {
            global $db;
            global $lng;
            global $user;
            global $products_table;
            global $favourites_table;
            global $products_images_table;
            global $brands_table;
            global $product_types_table;
            global $products_comments_table;
            
            if(!$user_id){
                $user_id = isset($user['id']) && $user['id'] > 0 ? (int)$user['id'] : 0;
            }
            
            if($limit){
                $sql_limit = " LIMIT {$limit} ";
            }
            
            $sqlItems = "SELECT
                            products.*,
                            products.name_{$lng} AS name,
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
                        INNER JOIN {$favourites_table} AS f ON products.id = f.product_id
                        WHERE
                            products.active = 1
                        AND products.edate = 0
                        AND f.user_id = {$user_id}
                        AND f.edate = 0
                        ORDER BY f.postdate DESC
                        {$sql_limit}";                        
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
                
                $v["excerpt"] = htmlspecialchars_decode($v["excerpt"]);
                $v["description"] = htmlspecialchars_decode($v["description"]);
                
                $items[$k] = $v;
            }
            
            return $items;
        }
        
        public function getFavouritesPage($page = 0){
            global $db;
			global $sm;
			global $lng;
            global $user;
            global $host;
            global $products_table;
            global $products_images_table;
            global $product_types_table;
            global $brands_table;
            global $products_comments_table;
            global $favourites_table;
            global $language_file;
            global $htaccess_file;
			global $limit;
			
            $sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
            
            if(!$user['id']){
                header("Location: /".$htaccessVars["htaccess_messages"]."/413");
                die();
            }
            
            $current_page = is_numeric($page) && $page > 0 ? (int)$page : 1;
            $limit = (int)$limit;
            
            $results = $db->getRow("SELECT 
                                        COUNT(DISTINCT products.id) as cntr
                                    FROM 
                                        ".$products_table." AS products
                                    INNER JOIN {$favourites_table} AS f ON products.id = f.product_id
                                    WHERE
                                        products.active = 1
                                    AND products.edate = 0
                                    AND f.user_id = {$user['id']}
                                    AND f.edate = 0"); safeCheck($results);
            $resultsCount = (int)$results["cntr"];
            $sm->assign("resultsCount", $resultsCount);
            
            $page_url = "/".$htaccessVars["htaccess_favourites"];
            
            $total_pages = (int)ceil($resultsCount/$limit); //break records into pages
            $start = (($current_page-1) * $limit); //get starting position
            $sql_limit = " LIMIT {$start}, {$limit}";
            $pages = Helpers::paginate($current_page, $total_pages, $page_url);
            $sm->assign("pages", $pages);
            
            $sqlItems = "SELECT
                            products.*,
                            products.name_{$lng} AS name,
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
                        INNER JOIN {$favourites_table} AS f ON products.id = f.product_id
                        WHERE
                            products.active = 1
                        AND products.edate = 0
                        AND f.user_id = {$user['id']}
                        AND f.edate = 0
                        ORDER BY f.postdate DESC
                        {$sql_limit}";
                        
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
                
                $v["excerpt"] = htmlspecialchars_decode($v["excerpt"]);
                $v["description"] = htmlspecialchars_decode($v["description"]);
                
                $items[$k] = $v;
            }
            
            $sm->assign("items", $items);
			
			$sm->assign("pages", $pages);
			$sm->assign("page", $page);
            
            $sm->configLoad($language_file);
            $configVars = $sm->getConfigVars();
            $breadcrumbs = '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>';
            $breadcrumbs .= '<span>'.$configVars["favourites_breadcrumbs"].'</span>';
            $sm->assign("breadcrumbs", $breadcrumbs);
            
			$sm->assign("page_favourites", 1);
			
			$sm->display("favourites.html");
		}
		
		function genFeed(){
			global $db;
			global $sm;
			global $lng;
			global $user;
			global $categories_table;
      global $products_table;
      global $favourites_table;
      global $product_to_category_table;
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
			global $host;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
//            $code = $params->getString("code");
//            $sm->assign("code", $code);
 
            
            $sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();

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
                        INNER JOIN {$product_to_category_table} AS ptc ON ptc.product_id = products.id 
                        WHERE
                            products.edate = 0
                        AND products.active = 1
                        {$sql_order_by}
                        ";


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
                
                $option_groups = Products::getOptionGroups($v["id"]);
                $v["option_groups"] = $option_groups;
                    
                $items[$k] = $v;
            }
            
      echo "<table><tr>";
			echo "<td>ID</td><td>Item title</td><td>Final URL</td><td>Image URL</td><td>Item description</td><td>Item category</td><td>Price</td>";
			echo "</tr>";
			foreach($items as $k => $v){
				
				$category = $db->getRow("SELECT *, (SELECT categories.name_{$lng} AS name FROM ".$categories_table." AS categories WHERE ptc.category_id = categories.id) AS name FROM ".$product_to_category_table." AS ptc WHERE ptc.product_id = ".$v["id"]." ORDER BY ptc.category_id"); safeCheck($category);
				if ($category["category_id"]){
                $categoryMain = $db->getRow("SELECT id, name_{$lng} AS name FROM ".$categories_table." WHERE edate = 0 AND id = '".$category["category_id"]."'"); safeCheck($categoryMain);
        }
				
				echo "<tr><td>".$v["id"]."</td><td>".$v["name_en"].",".$v["name"]."</td><td>".$host. 
				($v["htaccess_url"] ? $v["htaccess_url"]: $htaccessVars["htaccess_product"]."/".$v["id"])."</td><td>".$host."files/".$v["mainPic"]."</td><td>".strip_tags($v["excerpt"])."</td><td>".$categoryMain["name"]."</td><td>".$v["price"]."</td></tr>";
			}
			echo "</table>";
      //print_r($items);
		}
        
	}