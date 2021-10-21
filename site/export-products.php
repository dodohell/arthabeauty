<?php

	include("globals.php");
	echo "<table><tr>";
	echo "<td>id</td><td>barcode</td><td>price_supply</td><td>price</td><td>price_profit</td><td>name_bg</td><td>name_en</td><td>excerpt_bg</td><td>description_bg</td><td>usage_bg</td><td>ingredients_bg</td><td>useful_bg</td><td>images</td>";
	echo "</tr>";

	$productsImport = $db->getAll("SELECT id, barcode, price_supply, price, 	price_profit, name_bg, name_en, excerpt_bg, description_bg, usage_bg, ingredients_bg, useful_bg  FROM `arthabeauty_products` WHERE `brand_id` = 5");
	foreach($productsImport as $k => $v){
		
		$images = $db->getAll("select * from 	arthabeauty_products_images where product_id = ".$v["id"]);
		
		echo 
		"<tr><td>".
		maybeEncodeCSVField($v["id"])."</td><td>".
		maybeEncodeCSVField($v["barcode"])."</td><td>".
		maybeEncodeCSVField($v["price_supply"])."</td><td>".
		maybeEncodeCSVField($v["price"])."</td><td>".
		maybeEncodeCSVField($v["price_profit"])."</td><td>".
		maybeEncodeCSVField($v["name_bg"])."</td><td>".
		maybeEncodeCSVField($v["name_en"])."</td><td>".
		maybeEncodeCSVField(strip_tags($v["excerpt_bg"]))."</td><td>".
		maybeEncodeCSVField(strip_tags($v["description_bg"]))."</td><td>".
		maybeEncodeCSVField(strip_tags($v["usage_bg"]))."</td><td>".
		maybeEncodeCSVField(strip_tags($v["ingredients_bg"]))."</td><td>".
		maybeEncodeCSVField(strip_tags($v["useful_bg"]))."</td><td>";
		foreach($images as $kk => $vv){
			echo "https://arthabeauty.com/files/".$vv["pic"]."\n";
		}
		echo "</td></tr>";
	}
	echo "</table>";

function maybeEncodeCSVField($string) {
	$string = str_replace("&nbsp;", " ", $string);
	if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
		$string = '"' . str_replace('"', '""', $string) . '"';
	}
	return $string;
}
?>