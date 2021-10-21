<?php
include("globals.php");
$id = (int)$_REQUEST["id"];
$cart_id = (int)$_REQUEST["cart_id"];
$sm->assign("cart_id", $cart_id);
$sm->assign("id", $id);
$row = $db->getRow("
SELECT
products.*,
products.name_{$lng} AS name,
products.excerpt_{$lng} AS excerpt,
products.description_{$lng} AS description,
products.meta_title_{$lng} AS meta_title,
products.meta_keywords_{$lng} AS meta_keywords,
products.meta_description_{$lng} AS meta_description,
products.meta_metatags_{$lng} AS meta_metatags,
(SELECT product_types.name_{$lng} FROM ".$product_types_table." AS product_types WHERE products.product_type_id = product_types.id) AS product_type_name,
(SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_rating_table." AS rating WHERE rating.product_id = products.id) AS rating,
(SELECT COUNT(comments.id) FROM ".$products_comments_table." AS comments WHERE comments.product_id = products.id AND comments.edate = 0) AS comments
FROM ".$products_table." AS products
WHERE edate = 0
AND id = '{$id}'
");
safeCheck($row);
if (!$row["rating"]){
    $row["rating"] = 0;
}
//$vat = $db->getRow("SELECT * FROM ".$vat_table." WHERE id = 1"); safeCheck($vat);
$helpers = new Helpers();    $users = new Users();    $cartsObj = new Carts();
$cart = $cartsObj->getRecord($cart_id);
$user_id = $cart["user_id"];
$user = $user_id > 0 ? $users->getRecord($user_id) : array();
$user_group_id = isset($user["user_group_id"]) && $user["user_group_id"] > 0 ? $user["user_group_id"] : 0;
$price_specialoffer = getSpecialOfferPrice($row["id"], $row["brand_id"], 1);
if ( $price_specialoffer["price_specialoffer"] > 0.0 ){
    $row["price_specialoffer"] = $helpers->getDiscountedPrice($price_specialoffer["price_specialoffer"], 1, $user_group_id);
    $row["price_specialoffer_text"] = $price_specialoffer["price_specialoffer_text"];
    $row["bonus_points"] = $price_specialoffer["bonus_points"];
}else{
    $row["price"] = $helpers->getDiscountedPrice($row["price"], 0, $user_group_id);
}
$row["product_price_clear"] = round($row["product_price"],2);
$manifacturer = $db->getRow("SELECT id, name_{$lng} AS name FROM ".$manifacturers_table." WHERE edate = 0 AND id = '".$row["manifacturer_id"]."'");
safeCheck($manifacturer);	$sm->assign("manifacturer", $manifacturer);
$brand = $db->getRow("SELECT *, id, name_{$lng} AS name FROM ".$brands_table." WHERE edate = 0 AND id = '".$row["brand_id"]."'");
safeCheck($brand);	$sm->assign("brand", $brand);
$row["rating"] = round($row["rating"], 1);
if ( $row["enable_bonus_points"] ){
    if ( $row["bonus_points"] <= 0 ){
        $row["bonus_points"] = $row["price"] * $bonus_points_to_sell;
    }
}
$sm->assign("row", $row);
$category = $db->getRow("SELECT *, (SELECT categories.name_{$lng} AS name FROM ".$categories_table." AS categories WHERE ptc.category_id = categories.id) AS name_left FROM ".$product_to_category_table." AS ptc WHERE product_id = '".$id."' ORDER BY category_id ASC");
safeCheck($category);
$sm->assign("category",$category);
$option_groups = $db->getAll("SELECT DISTINCT option_group_id FROM ".$variants_table." WHERE product_id = '".$id."' AND edate = 0");
safeCheck($option_groups);
$option_groups_selected = $db->getAll("SELECT * FROM ".$variants_table." WHERE product_id = '".$id."' AND quantity > 0 AND edate = 0");
safeCheck($option_groups_selected);
foreach($option_groups as $k => $v){
    $row_tmp = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$option_groups_table." WHERE edate = 0 AND id = '".$v["option_group_id"]."' ");
    safeCheck($row_tmp);
    $v["info"] = $row_tmp;
    $options = $db->getAll("SELECT id, option_text AS name FROM ".$options_table." WHERE edate = 0 AND option_group_id = '".$v["option_group_id"]."' ORDER BY pos");
    safeCheck($options);
    $display_in_list = 0;
    foreach($options as $kk => $vv){
        foreach($option_groups_selected as $kkk => $vvv){
            if ( $vv["id"] == $vvv["option_id"] ){
                $vv["selected_values"] = $vvv;
                $vv["checked"] = "checked";
                $display_in_list = 1;
            }
        }
        $options[$kk] = $vv;
    }
    $v["display_in_list"] = $display_in_list;
    $v["options"] = $options;
    $option_groups[$k] = $v;
}
$sm->assign("option_groups", $option_groups);
$sm->display("admin/getProduct.html");
?>
