<?php
	class Carts extends Settings{
		
		public $pagination = "";
		
        function getRecordSimple($id = 0){
            global $db;
			global $carts_table;
            
            $row = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$id."'"); safeCheck($row);
            
            return $row;
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
                "finalised" => $params->getInt("finalised"),
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
            global $carts_products_table;
            global $users_table;
            global $econt_user;
            global $econt_pass;
            
            $cart = $this->getRecord($id);
            $cart_products = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE cart_id = '".$id."' AND edate = 0"); safeCheck($cart_products);
            $total_amount = 0;
            $total_weight = 0;
            foreach($cart_products as $k => $v){
                //$tmp_total_amount = 0;
                //$tmp_total_weight = 0;
                if ( $v["product_price_discount"] > 0.0 ){
                    $tmp_total_amount = $v["quantity"] * $v["product_price_discount"];
                }else{
                    $tmp_total_amount = $v["quantity"] * $v["product_price"];
                }
                $tmp_total_weight = $v["quantity"] * $v["product_weight"];


                $total_amount += $tmp_total_amount;
                $total_weight += $tmp_total_weight;
                //$cart_products[$k] = $v;
            }
            $cart["weight"] = $total_weight;

            $user = $db->getRow("SELECT * FROM ".$users_table." WHERE id = '".$user_id."'"); safeCheck($user);
            if ( $user["bonus_points"] || $user["bonus_points_tmp"] ){
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
            
            $res = $db->autoExecute($carts_table, array("total_amount" => $total_amount, "subtotal_amount" => $total_amount, "bonus_points_amount" => $bonus_points_amount, "bonus_points_number" => $bonus_points_number, "bonus_points_won_amount" => $bonus_points_won_amount, "bonus_points_won_number" => $bonus_points_won_number, "weight" => $total_weight), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
            
            if ( $user_id && $user["bonus_points"] > 0 ){
                $res = $db->autoExecute($users_table, array("bonus_points_tmp" => $user["bonus_points"], "bonus_points" => 0), DB_AUTOQUERY_UPDATE, "id = '".$user_id."'"); safeCheck($res);
            }
            
            $discounts = $this->getDiscounts($id);
            
            $fields_discount = array(
                                        "discount_amount" => round($discounts["discount_amount"],2),
                                        "discount_free_delivery" => 0 //$discounts["discount_free_delivery"]
                                    );
            
            $isFreeDelivery = CartDiscounts::isFreeDelivery($total_amount, $cart["weight"]);
            if($isFreeDelivery){
                $fields_discount["discount_free_delivery"] = 1;
                $fields_discount["discount_amount_delivery"] = 0; // Remove 20% discount if previously calculated;
            }else{
                $delivery_amount = CartDiscounts::getCustomDeliveryAmount($total_amount, $cart["weight"]);
            }
            
            $res = $db->autoExecute($carts_table, $fields_discount, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
            
            $cart["discount_amount"] = number_format($fields_discount["discount_amount"],2);
            $cart["discount_free_delivery"] = $fields_discount["discount_free_delivery"];
            
            // if delivery type is ECONT: get delivery and cod amount
            
            if($cart["delivery_type_id"] == 1 || $cart["delivery_type_id"] == 2){
                $receiver["clientCompany"] = $cart["delivery_first_name"]." ".$cart["delivery_last_name"]; //фирма получател;
                $receiver["clientName"] = $cart["delivery_first_name"]." ".$cart["delivery_last_name"]; //име на получателя;
                $receiver["clientMail"] = $cart["email"]; // email на получателя
                $receiver["deliveryCityBG"] = $cart["delivery_city"]; //фирма получател;
                $receiver["deliveryCityPostCode"] = $cart["delivery_postcode"]; //фирма получател;
                $receiver["deliveryOfficeSelected"] = ""; //фирма получател;
                $receiver["deliveryQuarter"] = ""; //квартал;
                $receiver["deliveryStreet"] = $cart["delivery_address_1"]; //улица;
                $receiver["deliveryStreetNum"] = ""; //номер на улицата;
                $receiver["deliveryBlock"] = ""; //блок;
                $receiver["deliveryEntrance"] = "";  //вход;
                $receiver["deliveryFloor"] = ""; //етаж;
                $receiver["deliveryApartment"] = ""; //апартамент;
                $receiver["streetOther"] = ""; //друга информация;
                $receiver["clientPhone"] = $cart["billing_phone"]; // телефон на получателя;

                $econtDelivery = new econt($econt_user, $econt_pass);
                if ( $cart["delivery_office_id"] ){
                    $office_tmp = $econtDelivery->getOfficeById($cart["delivery_office_id"]);
                    $receiver["deliveryOfficeSelected"] = (string)$office_tmp["office_code"]; //фирма получател;
                    $receiver["deliveryQuarter"] = ""; //квартал;

                    $receiver["deliveryCityPostCode"] = (string)$office_tmp["post_code"]; //фирма получател;
                    $receiver["deliveryQuarter"] = (string)$office_tmp["address_details"]->quarter_name; //квартал;
                    $receiver["deliveryStreet"] = (string)$office_tmp["address_details"]->street_name; //улица;
                    $receiver["deliveryStreetNum"] = (string)$office_tmp["address_details"]->num; //номер на улицата;
                    $receiver["deliveryBlock"] = (string)$office_tmp["address_details"]->bl; //блок;
                    $receiver["deliveryEntrance"] = (string)$office_tmp["address_details"]->vh;  //вход;
                    $receiver["deliveryFloor"] = (string)$office_tmp["address_details"]->et; //етаж;
                    $receiver["deliveryApartment"] = (string)$office_tmp["address_details"]->ap; //апартамент;
                    $receiver["streetOther"] = (string)$office_tmp["address_details"]->other; //друга информация;
                }

                if ( $cart["delivery_type_id"] == 1 ){
                    $deliveryTypeText = "DOOR";
                }
                if ( $cart["delivery_type_id"] == 2 ){
                    $deliveryTypeText = "OFFICE";
                }

                $xml_delivery = $econtDelivery->createShippingRequest($id, $receiver, $deliveryTypeText, $cart["total_amount"], $cart["weight"], 1, "", $fields_discount["discount_free_delivery"], $cart["payment_type_id"]);

                $xml_delivery_content = $econtDelivery->readXML($xml_delivery);

                $xml_delivery_prices = $xml_delivery_content->result->e->loading_price;

                $cod_amount = $xml_delivery_prices->CD;
                $delivery_amount = $xml_delivery_prices->total;
            }
            $fields_delivery["cod_amount"] = isset($cod_amount) ? (double)$cod_amount : NULL;
            $fields_delivery["delivery_amount"] = (double)$delivery_amount;
            //$fields["delivery_amount"] = (double)$delivery_amount;

            //$delivery_amount = $fields["delivery_amount"];
            $res = $db->autoExecute($carts_table, $fields_delivery, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);

            if ( $cart["discount_free_delivery"] ){
                // $total_amount_corrected = number_format($total_amount - $bonus_points_amount - $cart["discount_amount"] ,2);
                $total_amount_corrected = number_format($total_amount - $bonus_points_amount ,2);
            }else{
                // $total_amount_corrected = number_format($total_amount + $delivery_amount - $bonus_points_amount - $cart["discount_amount"] ,2);
                $total_amount_corrected = number_format($total_amount + $delivery_amount - $bonus_points_amount  ,2);
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
		function getCarts($page = 0, $limit = 50, $params){
			global $db;
			global $lng;
			global $carts_table;
            global $carts_user_table;
            global $carts_products_table;


            if ( $params->has("Filter") ){
                $id = $params->getInt("id");
                $econt_tracking_number = $params->getString("econt_tracking_number");
                $name = $params->getString("name");
                $email = $params->getEmail("email");
                if ($name){
                    $sql_name = " AND ( LOWER(delivery_first_name) LIKE '%".strtolower($name)."%' OR LOWER(delivery_last_name)  LIKE '%".strtolower($name)."%' OR LOWER(billing_first_name) LIKE '%".strtolower($name)."%' OR LOWER(billing_last_name)  LIKE '%".strtolower($name)."%'  )"; 
                    $sm->assign("name", $name);
                }
                if ($email){
                    $sql_email = " AND ( LOWER(email) LIKE '%".strtolower($email)."%')"; 
                    $sm->assign("email", $email);
                }
                if ($id){
                    $sql_id = " AND id = '".$id."'"; 
                    $sm->assign("id", $id);
                }
                if ($econt_tracking_number){
                    $sql_econt_tracking_number = " AND delivery_tracking_number = '".$econt_tracking_number."'"; 
                    $sm->assign("econt_tracking_number", $econt_tracking_number);
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
			$pages = $db->getRow("SELECT count(id) AS cntr FROM " . $carts_table . " WHERE edate = 0 {$sql_order_status} {$sql_name} {$sql_email}"); safeCheck($pages);
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
			
			$items = $db->getAll("SELECT * 
                                /*,
                                (SELECT os.name_{$lng} FROM ".$order_statuses_table." AS os WHERE os.edate =0 AND os.id = carts.status) AS order_status_name,
                                (SELECT os.color FROM ".$order_statuses_table." AS os WHERE os.edate =0 AND os.id = carts.status) AS order_status_color,
                                (SELECT pharmacies.name_{$lng} FROM ".$pharmacies_table." AS pharmacies WHERE pharmacies.edate =0 AND pharmacies.id = carts.pharmacy_id) AS pharmacy_name
                                */
                                FROM " . $carts_table . " AS carts
                                WHERE edate = 0
                                {$sql_order_status}
                                {$sql_name}
                                {$sql_email}
                                {$sql_econt_tracking_number}
                                {$sql_id}
                                ORDER BY postdate DESC
                                LIMIT {$start}, {$limit}"); safeCheck($items);
			foreach($items as $k => $v){
//                if ($v["postdate"] < 1351704368){
//                    $sql_cart_use = " session_id = '".$v["session_id"]."' ";
//                }else{
                    $sql_cart_use = " cart_id = '".$v["id"]."' ";
//                }
                $amounts = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE cart_id = '".$v["id"]."' AND edate = 0");
                $product_amount = 0;
                foreach($amounts as $kk => $vv){
                    if ( $vv["product_price_discount"] > 0.0 ){
                        $product_price = $vv["product_price_discount"];
                    }else{
                        $product_price = $vv["product_price"];
                    }
                    $product_amount += $product_price*$vv["quantity"];
                }
                $v["product_price_total"] = $product_amount;
                $v["product_price_total"] = $v["product_price_total"] - $v["bonus_points_amount"];

                if ( $v["discount_free_delivery"] ){
                    // $v["total_amount"] = $v["total_amount"]-$v["bonus_points_amount"]- $v["discount_amount"];

                    $v["total_amount"] = $v["total_amount"]-$v["bonus_points_amount"] - $v["discount_amount_delivery"];
                    $v["product_price_total"] = $v["product_price_total"] - $v["discount_amount_delivery"];
                }else{
                    // $v["total_amount"] = $v["total_amount"]+$v["delivery_amount"]-$v["bonus_points_amount"]- $v["discount_amount"];
                    $v["total_amount"] = $v["total_amount"]+$v["delivery_amount"]-$v["bonus_points_amount"];
                }

//                if ( $v["id"] <=  10787){
//                    $v["bonus_points_amount"] = (-1)*$v["bonus_points_amount"];
//                    $v["cod_amount"] = (-1)*$v["cod_amount"];
//                    $v["total_amount"] = $v["subtotal_amount"] - $v["bonus_points_amount"] ;
//                    $v["product_price_total"] = $v["subtotal_amount"] - $v["bonus_points_amount"];
//                }

                $user_info = $db->getRow("SELECT * 
                                            FROM ".$carts_user_table." AS users
                                            WHERE ".$sql_cart_use."
                                            "); safeCheck($user_info);
                $v["user_info"] = $user_info;
                $v["order_number"] = date("Y", $v["postdate"]).str_pad($v["id"], 4, "0", STR_PAD_LEFT);
                $items[$k] = $v;
            }
            
			return $items;
		}
        
        public function getCartProducts($cart_id) {
            global $db;
            global $lng;
            global $products_table;
            global $carts_products_table;
            global $options_table;
            global $option_groups_table;
            
            $cart = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE edate = 0 AND cart_id = '".$cart_id."'"); safeCheck($cart);
            $total_weight = 0;
            $total_postage_points = 0;
            foreach($cart as $k => $v){
                $product = $db->getRow("SELECT id, name_{$lng} AS name FROM ".$products_table." AS products WHERE edate = 0 AND id = '".$v["product_id"]."'"); safeCheck($product);
                $v["product"] = $product;
                $total_weight += $product["product_weight"]*$v["quantity"];
                $total_postage_points += $product["postage_points"]*$v["quantity"];
                $v["cart_price"] = number_format(($v["product_price"]+$v["choices_price"])*$v["quantity"],2);
                $vat_norounding = $v["cart_price"]*5/6;
                $v["cart_price_clear"] = number_format((($v["cart_price"]*5)/6), 2);
                $v["cart_price_w_vat"] = number_format($vat_norounding+($vat_norounding*$vat["vat_percent"])/100, 2);
                $v["cart_price_w_vat_total"] = number_format($v["cart_price_w_vat"]*$v["quantity"],2);
                
                $option = $db->getRow("SELECT *, option_text AS name, (SELECT og.name_{$lng} FROM ".$option_groups_table." AS og WHERE og.id = options.option_group_id) AS option_group_name FROM ".$options_table." AS options	WHERE options.id = '".$v["variant_id"]."'"); safeCheck($option);
                $v["option"] = $option;
                
                $tmp_total_amount = 0;
                if ( $v["product_price_discount"] > 0.0 ){
                    $subtotal_amount += $v["quantity"] * $v["product_price_discount"];
                    $tmp_total_amount = $v["quantity"] * $v["product_price_discount"];
                    $v["tmp_product_price"] = $v["product_price_discount"];
                }else{
                    $subtotal_amount += $v["quantity"] * $v["product_price"];
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
            global $emails_test;
            global $carts_table;
            global $carts_user_table;

            $cart = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$cart_id."'"); safeCheck($cart);
            $cart_user = $db->getRow("SELECT * FROM ".$carts_user_table." WHERE cart_id = '".$cart_id."'"); safeCheck($cart_user);
            $row = self::getOrderStatus($order_status_id);

            $order_template = $this->createOrderTemplate($cart_id);

            $order_number = str_pad($cart["id"], 4, "0", STR_PAD_LEFT);
            
            $tags = array("[full-order]", "[order-number]", "[delivery_tracking_number]", "[delivery_name]", "[delivery_last_name]", "[billing_name]", "[billing_last_name]");
            $tags_replace = array($order_template, $order_number, $cart["delivery_tracking_number"], $cart_user["delivery_name"], $cart_user["delivery_last_name"], $cart_user["billing_first_name"], $cart_user["billing_last_name"]);

            $subject = str_replace($tags, $tags_replace, $row["subject"]);
            $message = str_replace($tags, $tags_replace, $row["email_text"]);
            
            mailSender($cart_user["email"], $subject, $message);
            foreach($emails_test as $v){
                mailSender($v, $subject, $message);
            }
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
        
        public function generateEcont($id, $row) {
            global $econt_user;
            global $econt_pass;
            global $db;
            global $lng;
            global $carts_table;
            global $carts_products_table;
            global $products_table;
            global $variants_table;


            $econtDelivery = new econt($econt_user, $econt_pass);
		
            $receiver["deliveryCityBG"] = $row["delivery_city"]; //населеното място;
            $receiver["deliveryCityPostCode"] = $row["delivery_postcode"]; //пощенски код на населеното място
            if ( $row["delivery_office_id"] && $row['delivery_type'] == 2 ){
                $offices_tmp = $econtDelivery->getOffices();
                foreach($offices_tmp as $k => $v){
                    if ( trim($v["id"]) == $row["delivery_office_id"] ){
                        if ( trim($v["name"]) ){
                            $use_office = $v;
                        }
                    }
                }
                $econt_delivery_type = "OFFICE";
                $tmp_address = get_object_vars($use_office["address_details"]);
                $receiver["deliveryOfficeSelected"] = (string)$use_office["office_code"]; // код на офиса, който ще разнесе пратката. Не задължителен параметър, ако същия не бъде посочен, се взема офисът по подразбиране;
                $receiver["deliveryCityPostCode"] = (string)$use_office["post_code"]; //фирма получател;
                $receiver["deliveryQuarter"] = $tmp_address["quarter_name"] ; //квартал;
                $receiver["deliveryStreet"] = $tmp_address["street_name"]; //улица;
                $receiver["deliveryStreetNum"] = $tmp_address["num"]; //номер на улицата;
                $receiver["deliveryBlock"] = $tmp_address["bl"]; //блок;
                $receiver["deliveryEntrance"] = $tmp_address["vh"];  //вход;
                $receiver["deliveryFloor"] = $tmp_address["et"]; //етаж;
                $receiver["deliveryApartment"] = $tmp_address["ap"]; //апартамент;
                $receiver["streetOther"] = $tmp_address["other"]; //друга информация;

            }else{
                $econt_delivery_type = "DOOR";
                $receiver["deliveryOfficeSelected"] = ""; // код на офиса, който ще разнесе пратката. Не задължителен параметър, ако същия не бъде посочен, се взема офисът по подразбиране;
                $receiver["deliveryQuarter"] ; //квартал;
                $receiver["deliveryStreet"] = $row["delivery_address_1"]; //улица;
                $receiver["deliveryStreetNum"] = ""; //номер на улицата;
                $receiver["deliveryBlock"]; //блок;
                $receiver["deliveryEntrance"] = "";  //вход;
                $receiver["deliveryFloor"] = "" ; //етаж;
                $receiver["deliveryApartment"] = ""; //апартамент;
                $receiver["streetOther"]; //друга информация;
            }
            if ( $row["delivery_company_name"] ){
                $receiver["clientCompany"] = $row["delivery_company_name"]; //фирма получател;
            }else{
                $receiver["clientCompany"] = $row["delivery_first_name"]." ".$row["delivery_last_name"]; //фирма получател;
            }

            $receiver["clientName"] = $row["delivery_first_name"]." ".$row["delivery_last_name"]; //име на получателя;
            $receiver["clientMail"] = $row["email"]; // email на получателя

            $receiver["clientPhone"] = $row["billing_phone"]; // телефон на получателя;
            //$receiver["clientPhone"] = $row["billing_phone"];  //телефонен номер на подателя, при желание от негова страна да бъде уведомен с SMS за доставка на пратката му;

            //$total_amount = $row["subtotal_amount"] - $row["bonus_points_amount"];

            if ( $row["discount_free_delivery"] ){
                // $total_amount = $row["total_amount"]-$row["bonus_points_amount"] - $row["discount_amount"];
                $total_amount = $row["subtotal_amount"]-$row["bonus_points_amount"] - $row["discount_amount_delivery"] ;
            }else{
                // $total_amount = $row["total_amount"]+$row["delivery_amount"]-$row["bonus_points_amount"]- $row["discount_amount"];
                $total_amount = $row["total_amount"] - $row["bonus_points_amount"];
            }

//            if ( $row["id"] < 13510 ){
//                if ( $total_amount - $row["delivery_amount"] +$row["bonus_points_amount"] < 100 ){
//                    $total_amount = $total_amount - $row["delivery_amount"];
//                }else{
//                    $free_delivery = 1;
//                }
//            }else{
                if($row["discount_free_delivery"] == 1){
                    $free_delivery = 1;
                }
//            }

            if ( trim($_REQUEST["delivery_tracking_info"]) ){
                $fields["delivery_tracking_info"] = $_REQUEST["delivery_tracking_info"];
            }

            $tmp_row = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$id."'"); safeCheck($tmp_row);

            $cart_tmp2 = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE edate = 0 AND cart_id = '".$id."'"); safeCheck($cart_tmp2);
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

            if ( $free_delivery == 1 ){
                $fields_discount["discount_free_delivery"] = 1;
                $fields_discount["discount_amount_delivery"] = 0; // Remove 20% discount if previously calculated;
            }
            if( $total_amount >= 49 ){
                if ( $cart_weight < 1 ){
                    $fields_discount["discount_free_delivery"] = 1;
                    // $fields_discount["discount_amount_delivery"] = number_format($total_amount * 0.02,2); // Calculate 20% discount;
                    $fields_discount["discount_amount_delivery"] = 0; // Remove 20% discount if previously calculated;
                }
                if( $total_amount >= 100 ){
                    $fields_discount["discount_amount_delivery"] = number_format($total_amount * 0.02,2); // Calculate 20% discount;
                }
            }

            $total_amount_package = $total_amount;
            if ( !$fields_discount["discount_free_delivery"] ){
                if ( $row["payment_type_id"] != 2 ){
                    $total_amount_package = $total_amount_package + $row["delivery_amount"];
                }
            }

            $delivery_tracking_office = $_REQUEST["delivery_tracking_office"];
            
            $result_xml = $econtDelivery->createShippingRequest($id, $receiver, $econt_delivery_type, $total_amount_package, $row["weight"], 1, $fields["delivery_tracking_info"], $fields_discount["discount_free_delivery"], $delivery_tracking_office, $row["payment_type_id"]);
            $econtInfo = $econtDelivery->readXML($result_xml);

            $econtInfoUse = get_object_vars($econtInfo->result->e);

            if ( $econtInfoUse["error"] ){
                $fields["econt_error"] = $econtInfoUse["error"];
            }

//            $delivery_tracking_number = get_object_vars($econtInfo->result->e);
//            $delivery_tracking_pdf = get_object_vars($econtInfo->result->e->pdf_url);

            $fields["delivery_tracking_number"] = $econtInfoUse["loading_num"];
            $fields["delivery_tracking_pdf"] = $econtInfoUse["pdf_url"];

//            $fields["delivery_tracking_number_test"] = $econtInfoUse["loading_num"];
//            $fields["delivery_tracking_pdf_test"] = $econtInfoUse["pdf_url"];
            $fields["delivery_tracking_office"] = $delivery_tracking_office;

            $res = $db->autoExecute($carts_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
        }
        
        public function generateEcontDelete($cartId, $delivery_tracking_number) {
            global $db;
            global $carts_table;
            global $econt_user;
            global $econt_pass;

            $econtDelivery = new econt($econt_user, $econt_pass);

            $res = $db->autoExecute($carts_table, array("delivery_tracking_number" => "", "delivery_tracking_pdf" => "", "delivery_tracking_info" => ""), DB_AUTOQUERY_UPDATE, " id = '".$cartId."' "); safeCheck($res);
            $result = $econtDelivery->deleteShipping($delivery_tracking_number, $cartId, 1);
            
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
                
                $subtotal_amount = 0.0;
                $total_amount = 0.0;
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
                    
                    $subtotal_amount += (float)$v["product_price"]*(int)$v["quantity"];
                    $total_amount += $v["product_price_total"];
                    $cart_items += $v["quantity"];
                    $total_weight += $v["product_weight_total"];
                    $cartProducts[$k] = $v;
                }
                
                $cart["subtotal_amount"] = $subtotal_amount;
                $cart["total_amount"] = $total_amount;
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