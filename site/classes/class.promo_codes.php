<?php

class PromoCodes extends Settings {

    public static function getRecord(int $id) {
        global $db;
        global $lng;
        global $promo_codes_table;

        $row = $db->getRow("SELECT *, name_{$lng} AS name FROM " . $promo_codes_table . " WHERE id = {$id} AND edate = 0"); safeCheck($row);

        return $row;
    }

    public static function getActiveRecordByCode($code, $returnType = 1) {
        global $db;
        global $lng;
        global $promo_codes_table;

        $code_clean = htmlspecialchars(trim($code), ENT_QUOTES);

        $row = $db->getRow("SELECT
                                *,
                                name_{$lng} AS name
                            FROM
                                " . $promo_codes_table . "
                            WHERE
                                code = '{$code_clean}'
                            AND active = 1
                            AND edate = 0"); safeCheck($row);
        if ($returnType === 3) {
            echo json_encode($row);
            die();
        }
        return $row;
    }

    public static function getLogRecordByCartId(int $cart_id) {
        global $db;
        global $promo_codes_usage_log_table;

        $row = $db->getRow("SELECT
                                *
                            FROM
                                " . $promo_codes_usage_log_table . "
                            WHERE
                                carts_id = {$cart_id}"); safeCheck($row);
        return $row;
    }

    public static function getPromoCodes($name = '', $status = NULL) {
        global $db;
        global $lng;
        global $promo_codes_table;

//            echo "<pre>";
//            var_dump(self::checkPromoCode("OCTOBER-SALE", 400.00));
//            //var_dump(self::checkPromoCode("TEST1", 499.99));
//            echo "</pre>";
//            exit();

        $sql_where = '';
        if ($status === 1) {
            $sql_where .= ' AND active = 1';
        }
        if ($status === 0) {
            $sql_where .= ' AND active = 0';
        }
        if ($name) {
            $sql_where .= ' AND name_bg LIKE "%' . $name . '%"';
        }
        $sql = "SELECT
                    id,
                    name_{$lng} AS name,
                    code,
                    promo_code_discount_type,
                    valid_date_from,
                    valid_date_to,
                    order_amount_from,
                    order_amount_to,
                    value,
                    pos,
                    active
                FROM " . $promo_codes_table . "
                WHERE
                    edate = 0 " . $sql_where . "
                ORDER BY pos";
        $items = $db->getAll($sql); safeCheck($items);

        return $items;
    }

    /**
     *
     * @global type $language_file
     * @global Smarty $sm
     * @param string $code
     * @param float $total_amount
     * @param int $returnType
     * @return array or JSON
     */
    public static function checkPromoCode(string $code, float $total_amount, int $returnType = 1) {
        global $language_file;
        global $sm;
        global $db;	
        global $carts_table;	
        global $carts_products_table;	
        global $product_to_category_table;	
        global $products_table;	
        global $user;

        $cart_id = (int)$_SESSION["cart_id"];

        $sm->configLoad($language_file);
        $languageVars = $sm->getConfigVars();

        $converter = new Convert();	
        $total_amount = $converter->convertToBaseCurrency($total_amount);

        $check = self::getActiveRecordByCode($code);
        $result = array();
        if ($check) {
            $id = (int)$check["id"];
            $order_amount_from = (double)$check["order_amount_from"]; //>= 0.0 ? (double)$check["order_amount_from"] : 0;
            $order_amount_to = (double)$check["order_amount_to"]; //>= 0.0 ? (double)$check["order_amount_to"] : 0;
            $valid_date_from = $check["valid_date_from"];
            $valid_date_to = $check["valid_date_to"];
            $promo_code_discount_type = (int) $check["promo_code_discount_type"];
            $value = (double)$check["value"];
            $discount_amount = 0.0;
            $total_amount_disconted = 0.0;
            if ($total_amount >= $order_amount_from && $order_amount_to = $order_amount_to > 0.0 ? $total_amount <= $order_amount_to : true) {
                $now = date("Y-m-d");
                if ($now >= $valid_date_from && $valid_date_to = $valid_date_to > 0 ? $now <= $valid_date_to : true) {
                    if($check["is_one_time"]) {	
                        $promoCodeAlreadyUsed['orders'] = 0;	
                        if(!empty($user)) {	
                            $userId = $user['id'];	
                            $sql = "SELECT	
                                COUNT(id) as orders	
                            FROM	
                                " . $carts_table . "	
                            WHERE	
                                promo_code = '{$code}'	
                            AND user_id = '{$userId}'";	
                            $promoCodeAlreadyUsed = $db->getRow($sql); safeCheck($promoCodeAlreadyUsed);	
                        }	
                        if(empty($user) || $promoCodeAlreadyUsed['orders'] != 0) {	
                            $result["code"] = 400;	
                            $result["message"] = $languageVars["wrong_promo_code"];	
                            $res = $db->autoExecute($carts_table, array("promo_code" => null, "discount_promo_code_amount" => 0), DB_AUTOQUERY_UPDATE, "id = '".$cart_id."'"); safeCheck($res);	
                            if ($returnType === 3) {	
                                echo json_encode($result);	
                                die();	
                            }	
                            return $result;	
                        }	
                    }
                    $typeString = $promo_code_discount_type == 1 ? "%" : "лв";
                    $result["code"] = 200;
                    $result["message"] = $languageVars["valid_promo_code_1"] . " " . $value . $typeString . " " . $languageVars["valid_promo_code_2"];
                    $promoCodeToCategories = PromoCodes::getPromoCodeCategories($id);	
                    $promoCodeToBrands = PromoCodes::getPromoCodeBrands($id);	
                    $promoCodeToCollections = PromoCodes::getPromoCodeCollections($id);	
                    $promoCodeToProducts = PromoCodes::getPromoCodeProducts($id);	
                    $total_amount_of_non_discounted_products = 0;	
                    $cartProducts = $db->getAll("SELECT	
                            *	
                        FROM	
                            ".$carts_products_table."	
                        WHERE	
                            edate = 0	
                        AND cart_id = '".$cart_id."'");	
                    safeCheck($cartProducts);	

                    foreach($cartProducts as $product) {	
                        $eligibleToApplyCode = true;	
                        if($product['product_price_discount'] > 0 && !$check["force_apply"]) {	
                            $eligibleToApplyCode = false;	
                        } 	
                        if($promoCodeToCategories) {	
                            $productId = $product['product_id'];	
                            $productCategories = $db->getAll("SELECT category_id FROM ".$product_to_category_table." WHERE product_id = $productId");safeCheck($productCategories);	
                            $productCategories = array_column($productCategories, 'category_id');	
                            	
                            if(!array_diff($productCategories, $promoCodeToCategories)) {	
                                $eligibleToApplyCode = false;	
                            }	
                        }	
                        if($promoCodeToBrands && !in_array($product['brand_id'], $promoCodeToBrands)) {	
                            $eligibleToApplyCode = false;	
                        }	
                        if($promoCodeToCollections) {	
                            $productId = $product['product_id'];	
                            $baseProduct = $db->getRow("SELECT * FROM ".$products_table." WHERE id = $productId");safeCheck($baseProduct);	
                            	
                            if($promoCodeToCollections && !in_array($baseProduct['collection_id'], $promoCodeToCollections)) {	
                                $eligibleToApplyCode = false;	
                            }	
                        }	
                        if($promoCodeToProducts && !in_array($product['product_id'], $promoCodeToProducts)) {	
                            $eligibleToApplyCode = false;	
                        }	
                        if($eligibleToApplyCode) {	
                            $total_amount_of_non_discounted_products += $product['product_price_total'];	
                        }	
                    }
                    if($promo_code_discount_type == 1){
                        $total_amount_disconted = $total_amount - (($total_amount_of_non_discounted_products * $value) / 100);	
                        $discount_amount = $total_amount_of_non_discounted_products * $value / 100;
                    }else if($promo_code_discount_type == 2){
                        $total_amount_of_discounted_products = $total_amount - $total_amount_of_non_discounted_products;	
                        if($total_amount_of_non_discounted_products > 0 ) {	
                            $total_amount_disconted = $total_amount_of_discounted_products + ($total_amount_of_non_discounted_products - $value);	
                            $discount_amount = $value;
                        } else {	
                            $total_amount_disconted = $total_amount;	
                        }
                    }
                    
                    $res = $db->autoExecute($carts_table, array("promo_code" => $code, "discount_promo_code_amount" => $discount_amount), DB_AUTOQUERY_UPDATE, "id = '".$cart_id."'"); safeCheck($res);
                } else {
                    $result["code"] = 402;
                    $result["message"] = $languageVars["invalid_promo_code_date"];
                }
            } else {
                $result["code"] = 401;
                $result["message"] = $languageVars["invalid_promo_code_amount"];
            }
            $result["id"] = $id;
            $result["name"] = $check["name"];
            $result["value"] = $value;
            $result["discount_amount"] = $discount_amount;
            $result["total_amount_disconted"] = $total_amount_disconted;
            $result["promo_code_discount_type"] = $promo_code_discount_type;
            $result["order_amount_from"] = $order_amount_from;
            $result["order_amount_to"] = (double) $check["order_amount_to"];
            $result["valid_date_from"] = $valid_date_from;
            $result["valid_date_to"] = $check["valid_date_to"];
        } else {
            $result["code"] = 400;
            $result["message"] = "Грешен промо код";
        }

        if ($returnType === 3) {
            echo json_encode($result);
            die();
        }

        return $result;
    }

    /**
     *
     * @global type $language_file
     * @global Smarty $sm
     * @param string $code
     * @param float $total_amount
     * @param int $returnType
     * @return array or JSON
     */
    public static function applyPromoCode(string $code, float $total_amount, int $returnType = 1) {
        global $language_file;
        global $sm;
        global $db;
        global $brands_table;
        global $carts_table;
        global $carts_products_table;
        global $product_to_category_table;
        global $products_table;
        global $user;

        $cart_id = (int)$_SESSION["cart_id"];

        $sm->configLoad($language_file);
        $languageVars = $sm->getConfigVars();

        $converter = new Convert();
        $total_amount = $converter->convertToBaseCurrency($total_amount);

        $check = self::getActiveRecordByCode($code);
        $result = array();
        if ($check) {
            $id = (int)$check["id"];
            $order_amount_from = (double)$check["order_amount_from"]; //>= 0.0 ? (double)$check["order_amount_from"] : 0;
            $order_amount_to = (double)$check["order_amount_to"]; //>= 0.0 ? (double)$check["order_amount_to"] : 0;
            $valid_date_from = $check["valid_date_from"];
            $valid_date_to = $check["valid_date_to"];
            $promo_code_discount_type = (int) $check["promo_code_discount_type"];
            $value = (double)$check["value"];
            $discount_amount = 0.0;
            $total_amount_disconted = 0.0;
            if ($total_amount >= $order_amount_from && $order_amount_to = $order_amount_to > 0.0 ? $total_amount <= $order_amount_to : true) {
                $now = date("Y-m-d");
                if ($now >= $valid_date_from && $valid_date_to = $valid_date_to > 0 ? $now <= $valid_date_to : true) {
                    if($check["is_one_time"]) {
                        $promoCodeAlreadyUsed['orders'] = 0;
                        if(!empty($user)) {
                            $userId = $user['id'];
                            $sql = "SELECT
                                COUNT(id) as orders
                            FROM
                                " . $carts_table . "
                            WHERE
                                promo_code = '{$code}'
                            AND user_id = '{$userId}'";
                            $promoCodeAlreadyUsed = $db->getRow($sql); safeCheck($promoCodeAlreadyUsed);
                        }

                        if(empty($user) || $promoCodeAlreadyUsed['orders'] != 0) {
                            $result["code"] = 400;
                            $result["message"] = $languageVars["wrong_promo_code"];

                            $res = $db->autoExecute($carts_table, array("promo_code" => null, "discount_promo_code_amount" => 0), DB_AUTOQUERY_UPDATE, "id = '".$cart_id."'"); safeCheck($res);

                            if ($returnType === 3) {
                                echo json_encode($result);
                                die();
                            }

                            return $result;
                        }
                    }
                    $typeString = $promo_code_discount_type == 1 ? "%" : "лв";
                    $result["code"] = 200;
                    $result["message"] = $languageVars["valid_promo_code_1"] . " " . $value . $typeString . " " . $languageVars["valid_promo_code_2"];

                    $promoCodeToCategories = PromoCodes::getPromoCodeCategories($id);
                    $promoCodeToBrands = PromoCodes::getPromoCodeBrands($id);
                    $promoCodeToCollections = PromoCodes::getPromoCodeCollections($id);
                    $promoCodeToProducts = PromoCodes::getPromoCodeProducts($id);

                    $total_amount_of_non_discounted_products = 0;
                    $cartProducts = $db->getAll("SELECT
                            *
                        FROM
                            ".$carts_products_table."
                        WHERE
                            edate = 0
                        AND cart_id = '".$cart_id."'");
                    safeCheck($cartProducts);

                    $discountedProductsCount = 0;
                    /*foreach($cartProducts as $product) {
                        $eligibleToApplyCode = true;

                        if($product['product_price_discount'] > 0 && !$check["force_apply"]) {
                            $eligibleToApplyCode = false;
                        }

                        if($promoCodeToCategories) {
                            $productId = $product['product_id'];
                            $productCategories = $db->getAll("SELECT category_id FROM ".$product_to_category_table." WHERE product_id = $productId");safeCheck($productCategories);
                            $productCategories = array_column($productCategories, 'category_id');

                            if(!array_diff($productCategories, $promoCodeToCategories)) {
                                $eligibleToApplyCode = false;
                            }
                        }

                        if($promoCodeToBrands && !in_array($product['brand_id'], $promoCodeToBrands)) {
                            $eligibleToApplyCode = false;
                        }

                        //
                        if($promoCodeToCollections) {
                            $productId = $product['product_id'];
                            $baseProduct = $db->getRow("SELECT * FROM ".$products_table." WHERE id = $productId");safeCheck($baseProduct);

                            if($promoCodeToCollections && !in_array($baseProduct['collection_id'], $promoCodeToCollections)) {
                                $eligibleToApplyCode = false;
                            }
                        }

                        if($promoCodeToProducts && !in_array($product['product_id'], $promoCodeToProducts)) {
                            $eligibleToApplyCode = false;
                        }

                        if($eligibleToApplyCode) {
                            $discountedProductsCount++;
                            $total_amount_of_non_discounted_products += $product['product_price_total'];
                        }
                    }*/
                    foreach($cartProducts as $product) {
                        $eligibleToApplyCode = false;
                        /*$forceApply = true;
                        $inCategories = true;
                        $inBrands = true;
                        $inCollections = true;
                        $inProducts = true;*/

                        // If product has discount, and the force_apply property is not applied, skip
                        if($product['product_price_discount'] > 0 && $product['product_price_discount'] != $product['product_price'] && !$check["force_apply"]) {
                            /*var_dump($product['product_id']);
                            var_dump($product['product_price_discount']);*/
                            continue;
                        }

                        if($promoCodeToCategories) {
                            $productId = $product['product_id'];
                            $productCategories = $db->getAll("SELECT category_id FROM ".$product_to_category_table." WHERE product_id = $productId");safeCheck($productCategories);
                            $productCategories = array_column($productCategories, 'category_id');

                            if(!empty(array_intersect($productCategories, $promoCodeToCategories))) {
                                $eligibleToApplyCode = true;
                            }
                        }

                        if($promoCodeToBrands && in_array($product['brand_id'], $promoCodeToBrands)) {
                            $eligibleToApplyCode = true;
                        }


                        if($promoCodeToCollections) {
                            $productId = $product['product_id'];
                            $baseProduct = $db->getRow("SELECT * FROM ".$products_table." WHERE id = $productId");safeCheck($baseProduct);

                            if($promoCodeToCollections && in_array($baseProduct['collection_id'], $promoCodeToCollections)) {
                                $eligibleToApplyCode = true;
                            }
                        }

                        if($promoCodeToProducts && in_array($product['product_id'], $promoCodeToProducts)) {
                            $eligibleToApplyCode = true;
                        }

                        if (!$promoCodeToCategories && !$promoCodeToBrands && !$promoCodeToCollections && !$promoCodeToProducts) {
                            $eligibleToApplyCode = true;
                        }

                        // var_dump((!$promoCodeToCategories && !$promoCodeToBrands && !$promoCodeToCollections && !$promoCodeToProducts));
                        if($eligibleToApplyCode) {
                            $discountedProductsCount++;
                            $total_amount_of_non_discounted_products += $product['product_price_total'];
                        }
                    }


                    if (count($cartProducts) > $discountedProductsCount) {
                        $brand = $db->getRow("SELECT * FROM ".$brands_table." WHERE id =". $promoCodeToBrands[0]);safeCheck($brand);

                        if (is_array($brand)) {
                            $message = str_replace("{brand}", $brand['name_bg'], $languageVars["valid_promo_code_3"]);
                        } else {
                            $message = $languageVars["valid_promo_code_4"];
                        }

                        $message = str_replace("{value}", $value . $typeString, $message);
                        $result["message"] = $message;
                    }

                    if ($promoCodeToBrands && $discountedProductsCount == 0) {
                        $result["code"] = 403;

                        $brand = $db->getRow("SELECT * FROM ".$brands_table." WHERE id =" . $promoCodeToBrands[0]);safeCheck($brand);

                        if (is_array($brand)) {
                            $result["message"] = str_replace("{brand}", $brand['name_bg'], $languageVars["no_product_in_promo"]);
                        } else {
                            $result['message'] = $languageVars['no_product_in_promo_all'];
                        }
                    }

                    if($promo_code_discount_type == 1){
                        $total_amount_disconted = $total_amount - (($total_amount_of_non_discounted_products * $value) / 100);
                        $discount_amount = $total_amount_of_non_discounted_products * $value / 100;
                    }else if($promo_code_discount_type == 2){
                        $total_amount_of_discounted_products = $total_amount - $total_amount_of_non_discounted_products;
                        if($total_amount_of_non_discounted_products > 0 ) {
                            $total_amount_disconted = $total_amount_of_discounted_products + ($total_amount_of_non_discounted_products - $value);
                            $discount_amount = $value;
                        } else {
                            $total_amount_disconted = $total_amount;
                        }
                    }

                    $res = $db->autoExecute($carts_table, array("promo_code" => $code, "discount_promo_code_amount" => $discount_amount), DB_AUTOQUERY_UPDATE, "id = '".$cart_id."'"); safeCheck($res);

                } else {
                    $result["code"] = 402;
                    $result["message"] = $languageVars["invalid_promo_code_date"];

                    $res = $db->autoExecute($carts_table, array("promo_code" => null, "discount_promo_code_amount" => 0), DB_AUTOQUERY_UPDATE, "id = '".$cart_id."'"); safeCheck($res);
                }
            } else {
                $result["code"] = 401;
                $result["message"] = $languageVars["invalid_promo_code_amount"];

                $res = $db->autoExecute($carts_table, array("promo_code" => null, "discount_promo_code_amount" => 0), DB_AUTOQUERY_UPDATE, "id = '".$cart_id."'"); safeCheck($res);
            }
            $result["id"] = $id;
            $result["name"] = $check["name"];
            $result["value"] = $value;
            $result["discount_amount"] = $discount_amount;
            $result["total_amount_disconted"] = $total_amount_disconted;
            $result["promo_code_discount_type"] = $promo_code_discount_type;
            $result["order_amount_from"] = $order_amount_from;
            $result["order_amount_to"] = (double) $check["order_amount_to"];
            $result["valid_date_from"] = $valid_date_from;
            $result["valid_date_to"] = $check["valid_date_to"];
        } else {
            $result["code"] = 400;
            $result["message"] = $languageVars["wrong_promo_code"];

            $res = $db->autoExecute($carts_table, array("promo_code" => null, "discount_promo_code_amount" => 0), DB_AUTOQUERY_UPDATE, "id = '".$cart_id."'"); safeCheck($res);
        }

        if ($returnType === 3) {
            echo json_encode($result);
            die();
        }

        return $result;
    }

    public static function getPromoCodeProducts($promoCodeId) {
        global $db;
        global $product_to_promo_code_table;

        $productsSelected = $db->getAll("SELECT product_id FROM ".$product_to_promo_code_table." WHERE promo_code_id = $promoCodeId"); safeCheck($productsSelected);
        
        $products = [];
        foreach($productsSelected as $selectedProduct) {
            $products[] = $selectedProduct['product_id'];
        }

        return array_unique($products);
    }
    
    public static function getPromoCodeCollections($promoCodeId) {
        global $db;
        global $collection_to_promo_code_table;

        $collectionsSelected = $db->getAll("SELECT collection_id FROM ".$collection_to_promo_code_table." WHERE promo_code_id = $promoCodeId"); safeCheck($collectionsSelected);
        
        $collections = [];
        foreach($collectionsSelected as $selectedCollection) {
            $collections[] = $selectedCollection['collection_id'];
        }

        return array_unique($collections);
    }
    
    public static function getPromoCodeBrands($promoCodeId) {
        global $db;
        global $brand_to_promo_code_table;

        $selectedBrands = $db->getAll("SELECT brand_id FROM ".$brand_to_promo_code_table." WHERE promo_code_id = $promoCodeId"); safeCheck($brandsSelected);

        $brands = [];
        foreach($selectedBrands as $selectedBrand) {
            $brands[] = $selectedBrand['brand_id'];
        }

        return array_unique($brands);
    }
    
    public static function getPromoCodeCategories($promoCodeId) {
        global $db;
        global $category_to_promo_code_table;

        $selectedCategories = $db->getAll("SELECT category_id FROM ".$category_to_promo_code_table." WHERE promo_code_id = $promoCodeId"); safeCheck($selectedCategories);
        
        $categories = [];
        foreach($selectedCategories as $selectedCategory) {
            $categories[] = $selectedCategory['category_id'];
        }

        return array_unique($categories);
    }

}
