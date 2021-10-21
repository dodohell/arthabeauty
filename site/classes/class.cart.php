<?php
	class Cart extends Settings{
        
        public function getRecord(int $id){
			global $db;
			global $carts_table;
			
			$row = $db->getRow("SELECT c.* FROM ".$carts_table." AS c WHERE c.edate = 0 AND c.id = ".$id); safeCheck($row);
			
			return $row;
		}
        
        public function getPaymentProcessingRecord(int $id){
			global $db;
			global $payment_processing_table;
			
			$row = $db->getRow("SELECT * FROM ".$payment_processing_table." WHERE edate = 0 AND id = ".$id); safeCheck($row);
			
			return $row;
		}
		
		function deletePic($id){
			global $db;
			global $products_table;
			
			$res = $db->autoExecute($products_table, array("pic" => ""), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			header("Location: /site/index.php?param=myshop-product&id=".$id);
			die();
		}
		
		public static function generateBreadcrumbs(){
			global $sm;
			global $host;
			global $language_file;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
            
            $breadcrumbs = '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>';
            $breadcrumbs .= '<span>'.$configVars["cart_breadcrumbs"].'</span>';
            
            return $breadcrumbs;
		}
		
		public static function getPageCart(FilteredMap $params){
			global $sm;
			global $db;
            global $lng;
            global $htaccess_file;
            global $products_table;
            global $products_images_table;
            global $carts_table;
            global $options_table;
            global $option_groups_table;
            global $carts_products_table;
            global $products_comments_table;
            
            if($params->has("updateCart")){
                self::updateQuantity($params);
            }
            
            $sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
            
            if ($params->has("gotoCheckout")){
                header("Location: /".$htaccessVars["htaccess_checkout"]."/");
                die();
            }
            
            if ($params->has("continueShopping")){
                header("Location: /");
                die();
            }
            
            // CART ADDON
            $cart_addon = $db->getAll("SELECT
                                            *,
                                            name_{$lng} AS name,
                                            excerpt_{$lng} AS excerpt,
                                            (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id ORDER BY pos LIMIT 1) AS pic,
                                            (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id) AS rating,
                                            (SELECT COUNT(rating2.id) FROM ".$products_comments_table." AS rating2 WHERE rating2.product_id = products.id AND rating2.edate = 0) AS reviews_count
                                        FROM
                                            ".$products_table." AS products
                                        WHERE
                                            edate = 0
                                        AND cart_addon = 1
                                        LIMIT 3"); safeCheck($cart_addon);
            $helpers = new Helpers();
            $user_group_id = Helpers::getCurentUserGroupId();
            
            foreach ($cart_addon as $k => $v) {
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
                    $v["bonus_points_win"] = round($v["price"] * 1, 0) ;
                }
                $v["product_price_clear"] = round($v["product_price"],2);
                $cart_addon[$k] = $v;
            }
            // CART ADDON END
            
            $cart_addon_products_ids = array_values(array_unique(array_column($cart_addon, 'id')));
            
            if ($_SESSION["cart_id"]){
                $cart_id = (int)$_SESSION["cart_id"];
                $sql_cart_select = " id = ".$cart_id;
            }else{
                $sql_cart_select = " id = 0 ";
            }
            
            $cart_tmp = $db->getRow("SELECT * FROM ".$carts_table." WHERE ".$sql_cart_select);
            $cartProducts = $db->getAll("SELECT
                                            cp.*,
                                            (SELECT images.pic FROM ".$products_images_table." AS images WHERE images.id = cp.product_image_id) AS pic
                                        FROM 
                                            ".$carts_products_table." AS cp
                                        WHERE 
                                            cp.edate = 0 
                                        AND cp.cart_id = '".$cart_tmp["id"]."'"); safeCheck($cartProducts);
            $total_amount = 0;
            $total_amount_base_currency = 0.0;
            $cart_items = 0;
            $addon_exclude_ids = array();
            $convertor = new Convert();
            foreach($cartProducts as $k => $v){
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
                
                $product = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$products_table." WHERE edate = 0 AND id = '".$v["product_id"]."'"); safeCheck($product);
                
                if ( $product["cart_addon"] && in_array($product["id"], $cart_addon_products_ids) ){
                    $addon_exclude_ids[] = $product["id"];
                }
                $v["product"] = $product;
                
                $total_amount_base_currency += $v["product_price_total"];
                $v["product_price_total_base_currency"] = $v["product_price_total"];
                $v["product_price_base_currency"] = $v["product_price"];
                $v["product_price_discount_base_currency"] = $v["product_price_discount"];
                
                $v["product_price"] = $convertor->convert($v["product_price"]);
                $v["choices_price"] = (double)$v["choices_price"] > 0.0 ? $convertor->convert((double)$v["choices_price"]) : 0.0;
                
                if($v["product_price_discount"] > 0.0){
                    $v["product_price_discount"] = $convertor->convert($v["product_price_discount"]);
                    $v["cart_price"] = $v["product_price_discount"]+$v["choices_price"];
                }else{
                    $v["cart_price"] = $v["product_price"]+$v["choices_price"];
                }
                
                $total_amount += $v["cart_price"]*$v["quantity"];
                $cart_items += $v["quantity"];
                
                $cartProducts[$k] = $v;
            }
            
            foreach ($cart_addon as $k => $v) {
                if(in_array($v["id"], $addon_exclude_ids)){
                    unset($cart_addon[$k]);
                }
            }
            $cart_addon = array_values($cart_addon);
            
            $breadcrumbs = self::generateBreadcrumbs();
            $sm->assign("breadcrumbs", $breadcrumbs);
            
            $sm->assign("cart", $cartProducts);
            $sm->assign("total_amount_base_currency", $total_amount_base_currency);
            $sm->assign("total_amount", number_format($total_amount,2));
            
            $sm->assign("cart_addon", $cart_addon);
            //$sm->assign("has_addon", $has_addon);
            $sm->assign("cart_items", $cart_items);
            $sm->assign("total_amount_top", number_format($total_amount,2));
            
            $favouriteProducts = Products::getFavouriteProducts(6);
            $sm->assign("favouriteProducts", $favouriteProducts);
            
            $suggestedProducts = Products::getSuggestedProducts(8);
            $sm->assign("suggestedProducts", $suggestedProducts);
            
            $sm->display("cart.html");
		}
		
		public function getCheckoutPage(){
			global $sm;
			global $user;
			global $host;
			global $language_file;
			global $htaccess_file;
            //$settingsObj = new Settings();
			//$settingsObj->checkLogin();
			
			$cart = $this->getCart();
            
            $districts = Cities::getDistrictsAllActive();
            $sm->assign("regions", $districts);
            
            if ( $user["id"] ){
                $usersObj = new Users();
                $user_addresses = $usersObj->getUserAddresses($user["id"]);
                $sm->assign("user_addresses", $user_addresses);
                
                $billing_address = Users::getUserBillingAddress();
                if( $billing_address["district_id"] ) {
                    $billing_cities = Cities::getCitiesByDistrictId($billing_address["district_id"]);
                    $sm->assign("billing_cities",$billing_cities);
                }
                
                $delivery_address = Users::getUserDeliveryAddress();
                if( $delivery_address["district_id"] ) {
                    $delivery_cities = Cities::getCitiesByDistrictId($delivery_address["district_id"]);
                    $sm->assign("delivery_cities",$delivery_cities);
                }
                
                $sm->assign("billing_address", $billing_address);
                $sm->assign("delivery_address", $delivery_address);
            }
            
            $promo_code = $cart["promo_code"];
            $discount_promo_code_amount = $promo_code && $cart["discount_promo_code_amount"] > 0 ? (double)$cart["discount_promo_code_amount"] : 0.0;
			$sm->assign("promo_code", $promo_code);
			$sm->assign("discount_promo_code_amount", $discount_promo_code_amount);
            
            $productsAmount = 0;
            if($discount_promo_code_amount > 0.0){
                $productsAmount = $cart["total_amount"];
                $converter = new Convert();
                $totalAmount = (double)$cart["total_amount"]-$converter->convert($discount_promo_code_amount);
                $total_amount_base_currency = (double)$cart["total_amount_base_currency"]-$discount_promo_code_amount;
                
            }else{
                $totalAmount = (double)$cart["total_amount"];
                $total_amount_base_currency = (double)$cart["total_amount_base_currency"];
            }
            
            $presentProducts = array(); 
            if($total_amount_base_currency > 40.0){//was 80 -iskra 16.12.2019
                $presentProducts = Products::getPresentProducts();
            }
            $sm->assign("presentProducts", $presentProducts);
            
            $sm->assign("total_amount", $totalAmount);
            $sm->assign("total_amount_base_currency", $total_amount_base_currency);
            
            $sm->assign("productsAmount", $productsAmount);
            
            $sm->assign("cart_products", $cart["products"]);
			$sm->assign("cart", $cart);
            
            $sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
            $cartLink = $htaccessVars["htaccess_cart"];
            
            $sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
            
            $breadcrumbs = '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>';
            $breadcrumbs .= '<a href="'.$host.$cartLink.'/">'.$configVars["cart_breadcrumbs"].'</a> <span>|</span>';
            $breadcrumbs .= '<span>'.$configVars["checkout_breadcrumbs"].'</span>';
            
            $sm->assign("breadcrumbs", $breadcrumbs);
            
            $order_present = is_numeric($configVars["_order_present"]) ? (int)$configVars["_order_present"] : 0;
            $sm->assign("order_present", $order_present);
            
			$sm->assign("page_checkout", 1);
			$sm->display("checkout.html");
		}
        
		public function getCheckoutFinalPage(FilteredMap $params){
			global $sm;
            global $db;
			global $users_table;
            global $carts_table;
            global $carts_user_table;
            global $users_addresses_table;
            global $promo_codes_usage_log_table;
            global $useBonusPoints;
            global $useShippingApis;
            global $currency;
            global $host;
            global $language_file;
            global $htaccess_file;
            global $listPurchasedId;
            global $error_log_mailchimp_table;
            global $terminalID;
            global $merchantID;
            global $privateKeyFileName;
            global $privateKeyPassword;

            $currency_code = $currency["code"];
            $currency_rate = (double)$currency["rate"];
            
			$cart_products = $this->getCart();
            $cart_id = $cart_products["id"];
            if(!$cart_id){
                $settings = new Settings();
                $settings->getMessagePage(4002);
                die();
            }
            
            $present_product_id = $params->getInt("present_product_id");
//            $promo_code = $params->getString("promo_code_number");
            $promo_code = $cart_products["promo_code"];
            $discount_promo_code_percent = 0.0;
            $discount_promo_code_amount = 0.0;
            $promo_code_name = "";
            if($promo_code){
                $checkPromoCode = PromoCodes::checkPromoCode($promo_code, $cart_products["total_amount_base_currency"]);
                if($checkPromoCode["code"] == 200){
                    $discount_promo_code_percent = $checkPromoCode["promo_code_discount_type"] == 1 ? $checkPromoCode["value"] : 0.0;
                    $discount_promo_code_amount = $checkPromoCode["discount_amount"];
                    //$cart_products["total_amount"] = $checkPromoCode["total_amount_disconted"];
                    $promo_code_name = $checkPromoCode["name"];
                    $checkLog = PromoCodes::getLogRecordByCartId($cart_id);
                    if(!$checkLog){
                        $res = $db->autoExecute($promo_codes_usage_log_table, array(
                            "carts_id"          => $cart_id,
                            "promo_code_id"     => $checkPromoCode["id"],
                            "code"              => $checkPromoCode["code"],
                            "promo_code_discount_type" => $checkPromoCode["promo_code_discount_type"],
                            "value"             => $checkPromoCode["value"],
                            "server_info"       => serialize($_SERVER),
                            "usage_timestamp"   => time(),
                            "usage_datetime"    => date("Y-m-d H:i"),
                            "ip"                => $_SERVER["REMOTE_ADDR"] 
                            ), DB_AUTOQUERY_INSERT); safeCheck($res);
                    }
                }else{
                    $promo_code = null;
                }
            }
            $sm->assign("promo_code_name", $promo_code_name);
            
            $delivery_name = $params->getString("delivery_name");
            $delivery_first_name = $params->getString("delivery_first_name");
            $delivery_family_name = $params->getString("delivery_family_name");
            $delivery_address_line_1 = $params->getString("delivery_address_line_1");
            $delivery_address_line_2 = $params->getString("delivery_address_line_2");
            $delivery_region_id = $params->getInt("delivery_region");
            $delivery_city_id = $params->getInt("delivery_city");
            $delivery_postcode = $params->getString("delivery_postcode");

            $delivery_office_id = $params->getInt("delivery_office_id");
            
            $phone = $params->getString("billing_phone");
            //$country_id = $params->getInt("country_id");
            $billing_name = $params->getString("billing_name");
            $billing_family_name = $params->getString("billing_family_name");
            $billing_address_line_1 = $params->getString("billing_address_line_1");
            $billing_address_line_2 = $params->getString("billing_address_line_2");
            $billing_region = $params->getInt("billing_region");
            $billing_city_id = $params->getInt("billing_city");
            $billing_email = $params->getString("billing_email");
            $billing_postcode = $params->getString("billing_postcode");
            //$billing_country_id = $params->getInt("billing_country_id");
            $delivery_type_id = $params->getInt("delivery_type_id");
            $user_comments = $params->getString("user_comments");
            
            $payment_type_id = $params->getInt("payment_type_id");
            $agree_terms = $params->getInt("agree_terms");
            $agree_terms_gdpr = $params->getInt("agree_terms_gdpr");

            if ($delivery_type_id == 5 || $delivery_type_id == 4) {
                if(!$delivery_name || !$delivery_family_name || !$phone || !$billing_name || !$billing_family_name || !$billing_email) {
                    $settings = new Settings();
                    $settings->getMessagePage(404);
                    die();
                }
            } else {
                if(!$delivery_name || !$delivery_family_name || !$delivery_region_id
                    || !$delivery_city_id || !$phone || !$billing_name || !$billing_family_name
                    || !$billing_region || !$billing_city_id || !$billing_email || !$delivery_type_id){
//                echo "<pre>";
//                var_dump($delivery_name);
//                var_dump($delivery_first_name);
//                var_dump($delivery_family_name);
//                var_dump($delivery_address_line_1);
//                var_dump($delivery_region_id);
//                var_dump($delivery_city_id);
//                var_dump($phone);
//                var_dump($billing_name);
//                var_dump($billing_family_name);
//                var_dump($billing_address_line_1);
//                var_dump($billing_region);
//                var_dump($billing_city_id);
//                var_dump($billing_email);
//                var_dump($delivery_type_id);
//                echo "</pre>";
//                exit();

                    $settings = new Settings();
                    $settings->getMessagePage(404);
                    die();
                }
            }
            // Validate phone
            if (!preg_match("/(0)[0-9]{9}/", trim($phone)) && !preg_match("/(\+359)[0-9]{9}/", trim($phone))) {
                $settings = new Settings();
                $settings->getMessagePage(9999);
                die();
            }
            //get data if wantInvoice is checked
            $wantInvoice = $params->getInt("wantInvoice") == 1 ? 1 : 0;
            $company_name = $wantInvoice ? $params->getString("company_name") : NULL;
            $company_bulstat = $wantInvoice ? $params->getString("company_bulstat") : NULL;
            $company_city = $wantInvoice ? $params->getString("company_city") : NULL;
            $company_mol = $wantInvoice ? $params->getString("company_mol") : NULL;

            if ($delivery_city_id) {
                $delivery_city = Cities::getRecord($delivery_city_id)["name_bg"];
            }
            if ($billing_city_id) {
                $billing_city = Cities::getRecord($billing_city_id)["name_bg"];
            }

            //---- Mailchimp ---------------------------------------------------
            $mailchimpData = array(
                'email'     => $billing_email,
                'status'    => 'subscribed',
                'firstname' => $billing_name,
                'lastname'  => $billing_family_name
            );
            $helpers = new Helpers();
            $result = $helpers->syncMailchimp($listPurchasedId, $mailchimpData);
            
            //--- If http code is not 200 create error log record --------------
            if($result["httpCode"] != 200){
                $file = __FILE__; $line = __LINE__;
                $title = property_exists($result["result"], "title") ? $result["result"]->title : "";
                $status = property_exists($result["result"], "status") ? $result["result"]->status : "";
                $detail = property_exists($result["result"], "detail") ? $result["result"]->detail : "";
                $info = serialize($result);
                $res = $db->autoExecute($error_log_mailchimp_table, array(
                                                                            "delivery_type_id" => $delivery_type_id,
                                                                            "carts_id" => $cart_id,
                                                                            "file" => $file,
                                                                            "line" => $line,
                                                                            "title" => $title,
                                                                            "status" => $status,
                                                                            "detail" => $detail,
                                                                            "info" => $info,
                                                                            "server_info" => serialize($_SERVER),
                                                                            "error_datetime" => date("Y-m-d H:i")
                                                                        ), DB_AUTOQUERY_INSERT); safeCheck($res);
            }
            //---- END error logging -------------------------------------------
            
            $userID = 0;
            if(isset($_SESSION["userID"]) && (int)$_SESSION["userID"] > 0){
                $userID = (int)$_SESSION["userID"];
                $usersObj = new Users();
                $userAddresses = $usersObj->getLoggedUserAddresses();
                $delivery_address_line_1_compare = str_replace(' ', '', strtolower($delivery_address_line_1));
                $billing_address_line_1_compare = str_replace(' ', '', strtolower($billing_address_line_1));
                
                $fieldsDeliveryAddress = array(
                    "firstname" => $delivery_name,
                    "lastname" => $delivery_family_name,
                    "email" => $billing_email,
                    "phone" => $phone,
                    "district_id" => $delivery_region_id,
                    "city_id" => $delivery_city_id,
                    "address_line_1" => $delivery_address_line_1,
                    "address_line_2" => $delivery_address_line_2,
                    "postcode" => $delivery_postcode,
                    "company_name" => $company_name,
                    "vat_number" => $company_bulstat,
                    "company_city" => $company_city,
                    "company_mol" => $company_mol,
                    "default_shipping" => 1,
                    "default_billing" => 0,
                    "user_id" => $userID
                );
                $fieldsBillingAddress = array(
                    "firstname" => $billing_name,
                    "lastname" => $billing_family_name,
                    "email" => $billing_email,
                    "phone" => $phone,
                    "district_id" => $billing_region,
                    "city_id" => $billing_city_id,
                    "address_line_1" => $billing_address_line_1,
                    "address_line_2" => $billing_address_line_2,
                    "postcode" => $billing_postcode,
                    "company_name" => $company_name,
                    "vat_number" => $company_bulstat,
                    "company_city" => $company_city,
                    "company_mol" => $company_mol,
                    "default_shipping" => 0,
                    "default_billing" => 1,
                    "user_id" => $userID
                );
                
                $deliveryAddressExists = 0;
                $billingAddressExists = 0;
                if($userAddresses){
                    foreach ($userAddresses as $value) {
                        $userAddressCompare = str_replace(' ', '', strtolower($value["address_line_1"]));
                        
                        //Check if the delivery address exists
                        if($value["city_id"] == $delivery_city_id && $userAddressCompare == $delivery_address_line_1_compare){
                            $deliveryAddressExists = 1;
                        }
                        //Check if both input addresses are the same
                        if($delivery_city_id == $billing_city_id && $delivery_address_line_1_compare == $billing_address_line_1_compare){
                            $billingAddressExists = 1;
                        }else{
                            //If they are not the same, check if the billing address exists
                            if($value["city_id"] == $billing_city_id && $userAddressCompare == $billing_address_line_1_compare){
                                $billingAddressExists = 1;
                            }
                        }
                    }
                    if($deliveryAddressExists == 0){
                        $res1 = $db->autoExecute($users_addresses_table, array("default_shipping" => 0), DB_AUTOQUERY_UPDATE, " user_id = ".$userID." AND edate = 0"); safeCheck($res1);
                        $res2 = $db->autoExecute($users_addresses_table, $fieldsDeliveryAddress, DB_AUTOQUERY_INSERT); safeCheck($res2);
                    }
                    if($billingAddressExists == 0){
                        $res1 = $db->autoExecute($users_addresses_table, array("default_billing" => 0), DB_AUTOQUERY_UPDATE, " user_id = ".$userID." AND edate = 0"); safeCheck($res1);
                        $res2 = $db->autoExecute($users_addresses_table, $fieldsBillingAddress, DB_AUTOQUERY_INSERT); safeCheck($res2);
                    }
                }else{
                    $res21 = $db->autoExecute($users_addresses_table, $fieldsDeliveryAddress, DB_AUTOQUERY_INSERT); safeCheck($res21);
                    if($delivery_city_id != $billing_city_id || $delivery_address_line_1_compare != $billing_address_line_1_compare){
                        $res22 = $db->autoExecute($users_addresses_table, $fieldsBillingAddress, DB_AUTOQUERY_INSERT); safeCheck($res22);
                    }
                }
            }
            
            $fieldsUser = array(
                                "cart_id" => $cart_id,
                                "user_id" => $_SESSION["user"]["id"],
                                "email" => $billing_email,
                                "delivery_name" => $delivery_name,
                                "delivery_first_name" => $delivery_name,
                                "delivery_last_name" => $delivery_family_name,
                                "delivery_address_1" => $delivery_address_line_1,
                                "delivery_address_2" => $delivery_address_line_2,
                                //Delivery and billing only for Bulgaria
                                //"country_id" => 1,
                                "delivery_region_id" => $delivery_region_id,
                                "delivery_city_id" => $delivery_city_id,
                                "delivery_city" => $delivery_city,
                                "delivery_postcode" => $delivery_postcode,
                                "delivery_phone" => $phone,
                                
                                "billing_first_name" => $billing_name,
                                "billing_last_name" => $billing_family_name,
                                "billing_address_1" => $billing_address_line_1,
                                "billing_address_2" => $billing_address_line_2,
                                //Delivery and billing only for Bulgaria
                                //"billing_country_id" => 1,
                                "billing_region_id" => $billing_region,
                                "billing_city_id" => $billing_city_id,
                                "billing_city" => $billing_city,
                                "billing_postcode" => $billing_postcode,
                                "billing_phone" => $phone,
                                
                                "user_comments" => $user_comments,
                                
                                "want_invoice" => $wantInvoice,
                                "company_name" => $company_name,
                                "company_bulstat" => $company_bulstat,
                                "company_city" => $company_city,
                                "company_mol" => $company_mol,
                                
                                "ip" => $_SERVER["REMOTE_ADDR"],
                                "postdate" => time(),
                                "agree_terms" => $agree_terms,
                                "agree_terms_gdpr" => $agree_terms_gdpr,
                            );
            $res = $db->autoExecute($carts_user_table, $fieldsUser, DB_AUTOQUERY_INSERT); safeCheck($res);
            $delivery_amount = 0.0;
            
            if($useShippingApis && $cart_products["total_weight"] > 0.0){
                $delivery_region = Districts::getRecord($delivery_region_id)["name_bg"];
                
                $receiverSiteId = Delivery::getCourierSiteId($delivery_type_id, $delivery_region, $delivery_city);
                $delivery_amount = Delivery::getDeliveryAmountAndCodBase($delivery_type_id, $cart_products["total_amount_base_currency"], $cart_products["total_weight"], $receiverSiteId, $delivery_office_id)["deliveryAmount"];
            }else{
                $delivery_amount = CartDiscounts::getCustomDeliveryAmount($cart_products["total_amount_base_currency"], $cart_products["total_weight"]);
            }
            
//            $user_id = mysqli_insert_id($db->connection);
            
//            $cart_products = $this->getCart($cart_id);
            
//            $cart_products["cart_items"]
//            $cart_products["products"]
//            $cart_products["total_amount"]
//            $cart_products["total_price_with_delivery"]
//            $cart_products["total_weight"]
            
//            $res = $db->autoExecute($carts_table, array(
//                                                            "total_amount" => $cart_products["total_amount"], 
//                                                            "subtotal_amount" => $cart_products["total_amount"]
//                                                        ), DB_AUTOQUERY_UPDATE, " id = ".$cart_id); safeCheck($res);
            
            //$sm->assign("order_text", $order_text);
            $sm->assign("delivery_type_id", $delivery_type_id);
            //$sm->assign("delivery_price", number_format($delivery_price_use,2));
            //$sm->assign("total_amount_top", number_format($total_amount_sagepay,2));
            $sm->assign("cart_items", $cart_products["cart_items"]);
            
            $user_info = $db->getRow("SELECT * FROM ".$carts_user_table." WHERE cart_id = ".$cart_id); safeCheck($user_info);
            $sm->assign("user_info", $user_info);

            //$discounts = $this->getDiscounts($cart_id);
            $discount_amount = 0.0;
            foreach ($cart_products["products"] as $k => $v) {
                if($v["product_price_discount"] > 0.0){
                    $discount_amount += (($v["product_price_base_currency"] - $v["product_price_discount_base_currency"]) * $v["quantity"]);
                }
            }
            
            $discount_free_delivery = CartDiscounts::isFreeDelivery($cart_products["total_amount_base_currency"], $cart_products["total_weight"]);
            
            $fields = array(
                                "user_id" => $_SESSION["user"]["id"],
                                "payment_type_id" => $payment_type_id,
                                "present_product_id" => $present_product_id,
                                "delivery_type_id" => $delivery_type_id,
                                "delivery_office_id" => $delivery_office_id,
                                "session_id" => session_id(),
                                "promo_code" => $promo_code,
                                "discount_promo_code_percent" => $discount_promo_code_percent,
                                "discount_promo_code_amount" => $discount_promo_code_amount,
                                "discount_amount" => (float)$discount_amount,
                                "discount_free_delivery" => $discount_free_delivery ? 1 : 0,
                                "customer_currency_code" => $currency_code,
                                "customer_currency_rate" => $currency_rate,
                                
                                "subtotal_amount" => $cart_products["subtotal_amount_base_currency"],
                                "delivery_amount" => $delivery_amount,
                                "total_amount" => $cart_products["total_amount_base_currency"] - $discount_promo_code_amount,
                                "total_amount_supply" => $cart_products["total_amount_supply_base_currency"],
                                "total_amount_profit" => $cart_products["total_amount_profit_base_currency"],
                                "weight" => $cart_products["total_weight"],
                
                                "order_timestamp" => time(),
                                
                                "agree_terms" => $agree_terms,
                                "agree_terms_gdpr" => $agree_terms_gdpr
                            );
            //TODO feature (2% discount when total_amount > 100 lv)
            //$fields["discount_amount_delivery"] = 0;
            
            $bonus_points_amount = 0;
            if($useBonusPoints){
                $user = $db->getRow("SELECT * FROM ".$users_table." WHERE id = '".$_SESSION["user"]["id"]."'"); safeCheck($user);
                
                if ( $user["bonus_points"] || $user["bonus_points_tmp"] ){
                    if ( $user["bonus_points"] > 0 ){
                        $bonus_points = $user["bonus_points"];
                    }else{
                        if ( $user["bonus_points_tmp"] > 0 ){
                            $bonus_points = $user["bonus_points_tmp"];
                        }
                    }
                    $bonus_points_currency = $bonus_points / 100;
                    if ( $bonus_points_currency >= $cart_products["total_amount_base_currency"] ){
                        $bonus_points_amount = $cart_products["total_amount_base_currency"];
                        $bonus_points_number = $cart_products["total_amount_base_currency"] * 100;
                    }else{
                        $bonus_points_amount = $bonus_points / 100;
                        $bonus_points_number = $bonus_points;
                    }
                }
                
                $bonus_points_won_amount = round(($cart_products["total_amount_base_currency"] * 1)/100);
                $bonus_points_won_number = round(($cart_products["total_amount_base_currency"] * 1));
                
                //if bonus points are turned on we add them in the carts_table
                $fields["bonus_points_amount"] = (float)$bonus_points_amount;
                $fields["bonus_points_number"] = $bonus_points_number;
                $fields["bonus_points_won_amount"] = $bonus_points_won_amount;
                $fields["bonus_points_won_number"] = $bonus_points_won_number;
                
                $sm->assign("bonus_points_number", (int)$bonus_points_number);
                $sm->assign("bonus_points_amount", $bonus_points_amount);
                
                if ( $user["id"] && $user["bonus_points"] > 0 ){
                    $res = $db->autoExecute($users_table, array("bonus_points_tmp" => $user["bonus_points"], "bonus_points" => 0), DB_AUTOQUERY_UPDATE, "id = '".$user["id"]."'"); safeCheck($res);
                }
            }
            
            // $subtotal_amount = $total_amount - $delivery_amount;
            
            $res = $db->autoExecute($carts_table, $fields, DB_AUTOQUERY_UPDATE, " id = ".$cart_id); safeCheck($res);
            
//            $cart[0]["discount_amount"] = number_format($fields_discount["discount_amount"],2);
//            $cart[0]["discount_free_delivery"] = $fields_discount["discount_free_delivery"];
//            $cart[0]["discount_amount_delivery"] = $fields_discount["discount_amount_delivery"];
//            
//            if ( $cart[0]["discount_free_delivery"] ){
//                // $total_amount_corrected = number_format((double)$total_amount - (double)$bonus_points_amount - (double)$cart[0]["discount_amount"] ,2);
//                $total_amount_corrected = number_format((double)$total_amount - (double)$bonus_points_amount - (double)$cart[0]["discount_amount_delivery"] , 2  );
//            }else{
//                // $total_amount_corrected = number_format((double)$total_amount + (double)$delivery_amount - (double)$bonus_points_amount - (double)$cart[0]["discount_amount"] ,2);
//                $total_amount_corrected = number_format((double)$total_amount + (double)$delivery_amount - (double)$bonus_points_amount  ,2);
//            }
//            $sm->assign("total_amount_corrected", number_format($total_amount_corrected,2));
            
            $row = $this->getRecord($cart_id);
            $sm->assign("row", $row);
            
            $discount_promo_code_amount = (double)$row["discount_promo_code_amount"] > 0.0 ? (double)$row["discount_promo_code_amount"] : 0.0;
            if ( $row["discount_free_delivery"] ){
                // $total_amount_corrected = number_format((double)$total_amount - (double)$bonus_points_amount - (double)$cart[0]["discount_amount"] ,2);
//                $total_amount_corrected = (double)$cart_products["total_amount"] - (double)$bonus_points_amount - (double)$row["discount_amount_delivery"];
                $total_amount_corrected = (double)$row["total_amount"] - (double)$bonus_points_amount - (double)$row["discount_amount_delivery"];
            }else{
                // $total_amount_corrected = number_format((double)$total_amount + (double)$delivery_amount - (double)$bonus_points_amount - (double)$cart[0]["discount_amount"] ,2);
//                $total_amount_corrected = (double)$cart_products["total_amount"] + (double)$delivery_amount - (double)$bonus_points_amount;
                $total_amount_corrected = (double)$row["total_amount"] + (double)$delivery_amount - (double)$bonus_points_amount;
            }
            // TODO remove. This is only for temp test.
            /*if ($billing_email == 'amiladinov@weband.bg') {
                $total_amount_corrected = 0.10;
            }*/
            $convertor = new Convert();
            $delivery_amount = $convertor->convert($delivery_amount);
            $total_amount_corrected = $convertor->convert($total_amount_corrected);
            $discount_promo_code_amount = $convertor->convert($discount_promo_code_amount);
            $sm->assign("delivery_amount", $delivery_amount);
            $sm->assign("total_amount_corrected", $total_amount_corrected);
            $sm->assign("discount_promo_code_amount", $discount_promo_code_amount);
            
            $sm->assign("total_amount", $cart_products["total_amount"]);
            $sm->assign("subtotal_amount", $cart_products["subtotal_amount"]);
            $sm->assign("cart", $cart_products["products"]);

            $product_ids = implode(',', array_column($cart_products["products"], 'product_id'));
            $sm->assign("product_ids", $product_ids);
            
            $sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
            $cartLink = $htaccessVars["htaccess_cart"];
            //$checkoutLink = $htaccessVars["htaccess_checkout"];
            
            $sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
//            $breadcrumbs = '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>';
//            $breadcrumbs .= '<a href="'.$host.$cartLink.'/">'.$configVars["cart_breadcrumbs"].'</a> <span>|</span>';
//            $breadcrumbs .= '<a href="'.$host.$cartLink.'/'.$checkoutLink.'">'.$configVars["checkout_breadcrumbs"].'</a> <span>|</span>';
//            $breadcrumbs .= '<span>'.$configVars["finalise_breadcrumbs"].'</span>';
            $breadcrumbs = '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>';
            $breadcrumbs .= '<a href="'.$host.$cartLink.'/">'.$configVars["cart_breadcrumbs"].'</a> <span>|</span>';
            $breadcrumbs .= '<span>'.$configVars["checkout_breadcrumbs"].'</span> <span>|</span>';
            $breadcrumbs .= '<span>'.$configVars["finalise_breadcrumbs"].'</span>';
            $sm->assign("breadcrumbs", $breadcrumbs);
            
//            $sm->assign("total_amount", (double)$row["total_amount"]+$discount_promo_code_amount);
            //$sm->assign("total_amount", (double)$row["total_amount"]);
            //$sm->assign("subtotal_amount", $row["subtotal_amount"]);
            
            $favouriteProducts = Products::getFavouriteProducts(6);
            $sm->assign("favouriteProducts", $favouriteProducts);
            if($payment_type_id == 1){
                $orderStatusesObj = new OrderStatuses();
                $orderStatusesObj->changeStatus(1, $cart_id, true);
                $this->sendOrderTemplate(1, $cart_id);
                session_destroy();
            } else if($payment_type_id == 2) {
                $paymentForm = eBoricaV2::generateBOReq($privateKeyFileName, $privateKeyPassword, $total_amount_corrected, $terminalID, $cart_id, $merchantID);
                $sm->assign("paymentForm", $paymentForm);
            }
            
            $sm->display("checkout_1.html");
		}
        
        public function finaliseOrder(FilteredMap $params) {
            global $payment_processing_table;
            global $terminalID;
            global $protocolVersion;
            global $privateKeyFileName;
            global $privateKeyPassword;
            global $lng;
            global $db;
            global $user;
            
            $button = $params->getString("finalise");
            
            $cart_id = isset($_SESSION["cart_id"]) ? (int)$_SESSION["cart_id"] : null;
            $currencyCode = isset($_SESSION["currency"]) ? $_SESSION["currency"]["code"] : "BGN";

            if($cart_id && $button == "finalisePay"){
                $row = $this->getRecord($cart_id);
                $amount = $row["total_amount"] + $row["delivery_amount"];
                $payment_description = "козметика";
                
                $transactionCode = '10';
                //$transactionStatusFlag = '0';
                $orderDescription = iconv("UTF-8", "WINDOWS-1251", $payment_description);
                $language = $lng == "bg" ? "BG" : "EN";
                $user_id = isset($user["id"]) ? $user["id"] : 0;
                $fields = array(
                                    "cart_id" => $cart_id,
                                    "user_id" => $user_id,
                                    "transaction_code" => (int)$transactionCode,
                                    "sys" => 'borica',
                                    "amount" => $amount,
                                    "currency" => $currencyCode,
                                    "created" => date("Y-m-d H:i:s"),
                                    "descr" => $payment_description
                                );
                $res = $db->autoExecute($payment_processing_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
                $paymentId = mysqli_insert_id($db->connection);
                //$orderID = $row["id"];
                $orderID = $paymentId;
//                echo "<pre>";
//                var_dump($privateKeyFileName);
//                var_dump($privateKeyPassword);
//                var_dump($transactionCode);
//                var_dump($amount);
//                var_dump($terminalID);
//                var_dump($orderID);
//                var_dump($orderDescription);
//                var_dump($language);
//                var_dump($protocolVersion);
//                echo "</pre>";
//                exit();
                $message = eBorica::generateBOReq($privateKeyFileName, $privateKeyPassword, $transactionCode, $amount, $terminalID, $orderID, $orderDescription, $language, $protocolVersion);
//                $url = eBorica::testGatewayURL . "registerTransaction?eBorica=" . urlencode(base64_encode($message));
                $url = eBorica::gatewayURL . "registerTransaction?eBorica=" . urlencode(base64_encode($message));
                
                header('Location: ' . $url);
                
            }else{
                session_destroy();
                header("Location: /messages/404");
            }
            die();
        }
        
        /**
         * 
         * @global string $boricaPublicKey
         * @global DB $db
         * @param FilteredMap $params
         */
        public function processBoricaResult($params) {
            global $db;
            global $sm;
            global $user;
            global $htaccess_file;
            global $payment_processing_table;
            global $boricaPublicKey;
            global $boricaPublicCert;

            $message = $params->getString("eBorica");
            
            $res = eBoricaV2::parseBOResp($params, $boricaPublicCert);

            $cart_id = (int)$res["ORDER"];
            
            $responseCode = $res["RC"];
            $transactionCode = $res["INT_REF"];
            $terminal_id = $res["TERMINAL"];
            $sign = $res["SIGN"];
            
            $dateStr = $res["TIMESTAMP"];
            $year = substr($dateStr, 0, 4);
            $month = substr($dateStr, 4, 2);
            $day = substr($dateStr, 6, 2);
            $hour = substr($dateStr, 8, 2);
            $minute = substr($dateStr, 10, 2);
            $second = substr($dateStr, 12, 2);
            $responseDate = $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
            
            $post['code'] = $responseCode;
            $post['amount'] = $res['AMOUNT'];
            $user_id = isset($user["id"]) ? $user["id"] : 0;

            if($responseCode == "00"){
                $post['message'] = "Плащане с карта: Нормално изпълнена авторизация";
                $post['status_id'] = "11";
                $post['status'] = "Платено";
            }else if($responseCode == "54"){
                $post['message'] = "Картата е с изтекла валидност";
                $post['status_id'] = "10";
                $post['status'] = "Отказано";
            }else if($responseCode == "13"){
                $post['message'] = "Плащане с карта: БОРИКА - Няма достатъчна наличност";
                $post['status_id'] = "10";
                $post['status'] = "Отказано";
            }else if($responseCode == "55"){
                $post['message'] = "Грешен ПИН";
                $post['status_id'] = "10";
                $post['status'] = "Отказано";
            }else if($responseCode == "57"){
                $post['message'] = "Плащане с карта: БОРИКА - Транзакцията не е позволена";
                $post['status_id'] = "10";
                $post['status'] = "Отказано";
            }else if($responseCode == "59"){
                $post['message'] = "Плащане с карта: БОРИКА - Транзакцията не е позволена";
                $post['status_id'] = "10";
                $post['status'] = "Отказано";
            }else if($responseCode == "17"){
                $post['message'] = "Плащане с карта: БОРИКА - Спряна транзакция";
                $post['status_id'] = "10";
                $post['status'] = "Отказано";
            }else if($responseCode == "96"){
                $post['message'] = "Плащане с карта: БОРИКА - Техническа грешка при обработка на транзакцията";
                $post['status_id'] = "10";
                $post['status'] = "Отказано";
            }else if($responseCode == "41"){
                $post['message'] = "Плащане с карта: БОРИКА - Тази карта е обявена за открадната/изгубена";
                $post['status_id'] = "10";
                $post['status'] = "Отказано";
            }else{
                $post['message'] = "Плащане с карта: БОРИКА - Отхвърлена";
                $post['status_id'] = "10";
                $post['status'] = "Отказано";
            }
            //set payment status_id and message
            $post['payment_amount'] = $res['AMOUNT'];
            
            $date = new DateTime("now", new DateTimeZone('Europe/Sofia'));
            $currentDateTime = $date->format('Y-m-d H:i:s');

            $resPPUpdate = $db->autoExecute($payment_processing_table, array(
                "status_id" => (int)$post["status_id"],
                "cart_id" => $cart_id,
                "user_id" => $user_id,
                "sys" => 'borica',
                "amount" => $post["amount"],
                "borica_response_message" => $post["message"],
                "transaction_code" => $transactionCode,
                "response_code" => $responseCode,
                "transaction_time" => $responseDate,
                "terminal_id" => $terminal_id,
                "signature" => $sign,
                "updated" => $currentDateTime,
            ), DB_AUTOQUERY_INSERT); safeCheck($resPPUpdate);
            $orderStatusesObj = new OrderStatuses();
            $orderStatusesObj->changeStatus((int)$post['status_id'], $cart_id, true);
            self::sendOrderTemplate((int)$post['status_id'], $cart_id);
            
            $sm->configLoad($htaccess_file);
            $htaccessVars = $sm->getConfigVars();
            if((int)$post['status_id'] == 11){
                session_destroy();
                header("Location: /".$htaccessVars["htaccess_messages"]."/701");
            }else{
                session_destroy();
                header("Location: /".$htaccessVars["htaccess_messages"]."/414");
            }

            die();
        }
        
        public static function updatePaymentProcessingField(int $id, string $field, $value) {
            global $db;
            global $payment_processing_table;
            
            $res = $db->autoExecute($payment_processing_table, [$field => $value], DB_AUTOQUERY_UPDATE, "id = " . $id); safeCheck($res);
            
            return $res;
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
        
        public function getOrderRatingPage(FilteredMap $params){
            global $sm;
            global $db;
            global $htaccess_file;
            global $products_comments_table;
            global $products_rating_links_table;
            
            $cart_id = $params->getInt("cart_id");
            $rating_hash = $params->getString("rating_hash");
            
            $row = $this->getRecord($cart_id);
            
            if($params->has("order_rating_proceed")){
                $sm->configLoad($htaccess_file);
                $htaccessVars = $sm->getConfigVars();
                if(($cart_id > 0 && $rating_hash && $rating_hash == $row["rating_hash"]) == false){
                    header("Location: /".$htaccessVars["htaccess_messages"]."/411");
                    die();
                }
                $products = $params->get("products");
                $user_id = $row["user_id"] ? $row["user_id"] : 0;
                foreach ($products as $v) {
                    $product_id = intval($v["product_id"]);
                    $hash = trim($v["hash"]);
                    $ratingScore = floatval($v["rating_score"]);
                    
                    $check = $db->getRow("SELECT * FROM {$products_rating_links_table} WHERE cart_id = {$cart_id} AND product_id = {$product_id} AND active = 1 AND edate = 0");
                    if($check && trim($check["hash"]) == $hash){
                        if($check["rated"] == 0){
                            $res1 = $db->autoExecute($products_comments_table, array("postdate" => time(), "user_id" => $user_id, "rating" => $ratingScore, "product_id" => $product_id), DB_AUTOQUERY_INSERT); safeCheck($res1);
                            $res2 = $db->autoExecute($products_rating_links_table, array("user_id" => $user_id, "rated" => 1, "updated" => time() ), DB_AUTOQUERY_UPDATE, " id = ".$check["id"]); safeCheck($res2);
                        }
                    }else{
                        header("Location: /".$htaccessVars["htaccess_messages"]."/411");
                        die();
                    }
                }
                header("Location: /".$htaccessVars["htaccess_messages"]."/211");
                die();
            }
            if(($cart_id > 0 && $rating_hash && $rating_hash == $row["rating_hash"]) == false){
                header("Location: /");
                die();
            }
            
            $products = $this->getProductsForRating($cart_id);
            $sm->assign("products", $products);
            $hasUnrated = 0;
            foreach ($products as $v) {
                if($v["rated"] == 0){
                    $hasUnrated = 1;
                    break;
                }
            }
            $sm->assign("hasUnrated", $hasUnrated);
            
            $sm->display("order_rating.html");
        }
        
        public function sendOrderTemplate($order_status_id, $cart_id){
            global $db;
            global $emails_test;
            global $carts_table;
            global $carts_user_table;
            global $ordersEmail;
            global $ordersEmail1;

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
            mailSender($ordersEmail, $subject, $message);
            mailSender($ordersEmail1, $subject, $message);
            foreach($emails_test as $k => $v){
                mailSender($v, $subject, $message);
            }
        }
        
        public function createOrderTemplate( $cart_id ){
            global $db;
            global $carts_user_table;
            global $useBonusPoints;
            
            $tmp_cart = $this->getCart($cart_id, 0, 1);
            $sql_cart_use = " cart_id = '".$tmp_cart["id"]."' ";

            $user_info = $db->getRow("SELECT * 
                                    FROM ".$carts_user_table." AS users
                                    WHERE ".$sql_cart_use." ORDER BY id DESC"); safeCheck($user_info);
            $promo_code_discount = $tmp_cart["discount_promo_code_amount"];
            
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
                                            <strong>'.$v["product"]["name_en"].", ".$v["brand_name"].", ".$v["product"]["name"].'</strong><br />
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
                if ($tmp_cart["promo_code"] && $tmp_cart["discount_promo_code_amount"]){ 
                $message .= '
                <tr>
                    <td>
                        Промо код: "'.$tmp_cart["promo_code"].'"
                    </td>
                    <td>		
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td align="right">
                        - '.$promo_code_discount.' лв.
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
        
		public static function updateQuantity(FilteredMap $params, int $return_type = 3){
			global $db;
			global $sm;
			global $htaccess_file;
			global $language_file;
			global $carts_products_table;
			global $carts_table;
			global $products_table;

			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
            
			$sm->configLoad($language_file);
			$langVars = $sm->getConfigVars();
			
            $cart_id = (int)$_SESSION["cart_id"];
            $result = array();
            if(!$cart_id){
                if($return_type != 3){
                    $settings = new Settings();
                    $settings->getMessagePage(4001);
                    die();
                }else{
                    $result["code"] = 0;
                    $result["title"] = $langVars["message_title_4001"];
                    $result["message"] = $langVars["message_description_4001"];
                }
            }
			$cart_products = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE cart_id = '".$cart_id."' AND edate = 0"); safeCheck($cart_products);
            
            $convertor = new Convert();
            foreach($cart_products as $k=>$v){
                if ($v["option_id"]){
                    $option_id = $v["option_id"];
                }else{
                    $option_id = "";
                }
                $v["quantity"] = $params->getInt("quantity".$v["product_id"].$option_id);

                $product = $db->getRow("SELECT * FROM ".$products_table." WHERE id = ".$v["product_id"]); safeCheck($product);
                // Check quantity
                if ($product['quantity'] < $v['quantity']) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => true, 'message' => 'Недостатъчна наличност', 'quantity' => $v['quantity'], 'available' => $product['quantity'], 'id' => "quantity".$v["product_id"].$option_id]);
                    exit;
                }

                if ( $v["product_price_discount"] > 0.0 ){
                    $v["product_price_total"] = $v["quantity"]*$v["product_price_discount"];
                }else{
                    $v["product_price_total"] = $v["quantity"]*$v["product_price"];
                }
                
                $res = $db->autoExecute($carts_products_table, array("quantity" => $v["quantity"], "product_price_total" => $v["product_price_total"] ), DB_AUTOQUERY_UPDATE, " id = '".$v["id"]."' "); safeCheck($res);
                $v["product_price_total"] = $convertor->convert($v["product_price_total"]);
                $cart_products[$k] = $v;
            }
            $fields = array(
                                "promo_code" => null,
                                "discount_promo_code_percent" => 0.0,
                                "discount_promo_code_amount" => 0.0
                            );    
            $res = $db->autoExecute($carts_table, $fields, DB_AUTOQUERY_UPDATE, " id = ".$cart_id); safeCheck($res);
            
            //$_SESSION["cart"] = $cart_products;
            //$cart = $db->getRow("SELECT * FROM ".$carts_table." WHERE ".$sql_cart_select); safeCheck($cart);
            //$cart_id = $cart["id"];
			
            if($return_type != 3){
                header("Location: /".$htaccessVars["htaccess_cart"]."/");
                die();
            }else{
                $result["code"] = 1;
                $result["title"] = "Successful";
                $result["message"] = "Successful";
                $result["products"] = $cart_products;
                echo json_encode($result);
                die();
            }
		}
		
		public function deleteProductFromCart(FilteredMap $params){
			global $db;
			global $sm;
			global $carts_products_table;
			global $htaccess_file;
            global $host;

            $sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$remove_id = $params->getInt("id");
			
			$res = $db->autoExecute($carts_products_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, "id = '".$remove_id."'"); safeCheck($res);
            
//			$cart = $this->getCart();
//			$_SESSION["cart"] = $cart;
            
			header("Location: /".$htaccessVars["htaccess_cart"]."/");
			die();
		}
		
		public function getCart($cart_id_preset = 0, $user_id = 0, $finished = 0){
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
                $subtotal_amount_base_currency = 0.0;
                $total_amount = 0.0;
                $total_amount_base_currency = 0.0;
                $total_amount_supply_base_currency = 0.0;
                $total_amount_profit_base_currency = 0.0;
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
                    
                    $total_amount_base_currency += $v["product_price_total"];
                    $total_amount_supply_base_currency += $v["product_price_supply"] * $v["quantity"];
                    $total_amount_profit_base_currency += $v["product_price_profit"] * $v["quantity"];
                    $v["product_price_total_base_currency"] = $v["product_price_total"];
                    $v["product_price_base_currency"] = $v["product_price"];
                    $v["product_price_discount_base_currency"] = $v["product_price_discount"];
                    
                    $convertor = new Convert();
                    $v["product_price_total"] = $convertor->convert($v["product_price_total"]);
                    
                    $v["product_price"] = $convertor->convert($v["product_price"]);
                    
                    if($v["product_price_discount"] > 0){
                        $subtotal_amount_base_currency += (float)$v["product_price_discount_base_currency"]*(int)$v["quantity"];
                        $subtotal_amount += (float)$v["product_price_discount"]*(int)$v["quantity"];
                    }else{
                        $subtotal_amount_base_currency += (float)$v["product_price_base_currency"]*(int)$v["quantity"];
                        $subtotal_amount += (float)$v["product_price"]*(int)$v["quantity"];
                    }
                    
                    if((float)$v["product_price_discount"] > 0.0){
                        $v["product_price_discount"] = $convertor->convert($v["product_price_discount"]);
                    }
                    
                    $total_amount += $v["product_price_total"];
                    $cart_items += $v["quantity"];
                    $total_weight += $v["product_weight_total"];
                    $cartProducts[$k] = $v;
                }
                if($finished){
                    $cart["total_price_with_delivery"] = $cart["discount_free_delivery"] == 1 ? $cart["total_amount"] : $cart["total_amount"] + $cart["delivery_amount"];
                }else{
                    $cart["subtotal_amount"] = $subtotal_amount;
                    $cart["total_amount"] = number_format($total_amount, 2);
                    $cart["total_price_with_delivery"] = $total_amount + $cart["delivery_amount"];
                }
                
                $cart["total_amount_base_currency"] = $total_amount_base_currency;
                $cart["total_amount_supply_base_currency"] = $total_amount_supply_base_currency;
                $cart["total_amount_profit_base_currency"] = $total_amount_profit_base_currency;
                $cart["subtotal_amount_base_currency"] = $subtotal_amount_base_currency;
                //$cart["delivery_amount_show"] = calculateDelivery($cart["id"]);
                $cart["total_weight"] = $total_weight;
                $cart["cart_items"] = $cart_items;
                $cart["products"] = $cartProducts;
                
                return $cart;
            }
			return array();
		}
        
		public function getProductsForRating(int $cart_id){
			global $db;
			global $lng;
			global $products_table;
			global $products_images_table;
			global $products_rating_links_table;
			
            if($cart_id > 0){
                $products = $db->getAll("SELECT
                                            prl.id,
                                            prl.product_id,
                                            prl.cart_id,
                                            prl.user_id,
                                            prl.`hash`,
                                            prl.rated,
                                            prl.active,
                                            p.name_{$lng} AS product_name,
                                            (SELECT images.pic FROM {$products_images_table} AS images WHERE images.product_id = prl.product_id AND images.edate = 0 ORDER BY images.pos LIMIT 1) AS pic
                                        FROM
                                            {$products_rating_links_table} AS prl
                                        INNER JOIN {$products_table} AS p ON p.id = prl.product_id
                                        WHERE
                                            prl.cart_id = {$cart_id}
                                        AND prl.active = 1
                                        AND prl.edate = 0
                                        AND p.edate = 0"); safeCheck($products);
                return $products;
            }
			return array();
		}
        
        public function getMyordersPage(FilteredMap $params) {
            global $user;
            global $db;
            global $sm;
            global $host;
            global $htaccess_file;
            global $language_file;
            global $carts_table;
            global $carts_user_table;
            global $useBonusPoints;

            $user_id = isset($user["id"]) ? $user["id"] : null;
            $id = $params->getInt("id");
            
            $cart = array();
            $user_info = array();
            
            if($user && $user_id){
                if($id){
                    $cart = $this->getCart($id, $user_id, 1);
                    $user_info = $db->getRow("SELECT * FROM ".$carts_user_table." WHERE cart_id = ".$id); safeCheck($user_info);
                }
                
                $sql = "SELECT 
                            *, 
                            FROM_UNIXTIME(postdate, '%Y') AS post_year
                        FROM 
                            ".$carts_table." AS orders
                        WHERE 
                            edate = 0 
                            AND user_id = ".$user_id."
                        ORDER BY 
                            id DESC";
                $carts = $db->getAll($sql); safeCheck($carts);
                foreach($carts as $k => $v){
                    if ( $useBonusPoints ){
                        $v["total_amount"] = $v["total_amount"] - $v["bonus_points_amount"];
                    }
                    $carts[$k] = $v;
                }
                
//                $cart["subtotal_amount"] = $subtotal_amount;
//                $cart["total_amount"] = $total_amount;
//                $cart["total_price_with_delivery"] = $total_amount + $cart["delivery_amount"];
//                $cart["cart_items"] = $cart_items;
//                $cart["total_weight"] = $total_weight;
//                $cart["products"] = $cartProducts;
//                echo "<pre>";
//                var_dump($cart);
//                echo "</pre>";
//                exit();
                $sm->assign("carts", $carts);
                
                $sm->assign("user", $user);
                $sm->assign("user_info", $user_info);
                
                $sm->configLoad($htaccess_file);
                $htaccessVars = $sm->getConfigVars();
                $sm->configLoad($language_file);
                $configVars = $sm->getConfigVars();
                
                $breadcrumbs = '<li class="breadcrumb-item"><a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a></li>';
                $breadcrumbs .= '<li class="breadcrumb-item"><a href="'.$host.$htaccessVars["htaccess_my_profile"].'">'.$configVars["myprofile_breadcrumbs"].'</a></li>';
                if($id){
                    $breadcrumbs .= '<li class="breadcrumb-item"><a href="'.$host.$htaccessVars["htaccess_myorders"].'">'.$configVars["my_orders_breadcrumbs"].'</a></li>';
                    $breadcrumbs .= '<li class="breadcrumb-item">'.$configVars["order_number_breadcrumbs"].' '.$id.'</li>';
                }else{
                    $breadcrumbs .= '<li class="breadcrumb-item">'.$configVars["my_orders_breadcrumbs"].'</li>';
                }
                $sm->assign("breadcrumbs", $breadcrumbs);
                
                $sm->assign("row", $cart);
                $sm->assign("cart", $cart["products"]);
                $sm->assign("subtotal_amount", $cart["subtotal_amount"]);
                $sm->assign("total_amount", $cart["total_amount"]);
                $sm->assign("total_amount_corrected", $cart["total_price_with_delivery"]);
                
                
                $sm->display("myorders.html");
            }else{
                header("Location: /");
                die();
            }
        }
        
		function cartCalculateTotal($id){
			global $db;
			global $carts_table;
			global $carts_users_table;
			global $carts_products_table;
			
			$cart = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$id."'"); safeCheck($cart);
			$cart_products = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE cart_id = '".$id."' AND edate = 0"); safeCheck($cart_products);
			$total_weight = 0;
			foreach($cart_products as $k => $v){
				$tmp_total_amount = 0;
				$tmp_total_weight = 0;
				$tmp_total_amount = $v["quantity"] * $v["product_price"];
				$tmp_total_weight = $v["quantity"] * $v["product_weight"];
				
				$total_amount += $tmp_total_amount;
				$total_weight += $tmp_total_weight;
				$cart_products[$k] = $v;
			}
			$cart["weight"] = $total_weight;
			
			$res = $db->autoExecute($carts_table, array("total_amount" => $total_amount, "delivery_price" => $delivery_price_use), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			$res = $db->autoExecute($carts_users_table, array("total_amount" => $total_amount), DB_AUTOQUERY_UPDATE, " id = '".$user_info["id"]."' "); safeCheck($res);
			
			$total_amount_with_delivery = $total_amount + $delivery_price_use;
			
			return $total_amount_with_delivery;
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
                $discount_categories = $db->getAll("SELECT * FROM ".$category_to_discount_table." WHERE discount_id = '".$v["id"]."'"); safeCheck($discount_categories);
                $discount_brands = $db->getAll("SELECT * FROM ".$brand_to_discount_table." WHERE discount_id = '".$v["id"]."'"); safeCheck($discount_brands);
                $discount_products = $db->getAll("SELECT * FROM ".$product_to_discount_table." WHERE discount_id = '".$v["id"]."'"); safeCheck($discount_products);
                
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
                
                foreach($discount_brands as $kk => $vv){
                    foreach($cart_products as $kkk => $vvv){
                        $product_tmp = $db->getRow("SELECT id FROM ".$products_table." WHERE brand_id = '".$vv["brand_id"]."' AND id = '".$vvv["product_id"]."'"); safeCheck($product_tmp);
                        if ( $product_tmp["id"] ){
                            if ( $vvv["product_price_discount"] > 0.0 ){
                                $product_discount = (($vvv["product_price_total"] - $vvv["product_price_discount"]) * $v["discount_value"]/100);
                            }else{
                                $product_discount = (($vvv["product_price_total"]) * $v["discount_value"]/100);
                            }
                            $discount_value += $product_discount;
                            
                            $vvv["product_price_discount"] = (double)$product_discount + $vvv["product_price_discount"] ;
                        }
                        $cart_products[$kkk] = $vvv;
                    }
                }
                
                foreach($discount_products as $kk => $vv){
                    foreach($cart_products as $kkk => $vvv){
                        if ( $vvv["product_id"] == $vv["product_id"] ){
                            if ( $v["discount_type"] == 1 ){
                                $product_discount = ($vvv["product_price_total"] * $v["discount_value"]/100);
                            }else if($v["discount_type"] == 2){
                                $product_discount = $v["discount_value"];
                            }
                            $discount_value += $product_discount;
                            
                            //$vvv["product_price_total"] = $vvv["product_price_total"] - $product_discount;
                            $vvv["product_price_discount"] = (double)$product_discount;
                        }
                        $cart_products[$kkk] = $vvv;
                    }
                }
            }
            $discount_results["discount_amount"] = $discount_value;
            
            return $discount_results;
        }
		
	}
	
