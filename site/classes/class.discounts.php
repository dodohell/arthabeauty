<?php
class Discounts extends Settings {
    public static function getRecord(int $id){
        global $db;
        global $discounts_table;
        
        $row = $db->getRow("SELECT * FROM ".$discounts_table." WHERE id = $id AND edate = 0"); safeCheck($row);
        
        return $row;
    }

    public static function getValidDiscounts($discount_date = ''){
        global $db;
        global $lng;
        global $discounts_table;

        $sql_where = '';

        if($name){
            $sql_where .= ' AND name_bg LIKE "%'.$name .'%"';
        }

        $sql_where .= " AND  UNIX_TIMESTAMP( STR_TO_DATE(discount_date_from, '%Y-%m-%d')) <=". strtotime($discount_date);
    
        $sql_where .= " AND  UNIX_TIMESTAMP( STR_TO_DATE(discount_date_to, '%Y-%m-%d')) >=". strtotime($discount_date);

        $sql = "SELECT id, name_{$lng} AS name, pos, active, discount_date_to
                              FROM " . $discounts_table . "
                              WHERE edate = 0 ". $sql_where ." AND active = 1 AND in_offers = 1
                              ORDER BY pos";
        
        $items = $db->getAll($sql); safeCheck($items);

        return $items;
    }

    public static function getDiscountProducts($discountId) {
        global $db;
        global $product_to_discount_table;

        $productsSelected = $db->getAll("SELECT product_id FROM ".$product_to_discount_table." WHERE discount_id = $discountId"); safeCheck($productsSelected);
        
        return $productsSelected;
    }
    
    public static function getDiscountCollections($discountId) {
        global $db;
        global $collection_to_discount_table;

        $collectionsSelected = $db->getAll("SELECT collection_id FROM ".$collection_to_discount_table." WHERE discount_id = $discountId"); safeCheck($collectionsSelected);

        return $collectionsSelected;
    }
    
    public static function getDiscountBrands($discountId) {
        global $db;
        global $brand_to_discount_table;

        $brandsSelected = $db->getAll("SELECT brand_id FROM ".$brand_to_discount_table." WHERE discount_id = $discountId"); safeCheck($brandsSelected);

        return $brandsSelected;
    }
    
    public static function getDiscountCategories($discountId) {
        global $db;
        global $category_to_discount_table;

        $selectedCategories = $db->getAll("SELECT category_id FROM ".$category_to_discount_table." WHERE discount_id = $discountId"); safeCheck($selectedCategories);
        
        return $selectedCategories;
    }
}