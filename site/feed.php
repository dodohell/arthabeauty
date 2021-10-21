<?php
    header("Content-type: text/csv charset=UTF-8");
    header("Content-Transfer-Encoding: UTF-8");
    header("Pragma: no-cache");
    header("Expires: 0");

	include("globalsCsv.php");
    echo "\xEF\xBB\xBF";
    $sm->configLoad($language_file);
    $configVars = $sm->getConfigVars();
             
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
                AND products.quantity > 0
                {$sql_order_by}
                ";


    $items = $db->getAll($sqlItems); safeCheck($items);


    $helpers = new Helpers();
    $user_group_id = Helpers::getCurentUserGroupId();

    foreach ($items as $k => $v) {
        $price_specialoffer = getSpecialOfferPrice($v["id"], $v["brand_id"], 1);

        if ( $price_specialoffer["price_specialoffer"] > 0.0 ){
            $v["price"] = $helpers->getDiscountedPrice($v["price"], 1, $user_group_id);
            $v["price_specialoffer"] = $helpers->getDiscountedPrice($price_specialoffer["price_specialoffer"], 1, $user_group_id);
            $v["price_specialoffer_text"] = $price_specialoffer["price_specialoffer_text"];
            $v["bonus_points"] = $price_specialoffer["bonus_points"];
            $v["bonus_points_win"] = round($price_specialoffer["price_specialoffer"] * 1, 0) ;
            $v["discount_date_to"] = $price_specialoffer["discount_date_to"];
        }else{
            $v["price"] = $helpers->getDiscountedPrice($v["price"], 0, $user_group_id);
            $v["bonus_points_win"] = round($v["price"] * 1, 0);
        }

        if ( $user["id"] ){
            $check = $db->getRow("SELECT * FROM ".$favourites_table." WHERE edate = 0 AND product_id = ".$v["id"]." AND user_id = ".$user["id"]); safeCheck($check);
            if ( $check["id"] ){
                $v["in_favourites"] = 1;
            }else{
                $v["in_favourites"] = 0;
            }
        }

        if(isset($v["option_groups"])) {
            $option_groups  = Products::getOptionGroups($v["id"]);
            $v["option_groups"] = $option_groups;
        }

        $items[$k] = $v;
    }


    echo "ID,Item title,Final URL,Image URL,Item description,Item category,Price\n";

    foreach($items as $k => $v){

        $category = $db->getRow("SELECT *, (SELECT categories.name_{$lng} AS name FROM ".$categories_table." AS categories WHERE ptc.category_id = categories.id) AS name FROM ".$product_to_category_table." AS ptc WHERE ptc.product_id = ".$v["id"]." AND category_id NOT IN (SELECT id from ".$categories_table." where edate = 0 and category_id = 0) ORDER BY ptc.category_id"); safeCheck($category);

        if ($category["category_id"]){
            $categoryMain = $db->getRow("SELECT id, name_{$lng} AS name FROM ".$categories_table." WHERE edate = 0 AND id = '".$category["category_id"]."'"); safeCheck($categoryMain);
        }

        echo "".maybeEncodeCSVField($v["id"]).",".maybeEncodeCSVField($v["name_en"].",".$v["name"]).",".substr($host,0,-1).
        ($v["htaccess_url"] ? $v["htaccess_url"]: maybeEncodeCSVField($htaccessVars["htaccess_product"]."/".$v["id"])).",".maybeEncodeCSVField($host."files/".$v["mainPic"]).",".maybeEncodeCSVField(mb_substr(strip_tags($v["excerpt"]), 0, 25)).",".maybeEncodeCSVField($categoryMain["name"]).",".maybeEncodeCSVField($v["price"])." BGN\n";
    }

function maybeEncodeCSVField($string) {
	$string = str_replace("&nbsp;", " ", $string);
	if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
		$string = '"' . str_replace('"', '""', $string) . '"';
	}
	return $string;
}
?>