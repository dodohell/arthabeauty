<?php

class PromoCodes extends Settings {

    public $pagination = "";

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

    /**
     *
     * @global DB $db
     * @global type $promo_codes_table
     * @param FilteredMap $params
     */
    public static function addEditRow(FilteredMap $params) {
        global $db;
        global $promo_codes_table;
        global $brand_to_promo_code_table;
        global $category_to_promo_code_table;
        global $collection_to_promo_code_table;
        global $product_to_promo_code_table;

        $act = $params->getString("act");
        $id = $params->getInt("id");
        $promo_code_discount_type = $params->getInt("promo_code_discount_type");
        $valid_date_from = $params->getString("valid_date_from");
        $valid_date_to = $params->getString("valid_date_to");
        $fields = array(
            'code' => $params->getString("code"),
            'name_bg' => $params->getString("name_bg"),
            'name_en' => $params->getString("name_en"),
            'name_de' => $params->getString("name_de"),
            'name_ru' => $params->getString("name_ru"),
            'name_ro' => $params->getString("name_ro"),
            'promo_code_discount_type' => $promo_code_discount_type,
            'order_amount_from' => $params->getString("order_amount_from"),
            'order_amount_to' => $params->getString("order_amount_to"),
            'value' => $params->getNumber("value"),
            'active' => $params->getInt("active"),
            'is_one_time' => (!$params->getInt("is_one_time"))? '0' : $params->getInt("is_one_time"),
            'force_apply' => (!$params->getInt("force_apply"))? '0' : $params->getInt("force_apply"),
            'cms_user_id' => $params->getInt($_SESSION["uid"])
        );
        if($valid_date_from) {
            $fields['valid_date_from'] = $valid_date_from;
        }
        if($valid_date_to) {
            $fields['valid_date_to'] = $valid_date_to;
        }

        if ($act === "add") {
            $fields["postdate"] = time();
            shiftPos($db, $promo_codes_table);
            $db->autoExecute($promo_codes_table, $fields, DB_AUTOQUERY_INSERT);
            $id = mysqli_insert_id($db->connection);
        }

        if ($act === "edit") {
            $fields["updated_date"] = time();
            $sql = $db->autoExecute($promo_codes_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id);
        }

        if($productRequest = explode(',', $params->get("products") ) ){
            $del = $db->Query("DELETE FROM {$product_to_promo_code_table} WHERE promo_code_id = '{$id}'");
            foreach($productRequest as $v){
                $res = $db->Query("INSERT INTO ". $product_to_promo_code_table . " (product_id, promo_code_id) VALUES(" . $v . ", ".$id.")"); 
            }
        }

        if($collectionRequest = explode(',', $params->get("collections") ) ){
            $del = $db->Query("DELETE FROM {$collection_to_promo_code_table} WHERE promo_code_id = '{$id}'");
            foreach($collectionRequest as $v){
                $res = $db->Query("INSERT INTO ". $collection_to_promo_code_table . " (collection_id, promo_code_id) VALUES(" . $v . ", ".$id.")"); 
            }
        }

        if($brandRequest = explode(',', $params->get("brands") ) ){
            $del = $db->Query("DELETE FROM {$brand_to_promo_code_table} WHERE promo_code_id = '{$id}'");
            foreach($brandRequest as $v){
                $res = $db->Query("INSERT INTO ". $brand_to_promo_code_table . " (brand_id, promo_code_id) VALUES(" . $v . ", ".$id.")"); 
            }
        }

        $res = $db->Query("DELETE FROM ". $category_to_promo_code_table . " WHERE promo_code_id=".$id); safeCheck($res);
        $categoriesRequest = $params->get("categories");
        if ($categoriesRequest){
            foreach($categoriesRequest as $value){
                $res = $db->Query("INSERT INTO ". $category_to_promo_code_table . " (category_id, promo_code_id) VALUES(".$value.", ".$id.")"); safeCheck($res);
            }
        }

        return $id;
    }

    public static function getPromoCodes($name = '', $status = NULL, $validOnly = 0) {
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
        if ($validOnly === 1) {
            $now = time();
            $sql_where .= " AND {$now} BETWEEN valid_date_from AND valid_date_to";
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

    public static function deleteRecord(int $id) {
        global $db;
        global $promo_codes_table;

        $fields = array(
            "edate" => time(),
            "edate_cms_user_id" => $_SESSION["uid"]
        );
        $res = $db->autoExecute($promo_codes_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $id . "' "); safeCheck($res);

        return $res;
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

        $sm->configLoad($language_file);
        $languageVars = $sm->getConfigVars();

        $check = self::getActiveRecordByCode($code);
        $result = array();
        if ($check) {
            $order_amount_from = (double) $check["order_amount_from"]; //>= 0.0 ? (double)$check["order_amount_from"] : 0;
            $order_amount_to = (double) $check["order_amount_to"]; //>= 0.0 ? (double)$check["order_amount_to"] : 0;
            $valid_date_from = $check["valid_date_from"];
            $valid_date_to = $check["valid_date_to"];
            $promo_code_discount_type = (int) $check["promo_code_discount_type"];
            if ($total_amount >= $order_amount_from && $order_amount_to = $order_amount_to > 0.0 ? $total_amount <= $order_amount_to : true) {
                $now = date("Y-m-d");
                if ($now >= $valid_date_from && $valid_date_to = $valid_date_to > 0 ? $now <= $valid_date_to : true) {
                    $typeString = $promo_code_discount_type == 1 ? "%" : "лв";
                    $result["code"] = 200;
                    $result["message"] = $languageVars["valid_promo_code_1"] . " " . $check["value"] . $typeString . " " . $languageVars["valid_promo_code_2"];
                } else {
                    $result["code"] = 402;
                    $result["message"] = $languageVars["invalid_promo_code_date"];
                }
            } else {
                $result["code"] = 401;
                $result["message"] = $languageVars["invalid_promo_code_amount"];
            }
            $result["name"] = $check["name"];
            $result["value"] = $check["value"];
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

    public static function getPromoCodeBrands($promoCodeId) {
        global $db;
        global $lng;
        global $brands_table;
        global $brand_to_promo_code_table;

        $brands = $db->getAll("SELECT id, name_{$lng} AS name FROM ".$brands_table." WHERE edate = 0 ORDER BY name_{$lng} "); safeCheck($brands);
        if($promoCodeId){
            $brandsSelected = $db->getAll("SELECT brand_id FROM ".$brand_to_promo_code_table." WHERE promo_code_id = $promoCodeId"); safeCheck($brandsSelected);
            foreach($brands as $k=>$v){
                foreach($brandsSelected as $vv){
                    if ($v["id"] == $vv["brand_id"]){
                        $v["selected"] = "checked";
                    }
                }
                $brands[$k] = $v;
            }
        }
        return $brands;
    }

    public static function getPromoCodeCategories($promoCodeId) {
        global $db;
        global $lng;
        global $categories_table;
        global $category_to_promo_code_table;

        $items = $db->getAll("SELECT
                                id, 
                                name_{$lng} AS name
                            FROM 
                                $categories_table
                            WHERE 
                                edate = 0 
                            AND category_id = 0
                            ORDER BY pos"); safeCheck($items);

        foreach($items as $k=>$v){
            $v["submenus"] = getSubmenusCheckboxesPromoCode($v["id"], 1, $promoCodeId);
            $v["level"] = 1;
            $v["first"] = 0;
            $v["current"] = $k;
            
            if($promoCodeId){
                $selected = $db->getRow("SELECT count(*) AS cntr FROM ".$category_to_promo_code_table." WHERE promo_code_id = $promoCodeId AND category_id = ".$v["id"]); safeCheck($selected);
                if ($selected["cntr"]){
                    $v["selected"] = "checked";
                }
            }
            
            $v["last"] = sizeof($items)-1;
            $items[$k] = $v;
        }
        return $items;
    }

}
