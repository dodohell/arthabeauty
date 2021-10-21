<?php
include("globals.php");
error_reporting(E_ALL & ~E_NOTICE);
$id = $params->getInt("id");
$cart_id = $params->getInt("cart_id");
$quantity = $params->getInt("quantity");
if ($id){
    addProductToCart($id, $cart_id, $params, $quantity);
    $row = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$cart_id."'"); safeCheck($row);
    $cartsObj = new Carts();
    $cartsObj->cartCalculateTotal($cart_id, $row["user_id"]);
    header("Location: carts_ae.php?act=edit&id=".$cart_id);
    die();
}
function addProductToCart($product_id, $cart_id, $params, $quantity = 1 ){
    global $db;
    global $lng;
    global $products_table;
    global $products_images_table;
    global $brands_table;
    global $options_table;
    global $variants_table;
    global $carts_products_table;
    global $allowZeroQuantityVariantOrder;
    $variantQuantitySQL = " AND quantity > 0 ";
    if($allowZeroQuantityVariantOrder){
        $variantQuantitySQL = "";
    }
    $option_id = $params->getInt("option_id");
    $sql_option = "";
    if ( $option_id ){
        $sql_option = " AND option_id = ".$option_id;
        $option = $db->getRow("SELECT * FROM ".$options_table." WHERE edate = 0 AND id = '".$option_id."'");
        safeCheck($option);
        //reduce variant quantity
        $res = $db->query("UPDATE {$variants_table} SET quantity = quantity - {$quantity} WHERE product_id = $product_id AND option_id = {$option_id} AND quantity > 0 AND edate = 0"); safeCheck($res);
    }
    //reduce product quantity
    $res = $db->query("UPDATE {$products_table} SET quantity = quantity - {$quantity} WHERE id = $product_id AND quantity > 0 AND edate = 0");
    safeCheck($res);
    $product = $db->getRow("SELECT products.*,
        products.name_{$lng} AS name,
        (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE products.brand_id = brands.id) AS brand_name,
        (SELECT images.id FROM ".$products_images_table." AS images WHERE images.product_id = products.id ORDER BY pos LIMIT 1) AS product_image_id
        FROM ".$products_table." AS products                                WHERE id = ".$product_id."
        ");
    safeCHeck($product);
    $check_product = $db->getRow("SELECT id, quantity, product_weight FROM ".$carts_products_table." WHERE product_id = ".$product_id." AND cart_id = ".$cart_id." {$sql_option} AND edate = 0"); safeCheck($check_product);
    $variant = $db->getRow("SELECT * FROM ".$variants_table." WHERE product_id = ".$product_id." AND option_id = '".$option_id."' AND edate = 0 {$variantQuantitySQL}");
    safeCheck($variant);
    $price_specialoffer = getSpecialOfferPrice($product["id"], $product["brand_id"], 1, $option_id);
    $helpers = new Helpers();
    $users = new Users();
    $cartsObj = new Carts();
    $cart = $cartsObj->getRecord($cart_id);
    $user_id = $cart["user_id"];
    $user = $user_id > 0 ? $users->getRecord($user_id) : array();
    $user_group_id = isset($user["user_group_id"]) && $user["user_group_id"] > 0 ? $user["user_group_id"] : 0;
    if ( $variant["price"] ){
        if ( $price_specialoffer["price_specialoffer"] > 0.0 ){
            $price_use = $helpers->getDiscountedPrice($variant["price"], 1, $user_group_id);
        }else{
            $price_use = $helpers->getDiscountedPrice($variant["price"], 0, $user_group_id);
        }
        $price_bonus_points_use = $variant["bonus_points"];
        $product_weight = $variant["weight"];
        $price_supply = $variant['price_supply'];
        $price_profit = $variant['price_profit'];
    }else{
        if ( $price_specialoffer["price_specialoffer"] > 0.0 ){
            $price_use = $helpers->getDiscountedPrice($product["price"], 1, $user_group_id);
        }else{
            $price_use = $helpers->getDiscountedPrice($product["price"], 0, $user_group_id);
        }
        $product_weight = $product["weight"];
        $price_supply = $product['price_supply'];
        $price_profit = $product['price_profit'];
    }
    $price_use_discounted = 0.0;
    if ( $price_specialoffer["price_specialoffer"] > 0.0 ){
        $price_use_discounted = $helpers->getDiscountedPrice($price_specialoffer["price_specialoffer"], 1, $user_group_id);
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
    );//
    if ( $_SERVER["REMOTE_ADDR"] == "84.201.192.58" ){}
    if ($check_product["quantity"] == 0){
        $res = $db->autoExecute($carts_products_table, $fields, DB_AUTOQUERY_INSERT );
        safeCheck($res);
    }else{
        $fields["quantity"] = $quantity + $check_product["quantity"];
        $fields["product_weight_total"] = $fields["quantity"] * $check_product["product_weight"];
        $res = $db->autoExecute($carts_products_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$check_product["id"]."'" ); safeCheck($res);
    }
    // print_r($res);
}
?>
