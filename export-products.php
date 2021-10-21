<?php
	include("globals.php");

	$productsImport = $db->getAll("SELECT id, barcode, price_supply, price, 	price_profit, name_bg, name_en, excerpt_bg, description_bg, usage_bg, ingredients_bg, useful_bg  FROM `arthabeauty_products` WHERE `brand_id` = 5");
	echo "<pre>";
	foreach($productsImport as $k => $v){
		echo $v["id"]."|".$v["barcode"]."|".$v["price_supply"]."|".$v["price"]."|".$v["price_profit"]."|".$v["name_bg"]."|".$v["name_en"]."|".strip_tags($v["excerpt_bg"])."|".strip_tags($v["description_bg"])."|".strip_tags($v["usage_bg"])."|".strip_tags($v["ingredients_bg"])."|".strip_tags($v["useful_bg"])."|";
	}
	echo "</pre>";
?>