<?php
	class Carts extends Settings{
		
		public $pagination = "";
		
        function getRecordSimple($id = 0){
            global $db;
			global $carts_table;
            
            $row = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$id."'"); safeCheck($row);
            
            return $row;
        }
        
        public static function getCountNewOrders(){
            global $db;
			global $carts_table;
            
            $row = $db->getRow("SELECT
                                    count(id) AS cntr 
                                FROM 
                                    ".$carts_table."
                                WHERE 
                                    edate = 0
                                AND status = 1
                                AND finalised = 1 "); safeCheck($row);
            return $row["cntr"] > 0 ? (int)$row["cntr"] : 0;
        }
        
		function getRecord($id = 0){
			global $db;
			global $carts_table;
            global $carts_user_table;
            global $cities_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT
                                    c.*,
                                    cu.email,
                                    cu.delivery_name,
                                    cu.delivery_first_name,
                                    cu.delivery_last_name,
                                    cu.delivery_address_1,
                                    cu.delivery_address_2,
                                    cu.delivery_city_id,
                                    (SELECT c1.district_id FROM $cities_table AS c1 WHERE c1.id = cu.delivery_city_id AND c1.edate = 0 LIMIT 1) AS delivery_region_id,
                                    (SELECT c2.postcode FROM $cities_table AS c2 WHERE c2.id = cu.delivery_city_id AND c2.edate = 0 LIMIT 1) AS delivery_postcode,
                                    cu.delivery_phone,
                                    
                                    cu.billing_first_name,
                                    cu.billing_last_name,
                                    cu.billing_address_1,
                                    cu.billing_address_2,
                                    cu.billing_city_id,
                                    (SELECT c3.district_id FROM $cities_table AS c3 WHERE c3.id = cu.billing_city_id AND c3.edate = 0 LIMIT 1) AS billing_region_id,
                                    (SELECT c4.postcode FROM $cities_table AS c4 WHERE c4.id = cu.billing_city_id AND c4.edate = 0 LIMIT 1) AS billing_postcode,
                                    cu.billing_phone,
                                    cu.user_comments
                                FROM
                                    $carts_table AS c
                                INNER JOIN $carts_user_table AS cu ON cu.cart_id = c.id
                                WHERE
                                    c.id = $id
                                AND c.edate = 0"); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow($params){
			global $db;
			global $carts_table;
			global $carts_user_table;
            
            $act = $params->getString("act");
			$id = $params->getInt("id");
            
//            $delivery_name = $params->getString("delivery_name");
            $delivery_first_name = $params->getString("delivery_first_name");
            $delivery_last_name = $params->getString("delivery_last_name");
            $delivery_address_1 = $params->getString("delivery_address_1");
            $delivery_address_2 = $params->getString("delivery_address_2");
            //$delivery_region = $params->getString("delivery_region");
            $delivery_phone = $params->getString("delivery_phone");
            //$delivery_postcode = $params->getString("delivery_postcode");
            $delivery_city_id = $params->getInt("delivery_city_id");
            //$delivery_city = $params->getString("delivery_city");
            //$country_id = $params->getInt("country_id");
//            $billing_name = $params->getString("billing_name");
            $billing_first_name = $params->getString("billing_first_name");
            $billing_last_name = $params->getString("billing_last_name");
            $billing_address_1 = $params->getString("billing_address_1");
            $billing_address_2 = $params->getString("billing_address_2");
            //$billing_region = $params->getString("billing_region");
            //$billing_city = $params->getString("billing_city");
            $billing_city_id = $params->getInt("billing_city_id");
            $email = $params->getEmail("email");
            //$billing_postcode = $params->getString("billing_postcode");
            $billing_phone = $params->getString("billing_phone");
            //$billing_country_id = $params->getInt("billing_country_id");
            
//            $delivery_type_id = $params->getInt("delivery_type_id");
//            $delivery_type = $params->getInt("delivery_type");
            $delivery_office_id = $params->getInt("delivery_office_id");
            $user_comments = $params->getString("user_comments");
            
			$fields = array(
                "delivery_office_id" => $delivery_office_id,
                //"finalised" => $params->getInt("finalised"),
                "session_id" => session_id(),
                'user_id'	=> $params->getInt("user_id"),
                "ip" => $_SERVER["REMOTE_ADDR"],
			);
			
			$fields_user = array(
                "delivery_first_name" => $delivery_first_name,
                "delivery_last_name" => $delivery_last_name,
                "delivery_address_1" => $delivery_address_1,
                "delivery_address_2" => $delivery_address_2,
                //"delivery_region" => $delivery_region,
                //"delivery_city" => $delivery_city,
                "delivery_city_id" => $delivery_city_id,
                //"delivery_postcode" => $delivery_postcode,
                "delivery_phone" => $delivery_phone,
                //"country_id" => $country_id,
                "billing_first_name" => $billing_first_name,
                "billing_last_name" => $billing_last_name,
                "billing_address_1" => $billing_address_1,
                "billing_address_2" => $billing_address_2,
                "billing_phone" => $billing_phone,
                //"billing_region" => $billing_region,
                //"billing_city" => $billing_city,
                "billing_city_id" => $billing_city_id,
                //"billing_postcode" => $billing_postcode,
                //"billing_country" => $billing_country_id,
                "user_comments" => $user_comments,
                "cart_id" => $id,
                'user_id'	=> $params->getInt("user_id"),
                "email" => $email,
                "ip" => $_SERVER['REMOTE_ADDR'],
                "postdate" => time()
            );
			
			if($act == "add") {
                shiftPos($db, $carts_table);
                
                $fields["add_from_cms"] = 1;
                $fields["postdate"] = time();
				
				$res = $db->autoExecute($carts_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
                $fields_user["cart_id"] = $id;
                $res = $db->autoExecute($carts_user_table, $fields_user, DB_AUTOQUERY_INSERT); safeCheck($res);
			}
			
			if($act == "edit") {
                $fields["edit_from_cms"] = 1;
				$res = $db->autoExecute($carts_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
                $res = $db->autoExecute($carts_user_table, $fields_user, DB_AUTOQUERY_UPDATE, " cart_id = '".$id."' "); safeCheck($res);
            }
            
            $total_amount_corrected = $this->cartCalculateTotal($id, $row["user_id"]);
            
            header("Location: carts_ae.php?act=edit&id=".$id);
            die();
            
		}
        
        public function getCitiesByDistrict($id) {
            global $db;
            global $lng;
            global $cities_table;
            
            $district_id = (int)$id;
            
            $cities = $db->getAll("SELECT
                                        id, 
                                        name_{$lng} AS name
                                    FROM ".$cities_table."
                                    WHERE edate = 0 
                                    AND district_id = '{$district_id}'
                                    ORDER BY name_{$lng}"); safeCheck($cities);
            return $cities;
        }
        
        public function cartCalculateTotal($id, $user_id = 0){
			global $db;
			global $carts_table;
			global $users_table;
			global $carts_products_table;
			
			$cart = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$id."'"); safeCheck($cart);
			$cart_products = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE cart_id = '".$id."' AND edate = 0"); safeCheck($cart_products);
			
            $total_amount = 0;
            $total_amount_supply = 0;
            $total_amount_profit = 0;
			$total_weight = 0;
			foreach($cart_products as $k => $v){
				$tmp_total_amount = 0;
				$tmp_total_weight = 0;
				$tmp_total_amount = $v["quantity"] * $v["product_price"];
				$tmp_total_amount_supply = $v["quantity"] * $v["product_price_supply"];
				$tmp_total_amount_profit = $v["quantity"] * $v["product_price_profit"];
				$tmp_total_weight = $v["quantity"] * $v["product_weight"];
                
				$total_amount += $tmp_total_amount;
				$total_amount_supply += $tmp_total_amount_supply;
				$total_amount_profit += $tmp_total_amount_profit;
				$total_weight += $tmp_total_weight;
				$cart_products[$k] = $v;
			}
			$cart["weight"] = $total_weight;
			
			$user = $db->getRow("SELECT * FROM ".$users_table." WHERE id = '".$user_id."'"); safeCheck($user);
            if ($user && ($user["bonus_points"] || $user["bonus_points_tmp"]) ){
                if ( $user["bonus_points"] > 0 ){
                    $bonus_points = $user["bonus_points"];
                }
                if ( $user["bonus_points_tmp"] > 0 ){
                    $bonus_points = $user["bonus_points_tmp"];
                }
                $bonus_points_currency = $bonus_points / 100;
                if ( $bonus_points_currency >= $total_amount ){
                    $bonus_points_amount = $total_amount;
                    $bonus_points_number = $total_amount * 100;
                }else{
                    $bonus_points_amount = $bonus_points / 100;
                    $bonus_points_number = $bonus_points;
                }
            }

            $bonus_points_won_amount = round(($total_amount * 1)/100);
            $bonus_points_won_number = round(($total_amount * 1));

            $fields_cart = array(
                "total_amount" => $total_amount,
                "total_amount_supply" => $total_amount_supply,
                "total_amount_profit" => $total_amount_profit,
                "subtotal_amount" => $total_amount,
                "bonus_points_amount" => $bonus_points_amount,
                "bonus_points_number" => $bonus_points_number,
                "bonus_points_won_amount" => $bonus_points_won_amount,
                "bonus_points_won_number" => $bonus_points_won_number,
                "weight" => $total_weight
            );
            
            if ( $user_id && $user["bonus_points"] > 0 ){
                $res = $db->autoExecute($users_table, array("bonus_points_tmp" => $user["bonus_points"], "bonus_points" => 0), DB_AUTOQUERY_UPDATE, "id = '".$user_id."'"); safeCheck($res);
            }

            $discounts = $this->getDiscounts($id);

            $fields_cart["discount_amount"] = round($discounts["discount_amount"],2);
            $fields_cart["discount_free_delivery"] = $discounts["discount_free_delivery"];

            $fields_cart["discount_amount_delivery"] = 0;

//            if( $total_amount >= 49 ){
//                if ( $cart["weight"] < 1.0 ){
//                    $fields_cart["discount_free_delivery"] = 1;
//                    $fields_cart["discount_amount_delivery"] = 0; // Remove 20% discount if previously calculated;
//                }
//                if( $total_amount >= 100 ){
//                    if($cart["weight"] < 2.0){
//                        $fields_cart["discount_free_delivery"] = 1;
//                    }else{
//                        $fields_cart["discount_free_delivery"] = 0;
//                    }
//                    $fields_cart["discount_amount_delivery"] = number_format($total_amount * 0.02,2); // Calculate 20% discount;
//                }
//            }
            $discount_amount_delivery = $fields_cart["discount_amount_delivery"];
            
            $cart["discount_amount"] = number_format($fields_cart["discount_amount"],2);
            $cart["discount_free_delivery"] = $fields_cart["discount_free_delivery"];
            if($cart["pharmacy_id"] > 0){
                $delivery_amount["codAmount"] = 0.0;
                $delivery_amount["deliveryAmount"] = 0.0;
            }else{
                $receiverSiteId = Delivery::getCourierSiteId((int)$cart["delivery_type"], $cart["delivery_region"], $cart["delivery_city"]);
                if ( $cart["delivery_type"] == 1 ){
                    $delivery_amount = Delivery::getDeliveryAmountAndCodBase((int)$cart["delivery_type"], $cart["total_amount"], $cart["weight"], $receiverSiteId);
                }else if($cart["delivery_type"] == 2){
                    $delivery_amount = Delivery::getDeliveryAmountAndCodBase((int)$cart["delivery_type"], $cart["total_amount"], $cart["weight"], $receiverSiteId, (int)$cart["delivery_office_id"]);
                }
            }

            $fields_cart["cod_amount"] = (double)$delivery_amount["codAmount"];
            $fields_cart["delivery_amount"] = (double)$delivery_amount["deliveryAmount"];

            $res = $db->autoExecute($carts_table, $fields_cart, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);

            if ( $cart["discount_free_delivery"] ){
                $total_amount_corrected = number_format($total_amount - $bonus_points_amount - $discount_amount_delivery,2);
            }else{
                $total_amount_corrected = number_format($total_amount + $fields_cart["delivery_amount"] - $bonus_points_amount - $discount_amount_delivery,2);
            }

            return $total_amount_corrected;
		}
        
        public function getDiscounts($cart_id){
            global $db;
            global $products_table;
            global $carts_table;
            global $carts_products_table;
            global $discounts_table;
            global $product_to_discount_table;
            global $brand_to_discount_table;
            global $category_to_discount_table;
            global $discount_to_discount_table;
            global $product_to_category_table;

            $cart = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$cart_id."'"); safeCheck($cart);
            $cart_products = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE cart_id = '".$cart_id."' AND edate = 0"); safeCheck($cart_products);

            $date_today = date("Y-m-d");

            $sql = "SELECT * FROM ".$discounts_table." AS discounts WHERE edate = 0 AND active = '1' AND discount_date_from <= '".$date_today."' AND discount_date_to >= '".$date_today."' ";
            $discounts = $db->getAll($sql); safeCheck($discounts);

            $discount_results["delivery_amount"] = 0;
            $discount_results["discount_amount"] = 0;
            //$discount_results["discount_free_delivery"] = 0;

            $total_amount = $cart["total_amount"];
            $discount_value = 0;
            foreach( $discounts as $k => $v ){
                //$discount_discounts = $db->getAll("SELECT * FROM ".$discount_to_discount_table." WHERE discount_main_id = '".$v["id"]."'"); safeCheck($discount_discounts);
                $discount_products = $db->getAll("SELECT * FROM ".$product_to_discount_table." WHERE discount_id = '".$v["id"]."'"); safeCheck($discount_products);
                $discount_categories = $db->getAll("SELECT * FROM ".$category_to_discount_table." WHERE discount_id = '".$v["id"]."'"); safeCheck($discount_categories);
                $discount_brands = $db->getAll("SELECT * FROM ".$brand_to_discount_table." WHERE discount_id = '".$v["id"]."'"); safeCheck($discount_brands);

                if ( $v["discount_type"] == 1 ){
                    foreach($discount_products as $kk => $vv){
                        foreach($cart_products as $kkk => $vvv){
                            if ( $vvv["product_id"] == $vv["product_id"] ){
                                $product_discount = ($vvv["product_price_total"] * $v["discount_value"]/100);
                                if ( $v["items_count_exceeds"] > 0 && $v["items_count_exceeds"] <= $vvv["quantity"] ){
                                    $discount_value += $product_discount ;
                                }
                                if ( $v["items_count_exceeds"] == 0 ){
                                    $discount_value += $product_discount ;
                                }
                                //$vvv["product_price_total"] = $vvv["product_price_total"] - $product_discount;
                                $vvv["product_price_discount"] = (double)$product_discount;
                            }
                            $cart_products[$kkk] = $vvv;
                        }
                    }

                    foreach($discount_brands as $kk => $vv){
                        foreach($cart_products as $kkk => $vvv){
                            $product_tmp = $db->getRow("SELECT id FROM ".$products_table." WHERE brand_id = '".$vv["brand_id"]."' AND id = '".$vvv["product_id"]."'"); safeCheck($product_tmp);
                            if ( $product_tmp["id"] ){
                                if ( $vvv["product_price_discount"] > 0.0 ){
                                    $product_discount = (($vvv["product_price_total"] - $vvv["product_price_discount"]) * $v["discount_value"]/100);
                                }else{
                                    $product_discount = (($vvv["product_price_total"]) * $v["discount_value"]/100);

                                }

                                if ( $v["items_count_exceeds"] > 0 && $v["items_count_exceeds"] <= $vvv["quantity"] ){
                                    $discount_value += $product_discount;
                                }
                                if ( $v["items_count_exceeds"] == 0 ){
                                    $discount_value += $product_discount;
                                }
                                //$vvv["product_price_total"] = $vvv["product_price_total"] - $product_discount;
                                $vvv["product_price_discount"] = (double)$product_discount + $vvv["product_price_discount"] ;
                            }
                            $cart_products[$kkk] = $vvv;
                        }
                    }

                    if ( $_SERVER["REMOTE_ADDR"] == "84.201.192.58" ){
                        //dbg($discount_brands);
                    }

                    foreach($discount_categories as $kk => $vv){
                        foreach($cart_products as $kkk => $vvv){
                            $product_tmp = $db->getRow("SELECT product_id FROM ".$product_to_category_table." WHERE category_id = '".$vv["category_id"]."' AND product_id = '".$vvv["product_id"]."'"); safeCheck($product_tmp);
                            if ( $product_tmp["product_id"] ){
                                if ( $vvv["product_price_discount"] > 0.0 ){
                                    $product_discount = (($vvv["product_price_total"] - $vvv["product_price_discount"]) * $v["discount_value"]/100);
                                }else{
                                    $product_discount = (($vvv["product_price_total"]) * $v["discount_value"]/100);
                                }
                                if ( $v["items_count_exceeds"] > 0 && $v["items_count_exceeds"] <= $vvv["quantity"] ){
                                    $discount_value += $product_discount;
                                }
                                if ( $v["items_count_exceeds"] == 0 ){
                                    $discount_value += $product_discount;
                                }
                                //$vvv["product_price_total"] = $vvv["product_price_total"] - $product_discount;
                                $vvv["product_price_discount"] = (double)$product_discount + $vvv["product_price_discount"] ;
                            }
                            $cart_products[$kkk] = $vvv;
                        }
                    }

                }

                $total_amount = $total_amount - $discount_value;
                
//                if ( $v["discount_type"] == 2 && sizeof($discount_brands) == 0 && sizeof($discount_categories) == 0 && sizeof($discount_products) == 0 ){
//                    if ( $v["order_amount_exceeds"] <= $total_amount ){
//                        $discount_results["discount_delivery_amount"] = $cart["delivery_amount"];
//                        $discount_results["discount_free_delivery"] = 1;
//                    }
//                }elseif ($v["discount_type"] == 2 && sizeof($discount_products) > 0 ){
//                    foreach($cart_products as $k => $v){
//                        foreach($discount_products as $kk => $vv ){
//                            if ( $vv["product_id"] == $v["product_id"] ){
//                                $free_delivery = 1;
//                            }
//                        }
//                    }
//                    if ( $free_delivery ){
//                        $discount_results["discount_free_delivery"] = 1;
//                    }
//                }
            }
            $discount_results["discount_amount"] = $discount_value;
            
            return $discount_results;
        }
		
		function deleteRecord($id){
			global $db;
			global $carts_table;
			
			$fields = array(
								"edate" => time(),
								"edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($carts_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
        
        function deleteCartProductRecord($id){
			global $db;
			global $carts_products_table;
			
			$fields = array(
								"edate" => time(),
								"edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($carts_products_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
        
        function deleteCartProductByCartIdAndProductId($cart_id, $product_id){
			global $db;
			global $carts_products_table;
			
			$fields = array(
								"edate" => time(),
								"edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($carts_products_table, $fields, DB_AUTOQUERY_UPDATE, " cart_id = $cart_id AND product_id = $product_id "); safeCheck($res);
			
			return $res;
		}
		
//		function getSelectedCategories($id){
//			global $db;
//			global $carts_to_carts_categories_table;
//			
//			$all = $db->getAll("SELECT * FROM ".$carts_to_carts_categories_table." WHERE carts_id = '".$id."'"); safeCheck($all);
//			
//			return $all;
//		}
		
        /**
         * 
         * @global type $db
         * @global type $lng
         * @global type $carts_table
         * @param type $page
         * @param type $limit
         * @param FilteredMap $params
         * @return type
         */
		function getCarts($page = 0, $limit = 50, $params, $from_user = 0){
			global $db;
			global $sm;
			global $lng;
			global $carts_table;
			global $carts_products_table;
            global $carts_user_table;
            global $cities_table;
            global $products_table;

            if ( $params->has("Filter") || $from_user ){
                $id = $params->getInt("cart_id");
                $delivery_tracking_number = $params->getString("delivery_tracking_number");
                $name = $params->getString("name");
                $email = $params->getEmail("email");
                $phone = $params->getString("phone");
                $code = $params->getString("code");
                $barcode = $params->getString("barcode");
                $product_name = $params->getString("product_name");
                $product_id = $params->getInt("product_id");
                $brand_id = $params->getInt("brand_id");
                $user_id = $params->getInt("user_id");
                $date_from = html_entity_decode($params->getString("date_from"));
                $date_to = html_entity_decode($params->getString("date_to"));
				$sql_where = '';
                
                if ($name){
                    $sql_name = " AND ( LOWER(cu.delivery_first_name) LIKE '%".strtolower($name)."%' OR LOWER(cu.delivery_last_name) LIKE '%".strtolower($name)."%' OR LOWER(cu.billing_first_name) LIKE '%".strtolower($name)."%' OR LOWER(cu.billing_last_name)  LIKE '%".strtolower($name)."%'  )"; 
                    $sm->assign("name", $name);
                }
                if ($email){
                    $sql_email = " AND ( LOWER(cu.email) LIKE '%".strtolower($email)."%')"; 
                    $sm->assign("email", $email);
                }
                if ($id){
                    $sql_id = " AND carts.id = '".$id."'"; 
                    $sm->assign("id", $id);
                }
                if ($delivery_tracking_number){
                    $sql_delivery_tracking_number = " AND carts.delivery_tracking_number = '".$delivery_tracking_number."'"; 
                    $sm->assign("delivery_tracking_number", $delivery_tracking_number);
                }
                if ($phone){
                    $sql_phone = " AND ( LOWER(cu.billing_phone) LIKE '%".strtolower($phone)."%' OR LOWER(cu.delivery_phone) LIKE '%".strtolower($phone)."%')"; 
                    $sm->assign("name", $name);
                }
                if ($code){
                    $sql_code = " AND FIND_IN_SET('".$code."', (SELECT 
                                                                    GROUP_CONCAT(DISTINCT (SELECT products.code FROM ".$products_table." AS products WHERE products.id = cp.product_id )) 
                                                                FROM 
                                                                    ".$carts_products_table." AS cp
                                                                WHERE 
                                                                    cp.edate = 0 
                                                                AND cp.cart_id = carts.id))"; 
                    $sm->assign("code", $code);
                }
                if ($barcode){
                    $sql_barcode = " AND FIND_IN_SET('".$barcode."', (SELECT 
                                                                    GROUP_CONCAT(DISTINCT (SELECT products.barcode FROM ".$products_table." AS products WHERE products.id = cp.product_id )) 
                                                                FROM 
                                                                    ".$carts_products_table." AS cp
                                                                WHERE 
                                                                    cp.edate = 0 
                                                                AND cp.cart_id = carts.id))"; 
                    $sm->assign("barcode", $barcode);
                }
				
				if ( $user_id || $from_user ){
					$sql_where .= " AND carts.user_id = '".$user_id."'"; 
					$sm->assign("user_id", $user_id);
				}
				
				if ($date_from){
					$date_tmp = explode('/', $date_from);
					$date_from_time = mktime(0,0,0,$date_tmp[1],$date_tmp[0],$date_tmp[2]);
                    $sql_where .= " AND carts.postdate >= '".$date_from_time."'"; 
                    $sm->assign("date_from", $date_from);
                }
				if ($date_to){
					$date_tmp = explode('/', $date_to);
					$date_to_time = mktime(0,0,0,$date_tmp[1],$date_tmp[0],$date_tmp[2]);
                    $sql_where .= " AND carts.postdate < '".$date_to_time."'"; 
                    $sm->assign("date_to", $date_to);
                }
                if ($product_id){
                    $sql_product_name = " AND FIND_IN_SET('".$product_id."', (SELECT 
                                                                    GROUP_CONCAT(DISTINCT cp.product_id) 
                                                                FROM 
                                                                    ".$carts_products_table." AS cp
                                                                WHERE 
                                                                    cp.edate = 0 
                                                                AND cp.cart_id = carts.id))"; 
                    $sm->assign("product_id", $product_id);
                }
				if ($brand_id){
                    $sql_brands = " AND FIND_IN_SET('".$brand_id."', (SELECT 
                                                                    GROUP_CONCAT(DISTINCT (SELECT products.brand_id FROM ".$products_table." AS products WHERE products.id = cp.product_id )) 
                                                                FROM 
                                                                    ".$carts_products_table." AS cp
                                                                WHERE 
                                                                    cp.edate = 0 
                                                                AND cp.cart_id = carts.id))"; 
                    $sm->assign("brand_id", $brand_id);
                }
				
                $order_statuses_selected = $params->get("order_statuses_selected");
                if ( is_array($order_statuses_selected) && sizeof($order_statuses_selected) > 0 ){
                    $sql_order_status = "";
                    foreach($order_statuses_selected as $k => $v){
                        if ( $k == 0 ){
                            $sql_order_status .= " status = '".$v."' ";
                        }else{
                            $sql_order_status .= " OR status = '".$v."' ";
                        }
                    }
                    
                    $sql_order_status = " AND (". $sql_order_status .") ";
                }

            }
			
			
			$start = $page * $limit;
			$sql = "SELECT
						count(carts.id) AS cntr 
					FROM 
						" . $carts_table . " AS carts
					INNER JOIN $carts_user_table AS cu ON cu.id = (
						SELECT id
						FROM $carts_user_table AS cu2
						WHERE cu2.cart_id = carts.id
						ORDER BY id DESC
						LIMIT 1
					)
					WHERE 
						carts.edate = 0 
						{$sql_phone}
						{$sql_order_status}
						{$sql_name}
						{$sql_email}
						{$sql_delivery_tracking_number}
						{$sql_id}
						{$sql_product_name}
						{$sql_brands}
						{$sql_code}
						{$sql_barcode}
						{$sql_where}
						{$sql_order_status}";
			$pages = $db->getRow($sql); safeCheck($pages);
			$total_pages = ceil($pages["cntr"]/$limit);
			$generate_pages = '';
			
			if ( $page > 0 ){
				$generate_pages .= '<a href="carts.php?'.$_SERVER["QUERY_STRING"].'&page=0" class="first paginate_button paginate_button_enabled" tabindex="0">First</a>';
			}else{
				$generate_pages .= '<a href="#" class="first paginate_button paginate_button_disabled" tabindex="0">First</a>';
			}
			if ( $page > 0 ){
				$generate_pages .= '<a href="carts.php?'.$_SERVER["QUERY_STRING"].'&page='.($page-1).'" class="previous paginate_button paginate_button_enabled" tabindex="0">Previous</a>';
			}else{
				$generate_pages .= '<a href="#" class="previous paginate_button paginate_button_disabled" tabindex="0">Previous</a>';
			}
			
			$generate_pages .= '<span>';
			for ( $i = 0 ; $i < $total_pages; $i++ ){
				if ( $page == $i ){
					$generate_pages .= '<a href="carts.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" class="paginate_active" tabindex="0">'.($i+1).'</a>';
				}else{
					$generate_pages .= '<a href="carts.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" class="paginate_button" tabindex="0">'.($i+1).'</a>';
				}
			}
			$generate_pages .= '</span>';
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<a href="carts.php?'.$_SERVER["QUERY_STRING"].'&page='.($page+1).'" class="next paginate_button paginate_button_enabled" tabindex="0">Next</a>';
			}else{
				$generate_pages .= '<a href="#" class="next paginate_button paginate_button_disabled" tabindex="0">Next</a>';
			}
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<a href="carts.php?'.$_SERVER["QUERY_STRING"].'&page='.($total_pages-1).'" class="last paginate_button paginate_button_enabled" tabindex="0">Last</a>';
			}else{
				$generate_pages .= '<a href="#" class="last paginate_button paginate_button_disabled" tabindex="0">Last</a>';
			}
			
			$this->pagination = $generate_pages;
			
			$sql = "SELECT carts.*,
							cu.email,
							cu.delivery_name,
							cu.delivery_first_name,
							cu.delivery_last_name,
							cu.delivery_address_1,
							cu.delivery_address_2,
							cu.delivery_city_id,
							(SELECT c1.district_id FROM $cities_table AS c1 WHERE c1.id = cu.delivery_city_id AND c1.edate = 0 LIMIT 1) AS delivery_region_id,
							(SELECT c2.postcode FROM $cities_table AS c2 WHERE c2.id = cu.delivery_city_id AND c2.edate = 0 LIMIT 1) AS delivery_postcode,
							cu.delivery_phone,
							
							cu.billing_first_name,
							cu.billing_last_name,
							cu.billing_address_1,
							cu.billing_address_2,
							cu.billing_city_id,
							(SELECT c3.district_id FROM $cities_table AS c3 WHERE c3.id = cu.billing_city_id AND c3.edate = 0 LIMIT 1) AS billing_region_id,
							(SELECT c4.postcode FROM $cities_table AS c4 WHERE c4.id = cu.billing_city_id AND c4.edate = 0 LIMIT 1) AS billing_postcode,
							cu.billing_phone,
							cu.user_comments
						/*,
						(SELECT os.name_{$lng} FROM ".$order_statuses_table." AS os WHERE os.edate =0 AND os.id = carts.status) AS order_status_name,
						(SELECT os.color FROM ".$order_statuses_table." AS os WHERE os.edate =0 AND os.id = carts.status) AS order_status_color,
						(SELECT pharmacies.name_{$lng} FROM ".$pharmacies_table." AS pharmacies WHERE pharmacies.edate =0 AND pharmacies.id = carts.pharmacy_id) AS pharmacy_name
						*/
						FROM " . $carts_table . " AS carts
						INNER JOIN $carts_user_table AS cu ON cu.id = (
							SELECT id
							FROM $carts_user_table AS cu2
							WHERE cu2.cart_id = carts.id
							ORDER BY id DESC
							LIMIT 1
						)
						WHERE edate = 0
						{$sql_order_status}
						{$sql_name}
						{$sql_email}
						{$sql_delivery_tracking_number}
						{$sql_id}
						{$sql_brands}
						{$sql_code}
						{$sql_barcode}
						{$sql_where}
						{$sql_product_name}
						{$sql_order_status}
						{$sql_phone}
						ORDER BY carts.postdate DESC
						LIMIT {$start}, {$limit}";
            
			$items = $db->getAll($sql); safeCheck($items);
			foreach($items as $k => $v){
                $v["products"] = $this->getCartProducts($v["id"]);
				$v["bills"] = $this->getROI($v['products']);
				
                $items[$k] = $v;
            }
            
			return $items;
		}
        
        public function getROI($products) {
			foreach($products as $k => $v){
				$results[$v['product_id']]['client_price'] = $v["product_price_total"];
				$results[$v['product_id']]['supply_price'] = $v['quantity'] * $v["product_price_supply"];
				$results[$v['product_id']]['profit'] = $results[$v['product_id']]['client_price'] - $results[$v['product_id']]['supply_price'];
				$results['client_price'] += $results[$v['product_id']]['client_price'];
				$results['supply_price'] += $results[$v['product_id']]['supply_price'];
				$results['profit'] += $results[$v['product_id']]['profit'];
				
			}
			
			
			return $results;
		}
		
        public function getCartProducts($cart_id) {
            global $db;
            global $lng;
            global $products_table;
            global $carts_products_table;
            global $products_images_table;
            global $options_table;
            global $variants_table;
            global $option_groups_table;
            global $brands_table;
            global $collections_table;
            
            $cart = $db->getAll("SELECT
                                    cp.*,
                                    (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.id = cp.product_image_id) AS pic
                                FROM 
                                    ".$carts_products_table." AS cp
                                WHERE 
                                    cp.edate = 0 
                                AND cp.cart_id = '".$cart_id."'"); safeCheck($cart);
            //$total_weight = 0;
            //$total_postage_points = 0;
            foreach($cart as $k => $v){
                $product = $db->getRow("SELECT 
                													id, name_{$lng} AS name, barcode, name_en, 
																					(SELECT name_{$lng} FROM {$brands_table} WHERE id = brand_id) AS brand_name,
																					(SELECT name_{$lng} FROM {$collections_table} WHERE id = collection_id) AS col_name
                											  FROM ".$products_table." AS products WHERE edate = 0 AND id = '".$v["product_id"]."'"); safeCheck($product);
                $v["product"] = $product;
                //$total_weight += $product["product_weight"]*$v["quantity"];
                //$total_postage_points += $product["postage_points"]*$v["quantity"];
                $v["cart_price"] = number_format(($v["product_price"]+$v["choices_price"])*$v["quantity"],2);
                $vat_norounding = $v["cart_price"]*5/6;
                $v["cart_price_clear"] = number_format((($v["cart_price"]*5)/6), 2);
                $v["cart_price_w_vat"] = number_format($vat_norounding+($vat_norounding*$vat["vat_percent"])/100, 2);
                $v["cart_price_w_vat_total"] = number_format($v["cart_price_w_vat"]*$v["quantity"],2);
                
                $option = $db->getRow("SELECT *, option_text AS name, (SELECT og.name_{$lng} FROM ".$option_groups_table." AS og WHERE og.id = options.option_group_id) AS option_group_name FROM ".$options_table." AS options	WHERE options.id = '".$v["option_id"]."'"); safeCheck($option);
                $variant = $db->getRow("SELECT v.* FROM ".$variants_table." AS v WHERE v.option_id = '".$v["option_id"]."' AND v.product_id = '".$v["product_id"]."'"); safeCheck($variant);
                $v["option"] = $option;
                $v["variant"] = $variant;
                
                $tmp_total_amount = 0;
                if ( $v["product_price_discount"] > 0.0 ){
                    //$subtotal_amount += $v["quantity"] * $v["product_price_discount"];
                    $tmp_total_amount = $v["quantity"] * $v["product_price_discount"];
                    $v["tmp_product_price"] = $v["product_price_discount"];
                }else{
                    //$subtotal_amount += $v["quantity"] * $v["product_price"];
                    $tmp_total_amount = $v["quantity"] * $v["product_price"];
                    $v["tmp_product_price"] = $v["product_price"];
                }
                
                $v["tmp_total_amount"] = $tmp_total_amount;
                
                $cart[$k] = $v;
            }
            return $cart;
        }
		
		function getCartsPagination(){
			return $this->pagination;
		}
        
        public function saveTrackingNumber($params, $cart_id) {
            global $db;
            global $carts_table;
            
            $fields = array(
                "delivery_tracking_number" => $params->getString("delivery_tracking_number"),
                "delivery_tracking_info" => $params->getString("delivery_tracking_info")
            );

            $res = $db->autoExecute($carts_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $cart_id); safeCheck($res);
        }
        
        public static function getOrderStatus($id = 0) {
            global $db;
            global $lng;
            global $order_statuses_table;
            
            $row = $db->getRow("SELECT
                                    *,
                                    name_{$lng} AS name,
                                    subject_{$lng} AS subject,
                                    email_text_{$lng} AS email_text
                                FROM 
                                    ".$order_statuses_table."
                                WHERE 
                                    id = '".$id."'
                                AND edate = 0"); safeCheck($row);
            return $row;
        }
        
        public function sendOrderTemplate($order_status_id, $cart_id){
            global $db;
            global $sm;
            global $host;
            global $emails_test;
            global $carts_table;
            global $carts_user_table;
            
            $cart = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$cart_id."'"); safeCheck($cart);
            $cart_user = $db->getRow("SELECT * FROM ".$carts_user_table." WHERE cart_id = '".$cart_id."'"); safeCheck($cart_user);
            $row = self::getOrderStatus($order_status_id);
            
            $order_template = $this->createOrderTemplate($cart_id);
            
            $order_number = str_pad($cart["id"], 4, "0", STR_PAD_LEFT);
            
            $ratingLink = "";
            if (strpos($row["email_text"], '[rating_link]') !== false) {
                if($cart["rating_hash"] && $cart["rating_hash"] != 0){
                    $ratingHash = $cart["rating_hash"];
                }else{
                    $ratingHash = $this->generateRatingHashes((int)$cart_id, (int)$cart["user_id"]); //"https://www.os.nastop-bg.com/rating/";
                }
                
                $ratingLink = $host."order-ratingbg"."/".$cart_id."/".$ratingHash;
                
            }
            
            $tags = array("[full-order]", "[order-number]", "[delivery_tracking_number]", "[delivery_name]", "[delivery_last_name]", "[billing_name]", "[billing_last_name]", "[rating_link]");
            $tags_replace = array($order_template, $order_number, $cart["delivery_tracking_number"], $cart_user["delivery_name"], $cart_user["delivery_last_name"], $cart_user["billing_first_name"], $cart_user["billing_last_name"], $ratingLink);
            
            $subject = str_replace($tags, $tags_replace, $row["subject"]);
            $message = str_replace($tags, $tags_replace, $row["email_text"]);
            
            mailSender($cart_user["email"], $subject, $message);
            foreach($emails_test as $v){
                mailSender($v, $subject, $message);
            }
        }
        
        public function generateRatingHashes(int $cart_id, int $user_id = 0) {
            global $db;
            global $carts_table;
            global $carts_products_table;
            global $products_rating_links_table;
            
            $products = $db->getAll("SELECT
                                        *
                                    FROM
                                        {$carts_products_table} AS cp
                                    WHERE
                                        cp.cart_id = {$cart_id}
                                    AND cp.edate = 0
                                    ");
            if($products){
                $ratingHash = md5($cart_id.time().rand(1000,9999));
                $resRatingHash = $db->autoExecute($carts_table, array("rating_hash" => $ratingHash), DB_AUTOQUERY_UPDATE, "id = {$cart_id}"); safeCheck($resRatingHash);
            }else{
                return false;
            }
            foreach ($products as $v) {
                $fields = array(
                    "product_id" => $v["product_id"],
                    "cart_id" => $cart_id,
                    "user_id" => $user_id,
                    "hash" => md5($v["product_id"].time().rand(1000,9999)),
                    "rated" => 0,
                    "active" => 1,
                    "postdate" => time()
                );
                $check = $db->getRow("SELECT * FROM {$products_rating_links_table} WHERE cart_id = {$cart_id} AND product_id = {$v["product_id"]} AND edate = 0"); safeCheck($check);
                if(!$check){
                    $res = $db->autoExecute($products_rating_links_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
                }
//                else{
//                    $res = $db->autoExecute($products_rating_links_table, array("updated" => time()), DB_AUTOQUERY_UPDATE, "id = {$check["id"]}"); safeCheck($res);
//                }
            }
            return $resRatingHash ? $ratingHash : "";
        }
        
        public function createOrderTemplate( $cart_id ){
            global $db;
            global $carts_user_table;
            global $useBonusPoints;
            
            $tmp_cart = $this->getCart($cart_id);
            $sql_cart_use = " cart_id = '".$tmp_cart["id"]."' ";
            
            $user_info = $db->getRow("SELECT * 
                                    FROM ".$carts_user_table." AS users
                                    WHERE ".$sql_cart_use." ORDER BY id DESC"); safeCheck($user_info);
            $voucher_discount = $tmp_cart["voucher_discount"];
            
            $delivery_office = "";
            if ( $user_info["delivery_type"] == 2 ){
                $offices_tmp = Delivery::getOfficesByRegionAndCityNames((int)$user_info["delivery_type"], $tmp_cart["delivery_region"], $tmp_cart["delivery_city"]);
                foreach($offices_tmp as $k => $v){
                    if ( trim($v["id"]) == trim($tmp_cart["delivery_office_id"]) ){
                        $delivery_office = $v["name"];
                    }
                }
            }
            
            if ( $tmp_cart["discount_free_delivery"] ){
                if($useBonusPoints){
                    $total_amount_sagepay = (double)$tmp_cart["total_amount"] - (double)$tmp_cart["bonus_points_amount"] - (double)$tmp_cart["discount_amount_delivery"];
                }else{
                    $total_amount_sagepay = (double)$tmp_cart["total_amount"] - (double)$tmp_cart["discount_amount_delivery"];
                }
            }else{
                if($useBonusPoints){
                    $total_amount_sagepay = (double)$tmp_cart["total_amount"] + (double)$tmp_cart["delivery_amount"] - (double)$tmp_cart["bonus_points_amount"] - (double)$tmp_cart["discount_amount_delivery"];
                }else{
                    $total_amount_sagepay = (double)$tmp_cart["total_amount"] + (double)$tmp_cart["delivery_amount"] - (double)$tmp_cart["discount_amount_delivery"];
                }
            }
            
            //$total_amount = 0;
            //$total_items = sizeof($cart);
            
            $message = "";
            $message = '
            <h3>Номер на поръчка #'.$tmp_cart["id"].'</h3>
            
            <table width="100%" cellpadding="5" cellspacing="0"><tr><td width="50%">
                <h3>Информация за доставка</h3>
                '.$user_info["delivery_name"].' '.$user_info["delivery_last_name"].'<br />
                '.$user_info["delivery_address_1"].'<br />
                '.$user_info["delivery_address_2"].'<br />
                '.$user_info["delivery_city"].'<br />
                '.$user_info["delivery_postcode"].'<br />
                '.$user_info["delivery_country_name"].'<br />
                </td>
                <td>
                <h3>Информация за плащане</h3>
                '.$user_info["billing_first_name"].' '.$user_info["billing_last_name"].'<br />
                '.$user_info["billing_address_1"].'<br />
                '.$user_info["billing_address_2"].'<br />
                '.$user_info["billing_phone"].'<br />
                '.$user_info["billing_email"].'<br />
                '.$user_info["billing_city"].'<br />
                '.$user_info["billing_postcode"].'<br />
                '.$user_info["billing_country_name"].'<br />
                </td>
                </tr>
                </table>
                <br />
                ';
                $message .= '<table border="1" width="100%"  cellpadding="5" cellspacing="0">';
                $message .= '<thead>
                    <th>
                        Продукт
                    </th>
                    <th>
                        Единична цена
                    </th>
                    <th>
                        Количество
                    </th>
                    <th align="right">
                        Общо
                    </th>
                </thead>';
                foreach($tmp_cart["products"] as $k=>$v){
                    if ( $v["product_price_discount"] > 0.0 ){
                        $product_price_old = (float)$v["product_price"];
                        $product_price = (float)$v["product_price_discount"];
                    }else{
                        $product_price_old = 0.0;
                        $product_price = (float)$v["product_price"];
                    }
                    $message .='<tr>
                                    <td width="50%">
                                            <strong>'.$v["product"]["name"].'</strong><br />
                                            ';

                                            $message .= ' <strong>'.$v["option"]["name"].'</strong><br />';
                                        if($product_price_old > 0.0){
                                            $message.= '</td><td width="30%" valign="top" align="right">('. number_format($product_price_old, 2).' лв.) '.number_format($product_price, 2).' лв. </td>';
                                        }else{
                                            $message.= '</td><td width="30%" valign="top" align="right">'.number_format($product_price, 2).' лв. </td>';
                                        }
                                        $message .= '<td width="5%" valign="top" align="center">'.$v["quantity"].'</td>
                                        <td width="15%" valign="top" align="right">
                                            '.number_format($product_price*((int)$v["quantity"]), 2).' лв.
                                    </td>
                                </tr>';
                }

                if ( $useBonusPoints && $tmp_cart["bonus_points_amount"] > 0.0 ){
                $message .= '<tr>
                    <td>
                        Изполват се бонус <strong>'.(int)$tmp_cart["bonus_points_number"].'</strong> точки 
                    </td>
                    <td>		
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td align="right">
                        -'.$tmp_cart["bonus_points_amount"].' лв.
                    </td>
                </tr>';
                }
                if ( $tmp_cart["discount_amount"] > 0.0 ){
                $message .= '<tr>
                    <td>
                        Обща стойност на ползваните отстъпки върху поръчаните продукти:
                    </td>
                    <td align="right">
                        '.$tmp_cart["discount_amount"].' лв.
                    </td>
                    <td colspan="2">&nbsp;</td>
                </tr>';
                }
                if ( $tmp_cart["discount_free_delivery"] == 1 ){
                    if ( $tmp_cart["discount_amount_delivery"] > 0.0 ){
                        $message .= '<tr>
                        <td>
                            Отстъпка 2% при поръчка над 100 лева
                        </td>
                        <td>		
                            &nbsp;
                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td align="right">
                            -'.$tmp_cart["discount_amount_delivery"].' лв.
                        </td>
                    </tr>';
                    }
                    $message .= '<tr>
                        <td>
                            Безплатна доставка
                        </td>
                        <td>		
                            &nbsp;
                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td align="right">
                            0.00 лв.
                        </td>
                    </tr>';
                }else{
                    if ( $tmp_cart["discount_amount_delivery"] > 0.0 ){
                        $message .= '<tr>
                        <td>
                            Отстъпка 2% при поръчка над 100 лева
                        </td>
                        <td>		
                            &nbsp;
                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td align="right">
                            -'.$tmp_cart["discount_amount_delivery"].' лв.
                        </td>
                    </tr>';
                    }
                    if ( $tmp_cart["delivery_type_id"] == 2 ){
                    $message .= '<tr>
                        <td>
                            Доставка до офис на Спиди: '.$delivery_office.'
                        </td>
                        <td>		
                            &nbsp;
                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td align="right">
                            '.$tmp_cart["delivery_amount"].' лв.
                        </td>
                    </tr>';
                    }else{
                        $message .= '<tr>
                        <td>
                            Доставка до адрес
                        </td>
                        <td>		
                            &nbsp;
                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td align="right">
                            '.$tmp_cart["delivery_amount"].' лв.
                        </td>
                    </tr>';
                    }
                }
                if ($tmp_cart["voucher_number"]){ 
                $message .= '
                <tr>
                    <td>
                        Voucher Number: "'.$tmp_cart["voucher_number"].'"
                    </td>
                    <td>		
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td align="right">
                        - '.$voucher_discount.' лв.
                    </td>
                </tr>';
                }
                $message .='
                <tr>
                    <td>
                        Общо
                    </td>
                    <td>		
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td align="right">
                        '.number_format($total_amount_sagepay,2).' лв.
                    </td>
                </tr>
                </table>';
            return $message;
        }
        
        public function generateBOL($row) {
            global $db;
            global $lng;
            global $host;
            global $carts_table;
            global $carts_products_table;
            global $products_table;
            global $variants_table;
            global $useBonusPoints;
            global $error_log_delivery_table;
            
            $bonusPointsAmount = $useBonusPoints ? $row["bonus_points_amount"] : 0.0;
            if ( $row["discount_free_delivery"] ){
                $total_amount = $row["subtotal_amount"] - $bonusPointsAmount - $row["discount_amount_delivery"] ;
            }else{
                $total_amount = $row["total_amount"] - $bonusPointsAmount;
            }

            if($row["discount_free_delivery"] == 1){
                $free_delivery = 1;
            }

            if ( trim($_REQUEST["delivery_tracking_info"]) ){
                $fields["delivery_tracking_info"] = $_REQUEST["delivery_tracking_info"];
            }

            $tmp_row = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$row["id"]."'"); safeCheck($tmp_row);

            $cart_tmp2 = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE edate = 0 AND cart_id = '".$row["id"]."'"); safeCheck($cart_tmp2);
            $cart_weight = 0;
            foreach($cart_tmp2 as $k => $v){
                $product = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$products_table." WHERE edate = 0 AND id = '".$v["product_id"]."'"); safeCheck($product);
                if ($v["variant_id"]){
                    $option_id = $v["variant_id"];
                    $variant = $db->getRow("SELECT * FROM ".$variants_table." WHERE id = '".$option_id."'"); safeCheck($variant);
                    $cart_weight += $v["quantity"] * $variant["weight"];
                }else{
                    $cart_weight += $v["quantity"] * $product["weight"];
                }
                $cart_tmp2[$k] = $v;
            }
            
            $total_amount_package = $total_amount;
            if ( !$free_delivery ){
                if ( $row["payment_type_id"] != 2 ){
                    $total_amount_package = $total_amount_package + $row["delivery_amount"];
                }
            }
            if($row["payment_type_id"] == 2){
                $total_amount_package = 0;
            }

            $deliveryCompanyName = "";
            if ( $row["delivery_company_name"] ){
                $receiverCompanyName = $row["delivery_company_name"];
            }
            $receiverName = $row["delivery_first_name"]." ".$row["delivery_last_name"];
            
            $districtsObj = new Districts();
            $citiesObj = new Cities();
            $districtName = $districtsObj->getRecord($row["delivery_region_id"])["name"];
            $citiesName = $citiesObj->getRecord($row["delivery_city_id"])["name"];
            
            try {
                $courierReceiverSiteId = Delivery::getCourierSiteId((int)$row["delivery_type_id"], $districtName, $citiesName);
                $delivery_tracking_office = (int)$_REQUEST["delivery_tracking_office"];
                
                //$senderBringToOffice = false;
                $senderBringToOffice = true;
                if($delivery_tracking_office > 0){
                    $senderBringToOffice = true;
                }
                $deferredDeliveryWorkDays = (int)$_REQUEST["deferred_delivery_work_days"] > 0 ? (int)$_REQUEST["deferred_delivery_work_days"] : 0;
                $optionsBeforePayment = isset($_REQUEST["options_before_payment"]) && (int)$_REQUEST["options_before_payment"] > 0 ? true : false;
                
                if ( $row["delivery_office_id"] && ($row["delivery_type_id"] == 2 || $row["delivery_type_id"] == 4) ){
                    $resultBOL = Delivery::createBillOfLading((int)$row["delivery_type_id"], 0, $total_amount_package, $fields["delivery_tracking_info"], $row["weight"], null, null, $receiverName, $receiverCompanyName, $row["billing_phone"], $row["delivery_office_id"], $deferredDeliveryWorkDays, $optionsBeforePayment, $delivery_tracking_office, $senderBringToOffice);
                }elseif ( $row["delivery_type_id"] == 1 || $row["delivery_type_id"] == 3){
                    $resultBOL = Delivery::createBillOfLading((int)$row["delivery_type_id"], 0, $total_amount_package, $fields["delivery_tracking_info"], $row["weight"], $courierReceiverSiteId, $row["delivery_address_1"], $receiverName, $receiverCompanyName, $row["billing_phone"], null, $deferredDeliveryWorkDays, $optionsBeforePayment, $delivery_tracking_office, $senderBringToOffice);
                }
                
                if(!isset($resultBOL["errorMessage"])){
                    if ( $row["delivery_type_id"] == 1 || $row["delivery_type_id"] == 2 ){
                        $delivery_tracking_number = $resultBOL->getGeneratedParcels()[0]->getParcelId();
                        $fileName = Delivery::printBillOfLadingPDF((int)$row["delivery_type_id"], $delivery_tracking_number);
                    }else if( $row["delivery_type_id"] == 3 || $row["delivery_type_id"] == 4 ){
                        $delivery_tracking_number = $resultBOL["label"]["shipmentNumber"];
                        $pdfURL = $resultBOL["label"]["pdfURL"];
                        $fileName = Delivery::printBillOfLadingPDF((int)$row["delivery_type_id"], $delivery_tracking_number, $pdfURL);
                    }
                    
                    $delivery_tracking_pdf_url = "";
                    if($fileName){
                        if ( $row["delivery_type_id"] == 1 || $row["delivery_type_id"] == 2 ){
                            $delivery_tracking_pdf_url = $host."admin/showSpeedyBolPdf.php?file_name=$fileName";
                        }else if( $row["delivery_type_id"] == 3 || $row["delivery_type_id"] == 4 ){
                            $delivery_tracking_pdf_url = $host."admin/showEcontBolPdf.php?file_name=$fileName";
                        }
                    }

                    $fields["deferred_delivery_work_days"] = $deferredDeliveryWorkDays;
                    $fields["options_before_payment"] = $optionsBeforePayment;

                    $fields["delivery_tracking_number"] = $delivery_tracking_number;
                    $fields["delivery_tracking_pdf"] = $delivery_tracking_pdf_url;

                    //$fields["delivery_tracking_number_test"] = $delivery_tracking_number;
                    //$fields["delivery_tracking_pdf_test"] = $delivery_tracking_pdf_url;
                    $fields["delivery_tracking_office"] = $delivery_tracking_office;

                    $res = $db->autoExecute($carts_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$row["id"]."' "); safeCheck($res);
                }
            }catch (Exception $e) {
                $file = __FILE__; $line = __LINE__;
                $speedyErrorCode = $e->getCode();
                $speedyError = $e->getMessage();
                $res = $db->autoExecute($error_log_delivery_table, array("carts_id" => $row["id"], "file" => $file, "line" => $line, "message" => $speedyError, "server_info" => serialize($_SERVER), "error_datetime" => date("Y-m-d H:i")), DB_AUTOQUERY_INSERT); safeCheck($res);
                if( strpos( $speedyError, "OPTIONS_BEFORE_PAYMENT_NOT_ALLOWED_FOR_APT" ) ) {
                    die("Опциите преди плащане не са позволени при вземане от Автомат. Моля премахнете Преглед преди плащане или сменете офиса.");
                }
                //header("Location: /message.php?code=403");
                die("Възникна проблем. Проверете валидността на данните в полетата и опитайте отново!4<br>Message: ".$speedyError."<br>Code: ".$speedyErrorCode);
            }
            
            return $resultBOL;
        }
        
        public function generateBOLDelete($row) {
            global $db;
            global $carts_table;
            global $error_log_delivery_table;
            
            try {
                $result = Delivery::invalidateShipping((int)$row["delivery_type_id"], $row["delivery_tracking_number"]);
            }catch (Exception $e) {
                $file = __FILE__; $line = __LINE__;
                $speedyError = $e->getMessage();
                $res = $db->autoExecute($error_log_delivery_table, array("carts_id" => $row["id"], "file" => $file, "line" => $line, "message" => $speedyError, "server_info" => serialize($_SERVER), "error_datetime" => date("Y-m-d H:i")), DB_AUTOQUERY_INSERT); safeCheck($res);
                //header("Location: /message.php?code=403");
                die("Възникна проблем. Проверете валидността на данните в полетата и опитайте отново!3");
            }

            $res = $db->autoExecute($carts_table, array("delivery_tracking_number" => "", "delivery_tracking_pdf" => "", "delivery_tracking_info" => "", "delivery_tracking_office" => ""), DB_AUTOQUERY_UPDATE, " id = '".$row["id"]."' "); safeCheck($res);
            
        }
        
        public function getCart($cart_id_preset = 0, $user_id = 0){
			global $db;
			global $lng;
			global $carts_products_table;
			global $products_table;
			global $products_images_table;
			global $options_table;
			global $option_groups_table;
			
			if ( $cart_id_preset ){
				$cart_id = $cart_id_preset;
			}else{
				$cart_id = (int)$_SESSION["cart_id"];
			}
            if($cart_id > 0){
                $cart = $this->getRecord($cart_id);
                //if user_id is given we check if the given user is the same as the one in the carts record 
                if($user_id > 0 && $user_id != $cart["user_id"]){
                   return array();
                }
                $cartProducts = $db->getAll("SELECT
								cp.*,
								(SELECT images.pic FROM ".$products_images_table." AS images WHERE images.id = cp.product_image_id) AS pic
							FROM 
								".$carts_products_table." AS cp
							WHERE 
								cp.edate = 0 
							AND cp.cart_id = '".$cart["id"]."'"); safeCheck($cartProducts);
                
                //$subtotal_amount = 0.0;
                //$total_amount = 0.0;
                $cart_items = 0;
                $total_weight = 0.0;
                foreach( $cartProducts as $k => $v ){
                    $sql = "SELECT
                                o.*,
                                o.option_text AS name,
                                (SELECT name_{$lng} FROM {$option_groups_table} AS og WHERE og.id = o.option_group_id AND og.edate = 0 LIMIT 1) AS option_group_name
                            FROM
                                ".$options_table." AS o
                            WHERE
                                o.id = '".$v["option_id"]."'";
                    $option = $db->getRow($sql); safeCheck($option);
                    $v["option"] = $option;
                    
                    $product = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$products_table." WHERE edate = 0 AND id = ".$v["product_id"]); safeCheck($product);
                    $v["product"] = $product;
                    
                    //$subtotal_amount += (float)$v["product_price"]*(int)$v["quantity"];
                    //$total_amount += $v["product_price_total"];
                    $cart_items += $v["quantity"];
                    $total_weight += $v["product_weight_total"];
                    $cartProducts[$k] = $v;
                }
                
                //$cart["subtotal_amount"] = $subtotal_amount;
                //$cart["total_amount"] = $total_amount;
                $cart["total_price_with_delivery"] = $total_amount + $cart["delivery_amount"];
                $cart["cart_items"] = $cart_items;
                $cart["total_weight"] = $total_weight;
                $cart["products"] = $cartProducts;
                //$cart["delivery_amount_show"] = calculateDelivery($cart["id"]);
                return $cart;
            }
			return array();
		}
		
	}
	
?>